<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobMaintenance;
use App\Models\JobReplacementPart;
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
            ->orderBy('job_maintenances.created_at','desc')
            ->paginate(setting('pagination_limit'))
            ->through(function ($item) {
              

              
                // Check if created_at is not null before formatting
                $createdAtFormatted = $item->created_at ? $item->created_at->format('Y-m-d h:i A') : null;

                return [
                    'id' => $item->id,
                    'site_name' => $item->site_name,
                    'job_order_number' => $item->job_order_number,
                    'end_user' => $item->end_user,
                    'time_requested' => $item->time_requested,
                    'date_needed' => $item->date_needed,
                    'commitment_date' => $item->commitment_date,
                    'status' => $item->status,
                    'created_by' => $item->created_by,
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
    dd ($request->all());
    //Validate the incoming request data for job_maintenances
    $request = $request->validate([
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
        'status' => 'required|integer',
        'created_by' => 'nullable|integer',
        'updated_by' => 'nullable|integer',
    ]);

//    // Validate the replacement parts data
//     $validatedPartsData = $request->validate([
//         'replacement_parts' => 'nullable|array',
//         'replacement_parts.*.description' => 'nullable|string|max:255',
//         'replacement_parts.*.part_number' => 'nullable|string|max:255',
//         'replacement_parts.*.quantity' => 'nullable|integer|min:1',
//     ]);

    //make autogenerate
    if(empty($request['job_order_number'])){
        $lastJob = JobMaintenance::orderBy('id','desc')->first();
        $lastnumber = $lastJob ? intval(substr($lastJob->job_order_number,3)) : 0;
        $request['job_order_number'] = 'SLI' . str_pad($lastnumber + 1,8,'0', STR_PAD_LEFT);    
    }

    // Use transaction to ensure both tables are updated atomically
    try {
        DB::beginTransaction();

        // Insert or update the job_maintenances table
        $jobMaintenance = JobMaintenance::updateOrCreate(
            ['id' => $request->id], // Update if ID exists, otherwise create
            [
                'job_order_number' => $request['job_order_number'] ?? null,
                'site_id' => $request['site_id'],
                'end_user' => $request['end_user'] ?? null,
                'time_requested' => $request['time_requested'] ?? null,
                'date_needed' => $request['date_needed'] ?? null,
                'noted_by' => $request['noted_by'] ?? null,
                'type_of_job' => $request['type_of_job'] ?? null,
                'problem_description' => $request['problem_description'] ?? null,
                'findings_recommendations' => $request['findings_recommendations'] ?? null,
                'commitment_date' => $request['commitment_date'] ?? null,
                'status' => $request['status'],
                'created_by' => $request['created_by'] ?? auth()->id(),
                'updated_by' => $request['updated_by'] ?? auth()->id(),
            ]
        );

    //    // If replacement parts are provided, sync them to the job_replacement_parts table
    //     if (!empty($validatedPartsData['replacement_parts'])) {
    //         // Delete existing replacement parts for this job (optional: adjust if partial updates are needed)
    //         JobReplacementPart::where('job_order_request_id', $jobMaintenance->id)->delete();

    //         // Insert new replacement parts
    //         foreach ($validatedPartsData['replacement_parts'] as $part) {
    //             JobReplacementPart::create([
    //                 'job_order_request_id' => $jobMaintenance->id,
    //                 'description' => $part['description'] ?? null,
    //                 'part_number' => $part['part_number'] ?? null,
    //                 'quantity' => $part['quantity'] ?? null,
    //             ]);
    //         }
    //     }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Record saved successfully.',
            //'data' => $jobMaintenance->load('replacementParts'), // Eager load replacement parts if needed
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
        //
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
    public function destroy(Request $request)
{
    // Validate the incoming request to ensure the ID is provided
    $validatedData = $request->validate([
        'id' => 'required|integer|exists:job_maintenances,id',
    ]);

    try {
        // Find the job maintenance record
        $jobMaintenance = JobMaintenance::findOrFail($validatedData['id']);

        // Update the status to 'X' (soft delete)
        $jobMaintenance->update([
            'status' => 'X',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Record Successfully Closed!',
            'data' => $jobMaintenance,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while marking the record as deleted.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

}
