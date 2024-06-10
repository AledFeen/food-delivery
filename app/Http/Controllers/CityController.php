<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    public function getCities()
    {
        $cities = DB::select('select * from cities');
        return ['cities' => $cities];
    }

    public function getStoreCitiesList()
    {
        $user_id = Auth::id();
        $store_id = DB::selectOne('select * from stores where users_id = ? limit 1', [$user_id]);
        $cities = DB::select('select c.* from cities c inner join cities_has_stores chs on c.id = chs.city_id where chs.store_id = ?', [$store_id->id]);
        return ['cities' => $cities];
    }
    public function addCityToStore(Request $request)
    {
        $request->validate([
            'city_id' => 'required|integer'
        ]);

        $city_id = $request->input('city_id');
        $user_id = Auth::id();
        $store_id = DB::selectOne('select * from stores where users_id = ? limit 1', [$user_id]);
        DB::update('Insert into cities_has_stores(store_id, city_id) values (?,?)', [$store_id->id, $city_id]);
    }

    public function deleteCityFromList($id)
    {
        $user_id = Auth::id();
        $store = DB::selectOne('select * from stores where users_id = ? limit 1', [$user_id]);
        DB::delete('delete from cities_has_stores where store_id = ? and city_id = ?', [$store->id, $id]);
    }
}
