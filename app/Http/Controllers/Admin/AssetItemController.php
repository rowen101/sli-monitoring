<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\AssetItem;
use Illuminate\Http\Request;
use App\Models\User;
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
                $query->where('name', 'like', "%{$searchQuery}%") and $query->where('description', 'like', "%{$searchQuery}%");

            })
            ->latest()
            ->paginate(setting('pagination_limit'))
            ->through(function ($item) {

                $usercreate = User::find($item->created_by);
                $usercreated_by = $usercreate ? $usercreate->name : null;
                $userupdate = User::find($item->updated_by);
                $userupdated_by = $userupdate ? $userupdate->name : null;
                $itemcategory = AssetCategory::find($item->category_id);
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'itemcode' => $item->itemcode,
                    'description' => $item->description,
                    'category' => $itemcategory ? $itemcategory->name : null,
                    'created_by' => $usercreated_by,
                    'updated_by' => $userupdated_by,
                    'status' => $item->status,
                    'created_at' => $item->created_at->format('Y-m-d h:i A'),
                    'updated_at' => $item->created_at->format('Y-m-d h:i A'),

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
        $userId = auth()->user()->id;

        request()->validate([
            'name' => 'required',
            'description' => 'required',
            'itemcode' => 'required',
        ]);
        $data = [
            'name' => $request->name,
            'itemcode' => $request->itemcode,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'created_by' => $userId,
            'updated_by' => null
        ];

         // Check if 'id' is provided in the request
         if ($request->id) {
            // If 'id' is provided, include 'updated_by' in the data array
            $data['updated_by'] = $userId;
        } else {
            // If 'id' is not provided (indicating creation), set 'updated_by' to null
            $data['updated_by'] = null;
        }

        // Use updateOrCreate with the prepared data
        AssetItem::updateOrCreate(
            ['id' => $request->id],
            $data
        );

        return response()->json(['message' => 'Data successfully saved']);
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
        $data = AssetItem::find($id);
        $data->delete();
        return response()->json(['message' => 'Data successfully deleted']);
    }

    public function findCategorybyitem($id)
    {
        $categorybyid = AssetCategory::where('name',$id)->first();
        if (!$categorybyid) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        $data = AssetItem::select('id', 'name')
        ->where('status', 1)
        ->where('category_id', $categorybyid->id) 
        ->get();

        return response()->json($data);
    }
}
