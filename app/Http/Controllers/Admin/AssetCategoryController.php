<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\AssetItem;
use Illuminate\Http\Request;
use App\Models\User;
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
                $query->where('name', 'like', "%{$searchQuery}%") and $query->where('description', 'like', "%{$searchQuery}%") and $query->where('category', 'like', "%{$searchQuery}%");

            })
            ->latest()
            ->paginate(setting('pagination_limit'))
            ->through(function ($item) {
                $usercreate = User::find($item->created_by);
                $usercreated_by = $usercreate ? $usercreate->name : null;
                $userupdate = User::find($item->updated_by);
                $userupdated_by = $userupdate ? $userupdate->name : null;
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'categorycode' => $item->categorycode,
                    'description' => $item->description,
                    'status' => $item->status,
                    'created_by' => $usercreated_by,
                    'updated_by' => $userupdated_by,
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
        return response()->json(['message' => 'success']);
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
            'categorycode' => 'required',
        ]);
        $data = [
            'name' => $request->name,
            'categorycode' => $request->categorycode,
            'description' => $request->description,
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
        AssetCategory::updateOrCreate(
            ['id' => $request->id],
            $data
        );

        return response()->json(['message' => 'Asset successfully saved']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       return response()->json(['message' => 'success']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->json(['message' => 'success']);
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
        return response()->json(['message' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $itemExists = AssetItem::where('category_id', $id)->exists();

        if ($itemExists) {
            // If the ID exists in the item table, return a message indicating it's in use
            return response()->json(['message' => 'This item is in use. Delete canceled.'], 400);
        }

        $data = AssetCategory::find($id);
       if ($data) {
        $data->delete();
        return response()->json(['message' => 'Data successfully deleted']);
        }
        return response()->json(['message' => 'Data not found'], 404);
    }

    public function getCategory()
    {
        $data = AssetCategory::Select('id','name')->where('status', 1)->get();
        return response()->json($data);
    }
}
