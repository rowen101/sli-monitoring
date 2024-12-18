<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\JobMaintenance;
use App\Models\JobReplacementPart;
use App\Models\tbl_site;
use Illuminate\Support\Facades\DB;

class JobOrderRequiestController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = JobMaintenance::query()
            ->join('tbl_sites','tbl_sites.id','=','job_maintenances.site_id')
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('tb_sites.site_name', 'like', "%{$searchQuery}%");
            })
            ->where('job_maintenances.created_by', auth()->user()->id)
            ->orderBy('job_maintenances.created_at','desc')
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
                    'created_user' => $user ? $user->first_name . ' ' . $user->last_name : null,
                   'created_at' => $createdAtFormatted,
                ];
            });

        return $data;
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
                    'created_by' => $validatedJobData['created_by'] ?? auth()->id()

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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Record saved successfully.',
                'data' => $jobMaintenance->load('replacementParts'), // Eager load replacement parts if needed
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
        return $data;
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
        //
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
            $data->status = 'C';
            $data->save();
            return response()->json(['success' => 200, 'message' => 'Record Successfully Closed!']);
        }

        return response()->json(['errors' => 'Record not found'], 404);
    }




}
