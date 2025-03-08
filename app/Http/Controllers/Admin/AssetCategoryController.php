<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = AssetCategory::query()
        ->when(request('query'), function ($query, $searchQuery) {
            $query->where('user', 'like', "%{$searchQuery}%");
            $query->where('status', TechRecomStatus::from(request('status')));
        })
        ->latest()
        ->paginate(setting('pagination_limit'))
        ->through(function ($item) {
            $user = User::find($item->created_by);
            return [
                'id' => $item->id,
                'recommnum' => $item->recommnum,
                'user' => $item->user,
                'created_user' => $user ? $user->first_name . ' ' . $user->last_name : null,
                'model' => $item->model,
                'assettag' => $item->assettag,
                'serialnum' => $item->serialnum,
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
        request()->validate([
            'name' => 'required',
            'description' => 'required',
        ]);


        $latestRecord = AssetCategory::latest('id')->first();


        $lastRecommNum = optional($latestRecord)->recommnum;


        $recommnum = 'CAT' . str_pad(
            (intval(substr($lastRecommNum, 4)) + 1),
            strlen($lastRecommNum) - 4,
            '0',
            STR_PAD_LEFT
        );

        $assetItem = AssetCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'created_by' => auth()->user()->id,

        ]);

        return response()->json($assetItem, 201);
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
        //
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
    public function destroy($id)
    {
        //
    }
}
