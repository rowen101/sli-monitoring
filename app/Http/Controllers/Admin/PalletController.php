<?php

namespace App\Http\Controllers\Admin;

use App\Models\tbl_site;
use App\Models\User;
use App\Models\Pallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Pallet::query()
        ->when(request('query'), function ($query, $searchQuery) {
            $query->where('site', 'like', "%{$searchQuery}%");

        })
        ->latest()
        ->paginate(setting('pagination_limit'))
        ->through(function ($item){
            $user = User::find($item->user_id);
            $site = tbl_site::find($item->id);
        return [
            'id' => $item->id,
            'recommnum'=> $item->recommnum,
            'site'=> $site ? $site->site_name : null,
            'created_user' => $user ? $user->first_name . ' ' . $user->last_name : null,
            'date'=>$item->date->format('Y-m-d'),
            'allocatedpalletspace' => $item->allocatedpalletspace,
            'spaceuteltotal' => $item->spaceuteltotal,
            'caseperpallet' => $item->caseperpallet,
            'created_at' => $item->created_at->format('Y-m-d h:i A'),


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
        Pallet::updateOrCreate(
            [
                'id' => $request->id
            ],
            [
                'site_id' => $request->site_name,
                'date' => $request->date,
                'allocatedpalletspace' => $request->allocatedpalletspace,
                'spaceuteltotal' => $request->spaceuteltotal,
                'caseperpallet' => $request->caseperpallet
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
        $data = tbl_site::find($id);
        $data->delete();
        return response()->json(['message' => 'Task successfull Deleted']);
    }
}
