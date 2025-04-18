<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\tbl_site;
use Illuminate\Http\Request;
use App\Models\sliassetmonitoring;
use App\Http\Controllers\Controller;

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
            ->join('tbl_sites', 'tbl_sites.id', '=', 'sliassetmonitoring.site_id')
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('tbl_sites.site_name', 'like', "%{$searchQuery}%");
                $query->where('sliassetmonitoring.asset_name', 'like', "%{$searchQuery}%");
                $query->where('sliassetmonitoring.man_supplier', 'like', "%{$searchQuery}%");
                // Add other conditions as needed
            })
            ->select('sliassetmonitoring.*', 'tbl_sites.id as siteID')
            ->where('sliassetmonitoring.created_by', auth()->user()->id)
            ->latest('sliassetmonitoring.created_at')
            ->paginate(setting('pagination_limit'))
            ->through(function ($item) {

                $site = tbl_site::find($item->site_id);
                $siteName = $site ? $site->site_name : null;
                $usercreate = User::find($item->created_by);
                $usercreated_by = $usercreate ? $usercreate->name : null;
                $userupdate = User::find($item->updated_by);
                $userupdated_by = $userupdate ? $userupdate->name : null;

                // Calculate years of depreciation
                $years = $this->calculateDepreciationYears($item->date_acquired);
                $depreciationcost = ($item->purchasecost != 0 && $years != 0) ? ($item->purchasecost - ($item->depreciationcost * $years)) : 0;
                $depreciationCost = round($depreciationcost, 2);

                return [
                    'id' => $item->id,
                    'site_id' => $item->site_id,
                    'site_name' => $siteName,
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
                    'purchasecost' =>  $item->purchasecost,
                    'depreciationcostbyyear' => $depreciationCost,
                    'depreciationcost' => $item->depreciationcost,
                    'is_active' => $item->is_active,
                    'insurancewarrantyinfo' => $item->insurancewarrantyinfo,
                    'created_by' => $usercreated_by,
                    'updated_by' => $userupdated_by,
                    'created_at' => ($item->created_at ? $item->created_at->format('Y-m-d h:i A') : null),
                    'updated_at' => ($item->updated_at ? $item->updated_at->format('Y-m-d h:i A') : null),
                ];
            });

        return response()->json($data);
    }

    private function calculateDepreciationYears($dateAcquired)
    {
        // Calculate years between date acquired and current date
        $dateAcquired = new \DateTime($dateAcquired);
        $currentDate = new \DateTime();
        $interval = $currentDate->diff($dateAcquired);
        $years = $interval->y + $interval->m / 12 + $interval->d / 365;

        return $years;
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

        $latestRecord = sliassetmonitoring::latest('id')->first();


        $lastRecord = optional($latestRecord)->recommnum;
        $recommnum = 'SLI-AST-' . str_pad(
            (intval(substr($lastRecord, 4)) + 1),
            strlen($lastRecord) - 4,
            '0',
            STR_PAD_LEFT
        );

        $data = [
            'site_id' => $request->site_id,
            'asset_name' => $recommnum,
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
            'depreciationcost' => $request->depreciationcost,
            'insurancewarrantyinfo' => $request->insurancewarrantyinfo,
            'is_active'=>$request->is_active,
            'created_by' => $userId,
            'updated_by' => null // Set updated_by to null for new records
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
        sliassetmonitoring::updateOrCreate(
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
        return response()->json(['message' => 'Asset successful Deleted']);
    }

    public function bulkDelete()
    {
        sliassetmonitoring::whereIn('id', request('ids'))->delete();

        return response()->json(['message' => 'Records deleted successfully!']);
    }

    public function bulkPrint($ids)
    {
      $data = sliassetmonitoring::whereIn('id', explode(',', $ids))
      ->get(['id', 'asset_name','serial','date_acquired','purchasecost','paccountable','location']); // Include the fields you need

        return response()->json($data);

    }
}
