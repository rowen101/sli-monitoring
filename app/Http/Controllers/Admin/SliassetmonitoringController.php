<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\sliassetmonitoring;
use Illuminate\Http\Request;

class SliassetmonitoringController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Sliassetmonitoring::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('asset_name', 'like', "%{$searchQuery}%");
                // Add other conditions as needed
            })
            ->latest()
            ->paginate(setting('pagination_limit'))
            ->through(function ($item) {

                return [
                    'id' => $item->id,
                    'asset_name' => $item->asset_name,
                    'asset_type' => $item->asset_type,
                    'serial' => $item->serial,
                    'date_acquired' => ($item->date_acquired ? $item->date_acquired : 'N/A'),
                    'man_supplier' => $item->man_supplier,
                    'unit' => $item->unit,
                    'location' => $item->location,
                    'paccountable' => $item->paccountable,
                    'locationchangetranfer' => $item->locationchangetranfer,
                    'cucodition' => $item->cucodition,
                    'maintenancenotes' => $item->maintenancenotes,
                    'lastmaintenance' => $item->lastmaintenance,
                    'nextmaintenance' => $item->nextmaintenance,
                    'operationhours' => $item->operationhours,
                    'notes' => $item->notes,
                    'purchasecost' => $item->purchasecost,
                    'insurancewarrantyinfo' => $item->insurancewarrantyinfo,
                    'created_at' => ($item->created_at ? $item->created_at->format('Y-m-d h:i A') : null),

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
        $data = sliassetmonitoring::updateOrCreate(
            [
                'asset_name' => $request->asset_name // Assuming 'asset_name' is the unique identifier
            ],
            [
                'asset_name' => $request->asset_name,
                'asset_type' => $request->asset_type,
                'serial' => $request->serial,
                'date_acquired' => $request->date_acquired,
                'man_supplier' => $request->man_supplier,
                'unit' => $request->unit,
                'location' => $request->location,
                'paccountable' => $request->paccountable,
                'locationchangetranfer' => $request->locationchangetranfer,
                'cucodition' => $request->cucodition,
                'maintenancenotes' => $request->maintenancenotes,
                'lastmaintenance' => $request->lastmaintenance,
                'nextmaintenance' => $request->nextmaintenance,
                'operationhours' => $request->operationhours,
                'notes' => $request->notes,
                'purchasecost' => $request->purchasecost,
                'insurancewarrantyinfo' => $request->insurancewarrantyinfo
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
        $data = sliassetmonitoring::find($id);
        $data->delete();
        return response()->json(['message' => 'asset successfull Deleted']);
    }
}
