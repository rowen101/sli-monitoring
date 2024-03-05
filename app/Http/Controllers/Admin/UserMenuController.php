<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use App\Models\Usermenu;
use Illuminate\Http\Request;
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
        $menuItem->hasAccess = UserMenu::where('user_id', $user_id)
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

        $user_id = $request->input('user_id');
        $menu_ids = $request->input('menu_id');


        foreach ($menu_ids as $menu_id) {
            Usermenu::updateOrInsert(
                ['user_id' => $user_id, 'menu_id' => $menu_id],
                ['user_id' => $user_id, 'menu_id' => $menu_id]
            );
        }

        return response()->json(['message' => 'Data saved successfully']);


    }

    public function retrieveUserMenu($id)
    {
        Usermenu::where('user_id', $id)->get();
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
