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
        $request -> validate([
            'store' => 'required|integer',
            'city' => 'required|integer',
            'phone' => 'required|integer|min:10|max:15',
            'address' => 'required|string',
            'price' => 'required|integer'
        ]);

        $store = $request->input('store');
        $city = $request->input('city');
        $basket = $request->input('basket');
        $phone = $request->input('phone');
        $address = $request->input('address');
        $user = Auth::id();
        $dateTime = new DateTime();
        $date = $dateTime->format('Y-m-d H:i:s');
        $price = $request->input('price');

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

    public function getAvailableOrders() {
        $user_id = Auth::id();
        $courier = DB::selectOne('select * from couriers where users_id = ? limit 1', [$user_id]);
        $orders = DB::select('select orders.*, c.name as city, st.name as storeName from orders
        left join main.cities c on c.id = orders.cities_id
        left join main.stores st on st.id = orders.stores_id
        where status = 0 and cities_id = ?', [$courier->cities_id]);
        return ['orders' => $orders];
    }

    public function selectOrderForDelivery(Request $request) {
        $request -> validate([
            'orderId' => 'required|integer',
        ]);

        $orderId = $request->input('orderId');
        $user_id = Auth::id();
        $courier = DB::selectOne('select * from couriers where users_id = ? limit 1', [$user_id]);
        $dateTime = new DateTime();
        $date = $dateTime->format('Y-m-d H:i:s');

        DB::update('update orders set courier_id = ?, updated_at = ?, status = 1 where id = ?', [$courier->id, $date, $orderId]);
        return ['order' => $orderId];
    }

    public function getCurrentOrderForCourier() {
        $user_id = Auth::id();
        $courier = DB::selectOne('select * from couriers where users_id = ? limit 1', [$user_id]);
        $orders = DB::select('select orders.*, c.name as city, c.country, cour.name as courierName, cour.surname, cour.phone as phoneCourier, st.name as storeName from orders
        left join main.cities c on c.id = orders.cities_id
        left join main.couriers cour on cour.id = orders.courier_id
        left join main.stores st on st.id = orders.stores_id where courier_id = ? and status = 1', [$courier->id]);

        return ['orders' => $this->addProductsToOrders($orders)];
    }

    public function getCompletedOrdersForCourier() {
        $user_id = Auth::id();
        $courier = DB::selectOne('select * from couriers where users_id = ? limit 1', [$user_id]);
        $orders = DB::select('select orders.*, c.name as city, c.country, cour.name as courierName, cour.surname, cour.phone as phoneCourier, st.name as storeName from orders
        left join main.cities c on c.id = orders.cities_id
        left join main.couriers cour on cour.id = orders.courier_id
        left join main.stores st on st.id = orders.stores_id where courier_id = ? and status = 2', [$courier->id]);

        return ['orders' => $orders];
    }

    public function getUserOrders() {
        $user_id = Auth::id();
        $orders = DB::select('select orders.*, c.name as city, c.country, cour.name as courierName, cour.surname, cour.phone as phoneCourier, st.name as storeName from orders
        left join main.cities c on c.id = orders.cities_id
        left join main.couriers cour on cour.id = orders.courier_id
        left join main.stores st on st.id = orders.stores_id
         where orders.users_id = ? ORDER BY id DESC', [$user_id]);


        return ['orders' => $this->addProductsToOrders($orders)];
    }

    public function submitDelivery(Request $request) {
        $request -> validate([
            'orderId' => 'required|integer'
        ]);

        $dateTime = new DateTime();
        $date = $dateTime->format('Y-m-d H:i:s');
        $orderId = $request->input('orderId');
        DB::update('update orders set status = 2, updated_at = ? where id = ?', [$date, $orderId]);
    }

    private function addProductsToOrders($orders) {
        $products = DB::select('select * from selected_products');
        $options = DB::select('select * from selected_options');
        foreach ($orders as $order) {
            foreach ($products as $product) {
                if($order->id == $product->orders_id) {
                    $pr = clone $product;
                    foreach ($options as $option) {
                        if($option->selected_product_id == $pr->id) {
                            $pr->options[] = $option;
                        }
                    }
                    $order->products[] = $pr;
                }
            }
        }
        return $orders;
    }
}
