<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Wards;
class WardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
            
        if (Wards::query()->first())
            return;

        $file_path = resource_path('sql/wards.json');
        $data      = json_decode(file_get_contents($file_path));
        foreach ($data->RECORDS as $item) {
            $cities[] = [
                'id'         => $item->id,
                'name'       => $item->name,
                'district_id' => $item->district_id,
            ];
        }
        Wards::query()->insert($cities ?? []);
    }
}
