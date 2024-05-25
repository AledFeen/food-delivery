<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{

    public function checkStoreExistence()
    {
        $user = DB::selectOne('select * from stores where users_id = ?', [Auth::id()]);
        $result = $user ? true : false;
        return ['result' => $result];
    }

    public function selectUserStore()
    {
        $user_id = Auth::id();
        $store = DB::selectOne('select * from stores where users_id = ? limit 1', [$user_id]);
        return ['store' => $store];
    }

    public function getStoreById(Request $request)
    {
        $id = $request->query('id');
        $store = DB::selectOne('select * from stores where id = ? limit 1', [$id]);
        return ['store' => $store];
    }

    public function getStoresByCityId(Request $request)
    {
        $cityId = $request->query('city_id');
        $stores = DB::select('select s.* from stores s inner join cities_has_stores chs on s.id = chs.store_id where chs.city_id = ?', [$cityId]);
        return ['stores' => $stores];
    }

    public function updateStoreProfile(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->file('image')->isValid()) {
            $user_id = Auth::id();
            $store = DB::selectOne('select * from stores where users_id = ? limit 1', [$user_id]);
            $im = $store->image;
            Storage::delete('/public/images/stores/' . $im);

            $name = $request->input('name');
            $description = $request->input('description');
            $type_store = $request->input('type_store');
            $store_category = $request->input('store_category');
            $dateTime = new DateTime();
            $date = $dateTime->format('Y-m-d');
            $image = $request->file('image');
            $imagePath = Storage::put('/public/images/stores', $image);
            $imageName = basename($imagePath);
            DB::update('update stores set name = ?, description = ?, image = ?, type_store = ?, category = ?, updated_at = ? where id = ?',
                [$name, $description, $imageName, $type_store, $store_category, $date, $store->id]);
            return back()->with('success', 'Successfully added.');
        }
        return back()->with('error', 'Incorrect image format.');
    }
}
