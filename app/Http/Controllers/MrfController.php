<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MrfForm;
use App\Mail\MailNotify;
use App\Mail\MrfNotify;
use App\Mail\MRFNotifyApproved;
use App\Models\MrfItems;
use Illuminate\Http\Request;
use Illuminate\Mail\Attachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PDF;
class MrfController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $authUserId = auth()->user()->id;

        $data = MrfForm::query()->select(
            'mrf_form.id',
            'tbl_sites.site_name',
            'mrf_form.mrf_order_number',
            'mrf_form.date_requested',
            'mrf_form.date_needed',
            'mrf_form.status',
            'mrf_form.created_by',
            'mrf_form.created_at',
            'users.first_name',
            'users.last_name')
            ->join('tbl_sites', 'tbl_sites.id', '=', 'mrf_form.site_id')
            ->join('users', 'users.id', '=', 'mrf_form.created_by')
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('tbl_sites.site_name', 'like', "%{$searchQuery}%");
            })
            ->where(function ($query) use ($authUserId) {
                $query->where('mrf_form.created_by', $authUserId)
                    ->orWhere('users.sitehead_user_id', $authUserId); // Include sitehead_user_id condition
            })
            ->orderBy('mrf_form.created_at', 'desc')
            ->paginate(setting('pagination_limit'))
            ->through(function ($item) {
                // Check if created_at is not null before formatting
                $createdAtFormatted = $item->created_at ? $item->created_at->format('Y-m-d h:i A') : null;
                $user = User::find($item->created_by);
                return [
                    'id' => $item->id,
                    'site_name' => $item->site_name,
                    'mrf_order_number' => $item->mrf_order_number,
                    'requisitioner' => $item->requisitioner,
                    'date_requested' => $item->date_requested,
                    'date_needed' => $item->date_needed,
                    'status' => $item->status,
                    'created_user' => $item->first_name . ' ' . $item->last_name,
                    'created_at' => $createdAtFormatted,
                ];
            });

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data for job_maintenances
        $validatedData = $request->validate([
            'mrf_order_number' => 'nullable|string|max:255',
            'site_id' => 'required|integer',
            'date_requested' => 'nullable|date',
            'date_needed' => 'nullable|date',
            'purpose' => 'nullable|string',
            'status' => 'required|string|in:P,A,C',
            'created_by' => 'nullable|integer',
            'updated_by' => 'nullable|integer',
        ]);

        // Validate the replacement parts data
        $validatedPartsData = $request->validate([
            'mrf_items_parts' => 'nullable|array',
            'mrf_items_parts.*.particulars' => 'nullable|string|max:20',
            'mrf_items_parts.*.description' => 'nullable|string|max:255',
            'mrf_items_parts.*.uom' => 'nullable|string|max:20',
            'mrf_items_parts.*.quantity' => 'nullable|integer|min:1',
            'mrf_items_parts.*.unit_price' => 'nullable|integer|min:1',
        ]);

        // Generate mrf_order_number if not provided
        if (empty($validatedData['mrf_order_number'])) {
            $lastJob = MrfForm::orderBy('id', 'desc')->first();
            $lastNumber = $lastJob && preg_match('/\d+$/', $lastJob->mrf_order_number)
                          ? intval(substr($lastJob->mrf_order_number, 8))
                          : 0;
            $validatedData['mrf_order_number'] = 'SLI-MRF-' . str_pad($lastNumber + 1, 8, '0', STR_PAD_LEFT);
        }

        // Use transaction to ensure both tables are updated atomically
        try {
            DB::beginTransaction();

            // Insert or update the job_maintenances table
            $MrfForm = MrfForm::updateOrCreate(
                ['id' => $request->id], // Update if ID exists, otherwise create
                [
                    'mrf_order_number' => $validatedData['mrf_order_number'],
                    'site_id' => $validatedData['site_id'],
                    'date_requested' => $validatedData['date_requested'] ?? null,
                    'date_needed' => $validatedData['date_needed'] ?? null,
                    'purpose' => $validatedData['purpose'] ?? null,
                    'status' => $validatedData['status'],
                    'created_by' => $validatedData['created_by'] ?? auth()->id(),
                    'updated_by' => 0
                ]
            );

            // If mrf item are provided, sync them to the mrfitem table
            if (!empty($validatedPartsData['mrf_items_parts'])) {
                // Delete existing replacement parts for this job (optional: adjust if partial updates are needed)
                MrfItems::where('mrf_form_id', $MrfForm->id)->delete();

                // Insert new replacement parts
                foreach ($validatedPartsData['mrf_items_parts'] as $part) {
                    MrfItems::create([
                        'mrf_form_id' => $MrfForm->id,
                        'particulars' => $part['particulars'] ?? null,
                        'description' => $part['description'] ?? null,
                        'quantity' => $part['quantity'] ?? null,
                        'uom' => $part['uom'] ?? null,
                        'unit_price' => $part['unit_price'] ?? null,
                    ]);
                }
            }

            $authUserId = auth()->user()->id;
            $creatorEmail = User::where('id',  $authUserId)->pluck('email')->first();
            $departmentHeadId = User::where('id',  auth()->user()->id)->pluck('sitehead_user_id')->first();
            $siteHeadEmail = User::where('id', $departmentHeadId)->pluck('email')->first();


            // Prepare data for the email
            $emailRequest = [
                'mrf_order_number' => $MrfForm->mrf_order_number,
                'site_id' => $MrfForm->site_id,
                'date_requested' => $MrfForm->date_requested,
                'date_needed' => $MrfForm->date_needed,
                'purpose' => $MrfForm->purpose,
                'status' => $MrfForm->status,
                'created' => User::where('id', $MrfForm->created_by)
                    ->selectRaw('CONCAT(first_name, " ", last_name) as full_name')
                    ->pluck('full_name')
                    ->first(),
                'departmenthead' => User::where('id', $departmentHeadId)
                    ->selectRaw('CONCAT(first_name, " ", last_name) as full_name')
                    ->pluck('full_name')
                    ->first()
            ];

            // Generate dynamic subject
            $subject = "New MRF Request No " . $validatedData['mrf_order_number'];

            // Prepare emails
            $toEmails = implode(',', array_filter([$siteHeadEmail]));
            $ccEmails = implode(',', array_filter([$creatorEmail]));

            // Send email
            Mail::to($toEmails)
                ->cc($ccEmails)
                ->send(new MrfNotify($emailRequest, $subject));


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Record saved successfully.',
                'data' => $MrfForm->load('mrf_items_parts'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the record.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            // Retrieve the job maintenance record by job order number
            $user = MrfForm::where('mrf_order_number', $id)->firstOrFail();

            // Retrieve the user who created the record
            $createdByUser = DB::table('users')
                ->select(DB::raw("CONCAT(first_name, ' ', last_name) AS cfull_name"), 'position AS cposition')
                ->where('id', $user->created_by)
                ->first();

            // Retrieve the user who updated the record
            $updatedByUser = DB::table('users')
                ->select(DB::raw("CONCAT(first_name, ' ', last_name) AS ufull_name"), 'position AS uposition')
                ->where('id', $user->updated_by)
                ->first();

            $updatedByUser = $updatedByUser ? $updatedByUser : (object)[
                'ufull_name' => '',
                'uposition' => ''
            ];
            // Retrieve the job maintenance record by ID and load the replacement parts and join with tbl_site
            $data = MrfForm::with('mrf_items_parts')
                ->join('tbl_sites', 'tbl_sites.id', '=', 'mrf_form.site_id')
                ->select('mrf_form.*', 'tbl_sites.site_name')
                ->findOrFail($user->id);
            // Add the user information to the data object
            $data->createdby = $createdByUser;
            $data->updatedby = $updatedByUser;



            return response()->json([
                'success' => true,
                'record' => $data,


            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->json(['success' => 200]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
{
    $data = MrfForm::find($id);

    if (!$data) {
        return response()->json(['errors' => 'Record not found'], 404);
    }

    // Update status and save
    $data->updated_by =auth()->user()->id;
    $data->status = $request->status;
    $data->save();

    // Prepare email data
    $sitename = DB::table('tbl_sites')->where('id', $data->site_id)->value('site_name');
    $authUserId = auth()->user()->id;
    $creatorEmail = User::where('id', $authUserId)->value('email');
    $departmentHeadId = User::where('id', $authUserId)->value('sitehead_user_id');
    $dapartmentPostion = User::where('id', $authUserId)->value('position');
    $siteHeadEmail = User::where('id', $departmentHeadId)->value('email');

    // Prepare the job request data
    $jobRequest = [
        'mrf_order_number' => $data->mrf_order_number,
        'site_name' => $sitename,
        'date_requested' => $data->date_requested,
        'date_needed' => $data->date_needed,
        'purpose' => $data->purpose,
        'status' => $data->status,
        'createdby' => User::where('id', $data->created_by)
            ->selectRaw('CONCAT(first_name, " ", last_name) as full_name')
            ->value('full_name'),
        'createdbyPosition' => User::where('id', $data->position)
            ->selectRaw('CONCAT(first_name, " ", last_name) as full_name')
            ->value('full_name'),
        'departmenthead' => User::where('id', $departmentHeadId)
            ->selectRaw('CONCAT(first_name, " ", last_name) as full_name')
            ->value('full_name'),
        'departmentheadPosition' => $dapartmentPostion,
        'materials' => $data->materials // Assuming materials is a relationship or data field
    ];

    // Generate dynamic subject
    $subject = "MRF Request " . ($data->status === 'C' ? 'Rejected' : 'Approved') . " # " . $data->mrf_order_number;

    // Generate PDF and prepare email
    $pdf = PDF::loadView('emails.mrf_approved', ['jobRequest' => $jobRequest]);
    Mail::send('emails.mrf_approved', ['jobRequest' => $jobRequest], function ($message) use ($creatorEmail, $siteHeadEmail, $subject, $pdf) {
        $message->to([$creatorEmail])
            ->subject($subject)
            ->attachData($pdf->output(), "mrf_details.pdf");
    });

    return response()->json(['success' => true], 200);
}



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = MrfForm::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['success' => 200, 'message' => 'Record Successfully Reject!']);
        }

        return response()->json(['errors' => 'Record not found'], 404);
    }
}
