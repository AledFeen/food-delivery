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
    public function selectUserStore() {
        $user_id = Auth::id();
        $store = DB::selectOne('select * from stores where users_id = ? limit 1', [$user_id]);
        return ['store' => $store];
    }

    public function updateStoreProfile(Request $request) {

        DB::beginTransaction();

        try {
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
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Произошла ошибка: ' . $e->getMessage()], 500);
        }
    }
}
