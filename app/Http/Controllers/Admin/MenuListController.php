<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Menu::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('menu_title', 'like', "%{$searchQuery}%");
            })
            ->latest()
            ->paginate(setting('pagination_limit'))
            ->through(function ($item) {
                $parentTitle = null;

                // Check if the parent_id exists and is not the same as the current item's id
                    if ($item->parent_id && $item->parent_id !== $item->id) {
                        $parent = Menu::find($item->parent_id);

                        // Check if the parent item exists
                        if ($parent) {
                            $parentTitle = $parent->menu_title;
                        }
                    }

                // Check if created_at is not null before formatting
                $createdAtFormatted = $item->created_at ? $item->created_at->format('Y-m-d h:i A') : null;

                return [
                    'menu_id' => $item->menu_id,
                    'menu_title' => $item->menu_title,
                    'parent_id' => $item->parent_id,
                    'parent_title' => $parentTitle,
                    'sort_order' => $item->sort_order,
                    'is_active' => $item->is_active,
                    'menu_order' => $item->menu_order,
                    'menu_icon' => $item->menu_icon,
                    'menu_route' => $item->menu_route,
                    'created_by' => $item->created_by,
                    'created_at' => $createdAtFormatted,
                ];
            });

        return $data;
    }

    public function GetParentId ()
    {
        $data = Menu::select('menu_id','menu_title')
        ->where('is_active',1)
        ->get();

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
       $data = Menu::updateOrCreate(
            [
                'menu_id' => $request->menu_id
            ],
            [
                'menu_title' => $request->menu_title,
                'parent_id' => $request->parent_id,
                'menu_icon' => $request->menu_icon,
                'menu_route' => $request->menu_route,
                'is_active' => $request->is_active,
                'sort_order' => $request->sort_order
            ]
        );

        return response()->json($data);
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
      $data = Menu::find($id);
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
    public function destroy($id)
    {
        //
    }
}
