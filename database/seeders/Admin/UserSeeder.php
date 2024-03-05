<?php

namespace Database\Seeders\Admin;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path() . '/seeders/Admin/User.json';
        $str = file_get_contents($filePath);
        $json = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $str), true);
        foreach ($json as $value) {
            $rowdata = new User();
            $rowdata->id = $value["id"];
            $rowdata->email = $value["email"];
            $rowdata->email_verified_at = $value["email_verified_at"];
            $rowdata->password =Hash::make( $value["password"]);
            $rowdata->password_change_date = $value["passwordChangeDate"];
            $rowdata->role = $value["role"];
            $rowdata->name = $value["name"];
            $rowdata->first_name = $value["firstName"];
            $rowdata->last_name = $value["lastName"];
            $rowdata->position = $value["position"];
            $rowdata->gender = $value["gender"];
            $rowdata->last_ip_address = $value["lastIPAddress"];
            $rowdata->last_activity = $value["lastActivity"];
            $rowdata->incorrect_logins = $value["incorrectLogins"];
            $rowdata->avatar = $value["avatar"];
            $rowdata->is_active = $value["isActive"];
            $rowdata->remember_token = $value["remember_token"];
            $rowdata->sitehead_user_id = $value["sitehead_user_id"];
            $rowdata->created_by = 0;
            $rowdata->save();
        }

    }
}
