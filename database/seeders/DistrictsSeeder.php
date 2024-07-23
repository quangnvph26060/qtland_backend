<?php

namespace Database\Seeders;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Districts;

class DistrictsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
            
        if (Districts::query()->first())
            return;

        $file_path = resource_path('sql/districts.json');
        $data      = json_decode(file_get_contents($file_path));
        foreach ($data->RECORDS as $item) {
            $cities[] = [
                'id'         => $item->id,
                'name'       => $item->name,
                'city_id' => $item->city_id,
            ];
        }
        Districts::query()->insert($cities ?? []);
    }
}
