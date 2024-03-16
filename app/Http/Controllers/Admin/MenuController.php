<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
        public function index()
        {
            $userId = auth()->user()->id;

            // Check if the user ID is 1
            if ($userId == 1) {
                // If user ID is 1, show all menus
                $menu = Menu::select('menus.*')
                    ->where('menus.is_active', 1)
                    ->where('menus.parent_id', 0)
                    ->orderBy('menus.sort_order', 'ASC')
                    ->get();

                 // For each top-level menu item, fetch and attach its submenus based on user access
                 $menu->each(function ($menuItem){
                    $menuItem->submenus = Menu::select('menus.*')
                        ->where('menus.is_active', 1)
                        ->where('menus.parent_id', $menuItem->menu_id)
                        ->orderBy('menus.sort_order', 'ASC')
                        ->get();
                });

            } else {
                // If user ID is not 1, show menus based on user access
                $menu = Menu::select('menus.*')
                    ->join('usermenus', 'menus.menu_id', '=', 'usermenus.menu_id')
                    ->where('menus.is_active', 1)
                    ->where('menus.parent_id', 0)
                    ->where('usermenus.user_id', $userId)
                    ->orderBy('menus.sort_order', 'ASC')
                    ->get();

                // For each top-level menu item, fetch and attach its submenus based on user access
                $menu->each(function ($menuItem) use ($userId) {
                    $menuItem->submenus = Menu::select('menus.*')
                        ->join('usermenus', 'menus.menu_id', '=', 'usermenus.menu_id')
                        ->where('menus.is_active', 1)
                        ->where('menus.parent_id', $menuItem->menu_id)
                        ->where('usermenus.user_id', $userId)
                        ->orderBy('menus.sort_order', 'ASC')
                        ->get();
                });
            }

            return $menu;
        }

        public function usermenu()
        {
            $data = Menu::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('menu_title', 'like', "%{$searchQuery}%");
            })
            ->latest()
            ->paginate(setting('pagination_limit'));

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
        //
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
        $data = Menu::find($id);
        $data->delete();
        return response()->json(['message' => 'Menu successfull Deleted']);
    }
}
