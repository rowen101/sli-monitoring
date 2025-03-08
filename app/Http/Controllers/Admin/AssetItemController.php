<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\AssetItem;
use Illuminate\Http\Request;

class AssetItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = AssetItem::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%") and $query->where('description', 'like', "%{$searchQuery}%") and $query->where('category', 'like', "%{$searchQuery}%");

            })
            ->latest()
            ->paginate(setting('pagination_limit'))
            ->through(function ($item) {
                $itemcategory = AssetCategory::find($item->id);
                return [
                    'id' => $item->id,
                    'name' => $item->recommnum,
                    'description' => $item->user,
                    'category' => $itemcategory ? $itemcategory->name : null,
                    'created_at' => $item->created_at->format('Y-m-d h:i A'),

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


        $latestRecord = AssetItem::latest('id')->first();


        $lastRecommNum = optional($latestRecord)->recommnum;


        $recommnum = 'ITM' . str_pad(
            (intval(substr($lastRecommNum, 4)) + 1),
            strlen($lastRecommNum) - 4,
            '0',
            STR_PAD_LEFT
        );

        $assetItem = AssetItem::create([
            'name' => $request->name,
            'itemcode' => $request->itemcode,
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
