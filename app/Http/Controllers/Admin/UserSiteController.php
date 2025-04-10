<?php

namespace App\Http\Controllers\Admin;

use App\Models\tbl_site;
use App\Models\UserSites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserSiteController extends Controller
{
    public function getUserSites($userId)
    {

        $sites = tbl_site::select([
            'tbl_sites.id',
            'tbl_sites.site_name',
            DB::raw('IF(user_sites.site_id IS NOT NULL, 1, 0) as hasAccess')
        ])
            ->leftJoin('user_sites', function ($join) use ($userId) {
                $join->on('tbl_sites.id', '=', 'user_sites.site_id')
                    ->where('user_sites.user_id', '=', $userId);
            })
            ->where('tbl_sites.is_active', 1)
            ->orderBy('tbl_sites.site_name', 'ASC')
            ->get();

        return response()->json($sites);
    }




    public function getsitewthuserid()
    {
        tbl_site::where('is_active', 1)
            ->orderBy('site_name', 'ASC')
            ->get();

        return response()->json();
    }

    public function onSaveupdate(Request $request)
    {
        try {
            // Begin transaction
            DB::beginTransaction();

            // Extract user_id and menu_ids from the request
            $user_id = $request->input('user_id');
            $site_ids = $request->input('site_id');

            // Delete existing user-menu relationships for the specified user_id
            UserSites::where('user_id', $user_id)->delete();

            // Prepare the data array for bulk insert
            $data = [];
            foreach ($site_ids as $site_id) {
                $data[] = ['user_id' => $user_id, 'site_id' => $site_id];
            }

            // Bulk insert the new user-menu relationships
            UserSites::insert($data);

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
}
