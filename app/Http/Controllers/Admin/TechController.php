<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\TechRecomm;
use Illuminate\Http\Request;
use App\Enums\TechRecomStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TechController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = TechRecomm::query()
        ->when(request('query'), function ($query, $searchQuery) {
            $query->where('user', 'like', "%{$searchQuery}%");
            $query->where('status', TechRecomStatus::from(request('status')));
        })
        ->latest()
        ->paginate(setting('pagination_limit'))
        ->through(function ($item){
            $user = User::find($item->created_by);
        return [
            'id' => $item->id,
            'recommnum'=> $item->recommnum,
            'user' => $item->user,
            'created_user' => $user ? $user->first_name . ' ' . $user->last_name : null,
            'model' => $item->model,
            'assettag' => $item->assettag,
            'serialnum'=> $item->serialnum,
            'problem' => $item->problem,
            'assconducted' => $item->assconducted,
            'recommendation' => $item->recommendation,
            'branch' => $item->branch,
            'department' => $item->department,
            'created_by' => $item->created_by,
            'created_at' => $item->created_at->format('Y-m-d h:i A'),
            'statusid' => $item->status,
            'status' => [
                'name' => $item->status->name,
                'color' => $item->status->color(),
        ]
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
    public function store()
    {
        request()->validate([
            'branch' => 'required',
            'department' => 'required',
            'user' => 'required',
            'problem' => 'required',
            'assconducted' => 'required',
        ]);

        // Find the latest TECH recommendation
        $latestRecommendation = TechRecomm::latest('id')->first();

        // Extract the last recommendation number if available
        $lastRecommNum = optional($latestRecommendation)->recommnum;

        // Increment the recommendation number
        $recommnum = 'TECH' . str_pad(
            (intval(substr($lastRecommNum, 4)) + 1),
            strlen($lastRecommNum) - 4,
            '0',
            STR_PAD_LEFT
        );

        return TechRecomm::create([
            'recommnum' => $recommnum,
            'company' => 'Safexpress Logistics Corp.',
            'branch' => request('branch'),
            'department' => request('department'),
            'warehouse' => request('warehouse'),
            'user' => request('user'),
            'problem' => request('problem'),
            'brand' => request('brand'),
            'model' => request('model'),
            'assettag' => request('assettag'),
            'serialnum' => request('serialnum'),
            'assconducted' => request('assconducted'),
            'recommendation' => request('recommendation'),
            'status' => 1,
            'created_by' => request('created_by'),
            'updated_by' => 0,

        ]);
    }

    public function getAction(Request $request)
    {

        $dateTime = Carbon::now();
        $userId = auth()->user()->id;
        TechRecomm::updateOrCreate(
            [
                'id' => $request->id,

            ],
            [

                'status' => $request->status,
                'updated_by' => $userId,
                'updated_at' => $dateTime

            ]
        );

        return response()->json(['message' => 'success']);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = TechRecomm::where('recommnum', $id)->first();

        // // Retrieve the user who created the TechRecomm record
        $createdByUser = DB::table('users')
        ->select(DB::raw("CONCAT(first_name, ' ', last_name) AS cfull_name"), 'position AS cposition')
        ->where('id', $data->created_by)
        ->first();

        $updatedByUser = DB::table('users')
        ->select(DB::raw("CONCAT(first_name, ' ', last_name) AS ufull_name"), 'position AS uposition')
        ->where('id', $data->updated_by)
        ->first();


        // // Add the user information to the data object
        $data->createdby = $createdByUser;
        $data->updatedby = $updatedByUser;

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TechRecomm $tech)
    {
        request()->validate([
            'department' => 'required',
            'user' => 'required',
            'problem' => 'required',
            'assconducted' => 'required',

        ]);

        $tech->update([
            'company' => 'Safexpress Logistics Corp.',
            'branch' => request('branch'),
            'department' => request('department'),
            'warehouse' => request('warehouse'),
            'user' => request('user'),
            'problem' => request('problem'),
            'brand' => request('brand'),
            'model' => request('model'),
            'assettag' => request('assettag'),
            'serialnum' => request('serialnum'),
            'assconducted' => request('assconducted'),
            'recommendation' => request('recommendation'),
            'status' => 1,
            'created_by' => request('created_by'),
            'updated_by' => 0,

        ]);

        return $tech;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $data = TechRecomm::find($id);
        $data->delete();
        return response()->json(['success' => 200]);
    }

    public function bulkDelete()
    {
        TechRecomm::whereIn('id', request('ids'))->delete();

        return response()->json(['message' => 'Data deleted successfully!']);
    }
}
