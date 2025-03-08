<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssetCategory;
class AssetCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AssetCategory::create([
            'name' => 'Electronics',
            'categorycode' => 'CAT1',
            'description' => 'Electronic devices',
            'status' => 1,
            'created_by' => 1,
        ]);
        AssetCategory::create([
            'name' => 'Furniture',
            'categorycode' => 'CAT2',
            'description' => 'Office furniture',
            'status' => 1,
            'created_by' => 1,
        ]);
    }
}
