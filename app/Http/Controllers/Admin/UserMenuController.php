<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;

use App\Models\UserMenus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = $request->input('user_id');

        $menu = Menu::select('menus.*')
            ->where('menus.is_active', 1)
            ->where('menus.parent_id', 0)
            ->orderBy('menus.sort_order', 'ASC')
            ->get();

        // For each top-level menu item, fetch and attach its submenus based on user access
        $menu->each(function ($menuItem) use ($user_id) {
            $menuItem->submenus = Menu::select('menus.*')
                ->where('menus.is_active', 1)
                ->where('menus.parent_id', $menuItem->menu_id)
                ->orderBy('menus.sort_order', 'ASC')
                ->get();

            // Check if the user has access to this menu item
            $menuItem->hasAccess = UserMenus::where('user_id', $user_id)
                ->where('menu_id', $menuItem->menu_id)
                ->exists();
        });

        return response()->json($menu);
    }

    public function showusermenu(Request $request)
    {
        $user_id = $request->input('user_id');


        $menu = Menu::select('menus.*')
            ->where('menus.is_active', 1)
            ->where('menus.parent_id', 0)
            ->orderBy('menus.sort_order', 'ASC')
            ->get();

        // // For each top-level menu item, fetch and attach its submenus based on user access
        $menu->each(function ($menuItem) use ($user_id) {
            $menuItem->submenus = Menu::select('menus.*')
                ->where('menus.is_active', 1)
                ->where('menus.parent_id', $menuItem->menu_id)
                ->orderBy('menus.sort_order', 'ASC')
                ->get();

            //Check if the user has access to this menu item
            $menuItem->hasAccess = UserMenus::where('user_id', $user_id)
                ->where('menu_id', $menuItem->menu_id)
                ->exists();
        });

        return response()->json($menu);
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

        $request->validate([
            'user_id' => 'required|integer',
            'menu_id' => 'required|array',
            'menu_id.*' => 'integer',
        ]);

        try {
            // Begin transaction
            DB::beginTransaction();

            // Extract user_id and menu_ids from the request
            $user_id = $request->input('user_id');
            $menu_ids = $request->input('menu_id');

            // Delete existing user-menu relationships for the specified user_id
            UserMenus::where('user_id', $user_id)->delete();

            // Prepare the data array for bulk insert
            $data = [];
            foreach ($menu_ids as $menu_id) {
                $data[] = ['user_id' => $user_id, 'menu_id' => $menu_id];
            }

            // Bulk insert the new user-menu relationships
            UserMenus::insert($data);

            // Commit transaction
            DB::commit();

            return response()->json(['message' => 'Data saved successfully']);
        } catch (\Exception $e) {
            // Rollback transaction on failure
            DB::rollback();

            // Handle the exception, you can log it or return an error response
            return response()->json(['message' => 'Failed to save data', 'error' => $e->getMessage()], 500);
        }
    }

    public function retrieveUserMenu($id)
    {
        UserMenus::where('user_id', $id)->get();
        return response()->json(['message' => 'retrieved successfully!']);
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
