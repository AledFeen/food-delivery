<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $store = $request->input('store');
        $city = $request->input('city');
        $basket = $request->input('basket');
        $phone = $request->input('phone');
        $address = $request->input('address');
        $user = Auth::id();
        $dateTime = new DateTime();
        $date = $dateTime->format('Y-m-d H:i:s');
        $price = 0;
        foreach ($basket as $item) {
            $price += $item['price'];
        }

        try{
            DB::beginTransaction();

            // add order
            DB::insert('insert into orders(users_id, cities_id, stores_id, phoneNumber, address, price, created_at) values (?,?,?,?,?,?,?)',
                [$user, $city, $store, $phone, $address, $price, $date]);

            $lastId = DB::getPdo()->lastInsertId();

            foreach ($basket as $item) {
                $product = $item['product'];
                DB::insert('insert into selected_products(orders_id, name, price, count) values (?,?,?,?)', [$lastId, $product['name'], $product['price'], $item['count']]);
                $lastPrId = DB::getPdo()->lastInsertId();
                foreach ($item['options'] as $option) {
                    if(isset($option['obj'])) {
                        DB::insert('insert into selected_options(selected_product_id, name, price) values (?,?,?)', [$lastPrId, $option['obj']['name'], $option['obj']['price']]);
                    } else {
                        foreach ($option['objs'] as $obj) {
                            DB::insert('insert into selected_options(selected_product_id, name, price) values (?,?,?)', [$lastPrId, $obj['name'], $obj['price']]);
                        }
                    }
                }
            }
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error('Транзакция не удалась: ' . $e->getMessage());
        }
    }
}
