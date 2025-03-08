<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssetItem;
class AssetItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AssetItem::create([
            'name' => 'Laptop',
            'description' => 'Dell XPS 13',
            'itemcode' => 'ITM1',
            'category_id' => 1,
            'status' => 1,
            'created_by' => 1,
        ]);

        AssetItem::create([
            'name' => 'Desk',
            'itemcode' => 'ITM2',
            'description' => 'Office desk',
            'category_id' => 2,
            'status' => 1,
            'created_by' => 1,
        ]);
    }
}
