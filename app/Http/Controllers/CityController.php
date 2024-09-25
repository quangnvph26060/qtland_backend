<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Districts;
use App\Models\Wards;
use Illuminate\Http\Request;

class CityController extends Controller
{

    public function getCities()
    {
        $cities = City::all();
        return response()->json($cities);
    }

    /**
     * Lấy danh sách các quận/huyện dựa vào id của thành phố.
     */
    public function getDistrictsByCityId($cityId)
    {
        $districts = Districts::where('city_id', $cityId)->get();
        return response()->json($districts);
    }

    /**
     * Lấy danh sách các phường/xã dựa vào id của quận/huyện.
     */
    public function getWardsByDistrictId($districtId)
    {
        $wards = Wards::where('district_id', $districtId)->get();
        return response()->json($wards);
    }
}
