<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Model::unguard();
            
        if (City::query()->first())
            return;

        $file_path = resource_path('sql/cities.json');
        $data      = json_decode(file_get_contents($file_path));
        foreach ($data->RECORDS as $item) {
            $cities[] = [
                'id'         => $item->id,
                'name'       => $item->name,
                'country_id' => $item->country_id
            ];
        }
        City::query()->insert($cities ?? []);
    }
}
