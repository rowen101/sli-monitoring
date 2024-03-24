<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Pallet;
use App\Models\tbl_site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{

    $site_id = $request->input('site_id');
    $data = Pallet::query()
        ->when(request('query'), function ($query, $searchQuery) {
            $query->where('site_id', 'like', "%{$searchQuery}%");
        })
        ->latest()
        ->when($site_id, function ($query, $site_id) { // Filter by site_id if provided
            $query->where('site_id', $site_id);
        })
        ->get() // Removed pagination, using get() to fetch all records
        ->map(function ($item) {
            $user = User::find($item->user_id);
            $site = tbl_site::find($item->site_id);

            $date = ($item->date instanceof \DateTime) ? $item->date->format('Y-m-d') : null;
            $createdAt = ($item->created_at instanceof \DateTime) ? $item->created_at->format('Y-m-d h:i A') : null;

            $spacetotalutelpercent = ($item->allocatedpalletspace != 0) ? ($item->spaceuteltotal / $item->allocatedpalletspace) * 100 : null;
            $excess = $item->spaceuteltotal - $item->allocatedpalletspace;
            $cost = number_format($item->spaceuteltotal * $item->caseperpallet, 2);

            return [
                'id' => $item->id,
                'site' => $site ? $site->site_name : null,
                'user_id' => $item->user_id,
                'site_id' => $item->site_id,
                'created_user' => $user ? $user->first_name . ' ' . $user->last_name : null,
                'date' => $item->date,
                'allocatedpalletspace' => $item->allocatedpalletspace,
                'spaceuteltotal' => $item->spaceuteltotal,
                'spacetotalutelpercent' => $spacetotalutelpercent . '%',
                'excess' => $excess,
                'caseperpallet' => $item->caseperpallet,
                'cost' => '₱' . $cost,
                'created_at' => $createdAt,
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
                'site_id' => $request->site_id,
                'user_id' => $request->user_id,
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

    public function bulkDelete()
    {
        Pallet::whereIn('id', request('ids'))->delete();

        return response()->json(['message' => 'Datas deleted successfully!']);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Pallet::find($id);
        $data->delete();
        return response()->json(['message' => 'Task successfull Deleted']);
    }

    public function filter(Request $request)
    {
        $userId = auth()->user()->id;
        $site_id = $request->input('site_id');
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $data = Pallet::orderBy('pallets.date', 'asc')
            ->join('tbl_sites', 'tbl_sites.id', '=', 'pallets.site_id')
            ->where('pallets.user_id', $userId) // Changed 'user_id' to 'tbl_dailytask.user_id'
            ->whereBetween('pallets.date', [$startDate, $endDate])
            ->select('pallets.*', 'tbl_sites.site_name')
            ->when($site_id, function ($query, $site_id) { // Filter by site_id if provided
                $query->where('site_id', $site_id);
            })
            ->get()
            ->map(function ($item) {
                $user = User::find($item->user_id);
                $site = tbl_site::find($item->site_id);

                $date = ($item->date instanceof \DateTime) ? $item->date->format('Y-m-d') : null;
                $createdAt = ($item->created_at instanceof \DateTime) ? $item->created_at->format('Y-m-d h:i A') : null;

                $spacetotalutelpercent = ($item->allocatedpalletspace != 0) ? ($item->spaceuteltotal / $item->allocatedpalletspace) * 100 : null;
                $excess = $item->spaceuteltotal - $item->allocatedpalletspace;
                $cost = number_format($item->spaceuteltotal * $item->caseperpallet, 2);

                return [
                    'id' => $item->id,
                    'site' => $site ? $site->site_name : null,
                    'user_id' => $item->user_id,
                    'site_id' => $item->site_id,
                    'created_user' => $user ? $user->first_name . ' ' . $user->last_name : null,
                    'date' => $item->date,
                    'allocatedpalletspace' => $item->allocatedpalletspace,
                    'spaceuteltotal' => $item->spaceuteltotal,
                    'spacetotalutelpercent' => $spacetotalutelpercent . '%',
                    'excess' => $excess,
                    'caseperpallet' => $item->caseperpallet,
                    'cost' => '₱' . $cost,
                    'created_at' => $createdAt,
                ];
            });

        return $data;
    }

}
