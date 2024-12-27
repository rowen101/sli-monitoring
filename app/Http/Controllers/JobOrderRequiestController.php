<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\MailNotify;
use App\Models\tbl_site;
use Illuminate\Http\Request;
use App\Models\JobMaintenance;
use App\Models\JobReplacementPart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class JobOrderRequiestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $authUserId = auth()->user()->id;

        $data = JobMaintenance::query()->select(
            'job_maintenances.id',
            'tbl_sites.site_name',
            'job_maintenances.job_order_number',
            'job_maintenances.end_user',
            'job_maintenances.time_requested',
            'job_maintenances.date_needed',
            'job_maintenances.commitment_date',
            'job_maintenances.status',
            'job_maintenances.created_by',
            'job_maintenances.created_at',
            'users.first_name',
            'users.last_name')
            ->join('tbl_sites', 'tbl_sites.id', '=', 'job_maintenances.site_id')
            ->join('users', 'users.id', '=', 'job_maintenances.created_by')
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('tbl_sites.site_name', 'like', "%{$searchQuery}%");
            })
            ->where(function ($query) use ($authUserId) {
                $query->where('job_maintenances.created_by', $authUserId)
                    ->orWhere('users.sitehead_user_id', $authUserId); // Include sitehead_user_id condition
            })
            ->orderBy('job_maintenances.created_at', 'desc')
            ->paginate(setting('pagination_limit'))
            ->through(function ($item) {
                // Check if created_at is not null before formatting
                $createdAtFormatted = $item->created_at ? $item->created_at->format('Y-m-d h:i A') : null;
                $user = User::find($item->created_by);
                return [
                    'id' => $item->id,
                    'site_name' => $item->site_name,
                    'job_order_number' => $item->job_order_number,
                    'end_user' => $item->end_user,
                    'time_requested' => $item->time_requested,
                    'date_needed' => $item->date_needed,
                    'commitment_date' => $item->commitment_date,
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
        $validatedJobData = $request->validate([
            'job_order_number' => 'nullable|string|max:255',
            'site_id' => 'required|integer',
            'end_user' => 'nullable|string|max:255',
            'time_requested' => 'nullable|date',
            'date_needed' => 'nullable|date',
            'noted_by' => 'nullable|string|max:255',
            'type_of_job' => 'nullable|string|in:Preventive Maintenance,Corrective Maintenance,Calibration',
            'problem_description' => 'nullable|string',
            'findings_recommendations' => 'nullable|string',
            'commitment_date' => 'nullable|date',
            'status' => 'required|string|in:P,A,C',
            'created_by' => 'nullable|integer',
            'updated_by' => 'nullable|integer',
        ]);

        // Validate the replacement parts data
        $validatedPartsData = $request->validate([
            'replacement_parts' => 'nullable|array',
            'replacement_parts.*.description' => 'nullable|string|max:255',
            'replacement_parts.*.part_number' => 'nullable|string|max:255',
            'replacement_parts.*.quantity' => 'nullable|integer|min:1',
        ]);

        // Generate job_order_number if not provided
        if (empty($validatedJobData['job_order_number'])) {
            $lastJob = JobMaintenance::orderBy('id', 'desc')->first();
            $lastNumber = $lastJob ? intval(substr($lastJob->job_order_number, 3)) : 0;
            $validatedJobData['job_order_number'] = 'SLI' . str_pad($lastNumber + 1, 8, '0', STR_PAD_LEFT);
        }

        // Use transaction to ensure both tables are updated atomically
        try {
            DB::beginTransaction();

            // Insert or update the job_maintenances table
            $jobMaintenance = JobMaintenance::updateOrCreate(
                ['id' => $request->id], // Update if ID exists, otherwise create
                [
                    'job_order_number' => $validatedJobData['job_order_number'],
                    'site_id' => $validatedJobData['site_id'],
                    'end_user' => $validatedJobData['end_user'] ?? null,
                    'time_requested' => $validatedJobData['time_requested'] ?? null,
                    'date_needed' => $validatedJobData['date_needed'] ?? null,
                    'noted_by' => $validatedJobData['noted_by'] ?? null,
                    'type_of_job' => $validatedJobData['type_of_job'] ?? null,
                    'problem_description' => $validatedJobData['problem_description'] ?? null,
                    'findings_recommendations' => $validatedJobData['findings_recommendations'] ?? null,
                    'commitment_date' => $validatedJobData['commitment_date'] ?? null,
                    'status' => $validatedJobData['status'],
                    'created_by' => $validatedJobData['created_by'] ?? auth()->id(),
                    'updated_by' => 0
                ]
            );

            // If replacement parts are provided, sync them to the job_replacement_parts table
            if (!empty($validatedPartsData['replacement_parts'])) {
                // Delete existing replacement parts for this job (optional: adjust if partial updates are needed)
                JobReplacementPart::where('job_order_request_id', $jobMaintenance->id)->delete();

                // Insert new replacement parts
                foreach ($validatedPartsData['replacement_parts'] as $part) {
                    JobReplacementPart::create([
                        'job_order_request_id' => $jobMaintenance->id,
                        'description' => $part['description'] ?? null,
                        'part_number' => $part['part_number'] ?? null,
                        'quantity' => $part['quantity'] ?? null,
                    ]);
                }
            }


            $authUserId = auth()->user()->id;
            $creatorEmail = User::where('id',  $authUserId)->pluck('email')->first();
            $departmentHeadId = User::where('id',  auth()->user()->id)->pluck('sitehead_user_id')->first();
            $siteHeadEmail = User::where('id', $departmentHeadId)->pluck('email')->first();


            // Prepare data for the email
            $jobRequest = [
                'job_order_number' => $jobMaintenance->job_order_number,
                'site_id' => $jobMaintenance->site_id,
                'end_user' => $jobMaintenance->end_user,
                'time_requested' => $jobMaintenance->time_requested,
                'date_needed' => $jobMaintenance->date_needed,
                'type_of_job' => $jobMaintenance->type_of_job,
                'problem_description' => $jobMaintenance->problem_description,
                'status' => $jobMaintenance->status,
                'created' => User::where('id', $jobMaintenance->created_by)
                    ->selectRaw('CONCAT(first_name, " ", last_name) as full_name')
                    ->pluck('full_name')
                    ->first(),
                'departmenthead' => User::where('id', $departmentHeadId)
                    ->selectRaw('CONCAT(first_name, " ", last_name) as full_name')
                    ->pluck('full_name')
                    ->first()
            ];

            // Generate dynamic subject
            $subject = "New Job Order Request for " . $validatedJobData['type_of_job'] . " #" . $validatedJobData['job_order_number'];

            // Prepare emails
            $toEmails = implode(',', array_filter([$siteHeadEmail]));
            $ccEmails = implode(',', array_filter([$creatorEmail]));

            // $toEmails = 'rgrowengonzales66@gmail.com';
            // $ccEmails = 'stephandren035@gmail.com';
            // Send email
            Mail::to($toEmails)
                ->cc($ccEmails)
                ->send(new MailNotify($jobRequest, $subject));


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Record saved successfully.',
                'data' => $jobMaintenance->load('replacement_parts'),
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
            $user = JobMaintenance::where('job_order_number', $id)->firstOrFail();

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
            $data = JobMaintenance::with('replacement_parts')
                ->join('tbl_sites', 'tbl_sites.id', '=', 'job_maintenances.site_id')
                ->select('job_maintenances.*', 'tbl_sites.site_name')
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
        $data = JobMaintenance::find($id);
        return response()->json($data);
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
        $data = JobMaintenance::find($id);

        if ($data) {
            // Update status and save
            $data->status = $request->status;
            $data->save();

            // Prepare email data
            $authUserId = auth()->user()->id;
            $creatorEmail = User::where('id', $authUserId)->pluck('email')->first();
            $departmentHeadId = User::where('id', $authUserId)->pluck('sitehead_user_id')->first();
            $siteHeadEmail = User::where('id', $departmentHeadId)->pluck('email')->first();

            // Prepare data for the email
            $jobRequest = [
                'job_order_number' => $data->job_order_number,
                'site_id' => $data->site_id,
                'end_user' => $data->end_user,
                'time_requested' => $data->time_requested,
                'date_needed' => $data->date_needed,
                'type_of_job' => $data->type_of_job,
                'problem_description' => $data->problem_description,
                'status' => $data->status,
                'created' => User::where('id', $data->created_by)
                    ->selectRaw('CONCAT(first_name, " ", last_name) as full_name')
                    ->pluck('full_name')
                    ->first(),
                'departmenthead' => User::where('id', $departmentHeadId)
                    ->selectRaw('CONCAT(first_name, " ", last_name) as full_name')
                    ->pluck('full_name')
                    ->first()
            ];

            // Generate dynamic subject
            $subject = "Job Order Request " . ($data->status === 'C' ? 'Rejected' : 'Approved') . " for " . $data->type_of_job . " #" . $data->job_order_number;

            // Prepare emails
            $toEmails = implode(',', array_filter([$creatorEmail]));


            // Send email
            Mail::to($toEmails)
                ->send(new MailNotify($jobRequest, $subject));

            return response()->json(['success' => 200]);
        }

        return response()->json(['errors' => 'Record not found'], 404);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $data = JobMaintenance::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['success' => 200, 'message' => 'Record Successfully Delete!']);
        }

        return response()->json(['errors' => 'Record not found'], 404);
    }

}
