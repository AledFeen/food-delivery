<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use function Laravel\Prompts\select;

class ProductController extends Controller
{
    public function addProduct(Request $request)
    {
        $name = $request->input('name');
        $description = $request->input('description');
        $category_id = $request->input('category_id');
        $price = $request->input('price');
        $image = $request->file('image');

        $imagePath = Storage::put('/public/images/products', $image);
        $imageName = basename($imagePath);

        DB::insert('insert into products(name, description, price, imagePath, category_id) VALUES (?,?,?,?,?)', [$name, $description, $price, $imageName, $category_id]);
    }

    public function updateProduct(Request $request)
    {
        $prevImage = $request->input('prevImage');
        Storage::delete('/public/images/products/' . $prevImage);

        $product_id = $request->input('product_id');
        $name = $request->input('name');
        $description = $request->input('description');
        $category_id = $request->input('category_id');
        $price = $request->input('price');
        $image = $request->file('image');

        $imagePath = Storage::put('/public/images/products', $image);
        $imageName = basename($imagePath);

        DB::update('update products set name = ?, description = ?, price = ?, imagePath = ?, category_id = ? where id = ?', [$name, $description, $price, $imageName, $category_id, $product_id]);
    }

    public function getProductCategories(Request $request)
    {
        $user_id = Auth::id();
        $store = DB::selectOne('select * from stores where users_id = ? limit 1', [$user_id]);
        $productId = $request->query('productId');

        return $this->makeProductCategories($store->id, $productId);
    }

    public function getProductCategoriesByStoreId(Request $request)
    {
        $productId = $request->query('productId');
        $storeId = $request->query('storeId');

        return $this->makeProductCategories($storeId, $productId);
    }

    private function makeProductCategories($storeId, $productId)
    {
        $products = DB::select('select p.* from categories c inner join products p on c.id = p.category_id
            where c.store_id = ?', [$storeId]);

        $productsCategories = DB::select('select pc.* from products p inner join product_categories pc on p.id = pc.product_id where p.id = ?', [$productId]);

        $productsHasCategories = DB::select('select pchp.* from products p inner join product_category_has_products pchp on p.id = pchp.parent_id where p.id = ?', [$productId]);

        $staticProducts = array();

        foreach ($products as $k => $v) {
            $staticProducts[$k] = clone $v;
        }

        foreach ($products as $product) {
            if ($product->id == $productId) {
                foreach ($productsCategories as $category) {
                    foreach ($productsHasCategories as $phc) {
                        if ($phc->category_id == $category->id && $phc->parent_id == $product->id) {
                            foreach ($staticProducts as $stProduct) {
                                if ($stProduct->id == $phc->product_id) {
                                    $category->items[] = $stProduct;
                                }
                            }
                        }
                    }
                }
            }
        }
        return ['productCategories' => $productsCategories, 'storeProducts' => $staticProducts];
    }

    public function getProductsByCategoryId(Request $request)
    {
        $categoryId = $request->query('categoryId');
        $products = DB::select('select * from products where category_id = ?', [$categoryId]);
        return ['products' => $products];
    }

    public function addProductCategory(Request $request)
    {
        $productId = $request->input('productId');
        $name = $request->input('name');
        $type = $request->input('type');
        $count = $request->input('count');

        DB::insert('Insert into product_categories(product_id, name, type, count) VALUES (?,?,?,?)', [$productId, $name, $type, $count]);
    }

    public function addItemToCategory(Request $request)
    {
        $productId = $request->input('productId');
        $parentId = $request->input('parentId');
        $categoryId = $request->input('categoryId');

        DB::insert('insert into product_category_has_products(product_id, parent_id, category_id) values (?,?,?)', [$productId, $parentId, $categoryId]);
    }

    public function deleteItemFromProductCategory(Request $request)
    {
        $data = $request->all();
        DB::delete('delete from product_category_has_products where category_id = ? and product_id = ? and parent_id = ?', [$data['category'], $data['item'], $data['parent']]);
    }

    public function deleteCategoryFromProduct(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            DB::delete('delete from product_category_has_products where category_id = ? and parent_id = ?', [$data['category'], $data['parent']]);
            DB::delete('delete from product_categories where id = ? and product_id = ?', [$data['category'], $data['parent']]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Транзакция не удалась: ' . $e->getMessage());
        }

    }

    public function deleteProduct(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();

            DB::delete('delete from product_category_has_products where product_id = ?', [ $data['product']]);
            DB::delete('delete from product_category_has_products where parent_id = ?', [ $data['product']]);
            DB::delete('delete from product_categories where product_id = ?', [$data['product']]);
            DB::delete('delete from products where id = ?', [$data['product']]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Транзакция не удалась: ' . $e->getMessage());
        }
    }
}
