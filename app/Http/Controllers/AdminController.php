<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{

    public function checkAdminExistence()
    {
        $user = DB::selectOne('select * from admins where users_id = ?', [Auth::id()]);
        $result = $user ? true : false;
        return ['result' => $result];
    }
    public function selectAdmin() {
        $user_id = Auth::id();
        $user = DB::selectOne('select * from admins where users_id = ? limit 1', [$user_id]);
        return ['user' => $user];
    }

    public function getUsers() {
        $users = DB::select('select * from users');
        $couriers = DB::select('select * from couriers');
        $stores = DB::select('select * from stores');

        $returnData = [];
        foreach ($users as $user) {

            $isCourier = false;
            foreach ($couriers as $courier) {
                if ($courier->users_id === $user->id) {
                    $isCourier = true;
                    break;
                }
            }

            $hasStore = false;
            foreach ($stores as $store) {
                if ($store->users_id === $user->id) {
                    $hasStore = true;
                    break;
                }
            }

            $userData = (object) [
                'user' => $user,
                'isCourier' => $isCourier,
                'hasStore' => $hasStore
            ];

            $returnData[] = $userData;
        }
        return ['users' => $returnData];
    }

    public function addStore(Request $request) {

        $request->validate([
            'user_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required|string',
            'type_store' => 'required|string',
        ]);

        $name = $request->input('name');
        $description = $request->input('description');
        $user_id = $request->input('user_id');
        $type_store = $request->input('type_store');

        $dateTime = new DateTime();
        $date = $dateTime->format('Y-m-d');
        $image = $request->file('image');
        $imagePath = Storage::put('/public/images/stores', $image);
        $imageName = basename($imagePath);
        DB::insert('insert into stores(name, description, users_id, image, type_store, created_at)
        values (?,?,?,?,?,?)', [$name, $description, $user_id, $imageName, $type_store, $date]);
    }
}
