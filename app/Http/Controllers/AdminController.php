<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use function Laravel\Prompts\select;

class AdminController extends Controller
{
    public function checkAdminExistence()
    {
        $user = DB::selectOne('select * from admins where users_id = ?', [Auth::id()]);
        $result = $user ? true : false;
        return ['result' => $result];
    }

    public function selectAdmin()
    {
        $user_id = Auth::id();
        $user = DB::selectOne('select * from admins where users_id = ? limit 1', [$user_id]);
        return ['user' => $user];
    }

    public function getUsers()
    {
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

            $userData = (object)[
                'user' => $user,
                'isCourier' => $isCourier,
                'hasStore' => $hasStore
            ];

            $returnData[] = $userData;
        }
        return ['users' => $returnData];
    }

    public function addStore(Request $request)
    {
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

    public function addCourier(Request $request) {
        $request->validate([
            'cityId' => 'required|integer',
            'userId' => 'required|integer',
            'name' => 'required|string',
            'surname' => 'required|string',
            'phone' => 'required|integer'
        ]);
        $userId = $request->input('userId');
        $cityId = $request->input('cityId');
        $name = $request->input('name');
        $surname = $request->input('surname');
        $phone = $request->input('phone');
        $dateTime = new DateTime();
        $date = $dateTime->format('Y-m-d');


        DB::table('couriers')->insert([
            'users_id' => $userId,
            'cities_id' => $cityId,
            'name' => $name,
            'surname' => $surname,
            'phone' => $phone,
            'created_at' => $date
        ]);
    }

    public function deleteStore(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer'
        ]);

        $data = $request->all();
        $userId = $data['userId'];
        Log::error($userId);
        try {
            DB::beginTransaction();
            $store = DB::table('stores')->where('users_id', '=', $userId)->first();
            $categories = DB::table('categories')->where('store_id', '=', $store->id)->get();
            if($categories) {
                foreach ($categories as $category) {
                    $subCategories = DB::table('categories')->where('parent_id', '=', $category->id)->get();
                    if($subCategories) {
                        foreach ($subCategories as $subCategory) {
                            $products = DB::table('products')->where('category_id', $subCategory->id)->get();
                            if($products) {
                                foreach ($products as $product) {
                                    DB::table('product_category_has_products')->where('product_id', $product->id)->delete();
                                    DB::table('product_category_has_products')->where('parent_id', $product->id)->delete();
                                    DB::table('product_categories')->where('product_id', $product->id)->delete();
                                    $image = DB::table('products')->where('id', $product->id)->value('imagePath');
                                    if ($image) {
                                        Storage::delete('/public/images/products/' . $image);
                                    }
                                    DB::table('products')->where('id', $product->id)->delete();
                                }
                            }
                            DB::table('categories')->where('id', $subCategory->id)->delete();
                        }
                        DB::table('categories')->where('id', $category->id)->delete();
                    }
                }
            }
            DB::table('cities_has_stores')->where('store_id', '=', $store->id)->delete();
            DB::table('stores')->where('users_id', '=', $userId)->delete();
            Storage::delete('/public/images/stores/' . $store->image);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Transaction didnt completed: ' . $e->getMessage() . $e->getLine());
        }
    }


}
