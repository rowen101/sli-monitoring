<?php

namespace Database\Seeders\Admin;

use App\Models\ThemeVsc;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ThemesVSCSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path() . '/seeders/Admin/ThemesVSC.json';
        $str = file_get_contents($filePath);
        $json = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $str), true);
        foreach ($json as $value) {
            $rowdata = new ThemeVsc();
            $rowdata->id = $value["id"];
            $rowdata->userid = $value["userid"];
            $rowdata->background = $value["background"];
            $rowdata->active_background =$value["active_background"];
            $rowdata->font_background = $value["font_background"];
            $rowdata->save();
        }

    }
}
