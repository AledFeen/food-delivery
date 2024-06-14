<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use stdClass;

class CategoryController extends Controller
{
    public function addMainCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $user_id = Auth::id();
        $store = DB::selectOne('select * from stores where users_id = ? limit 1', [$user_id]);

        $storeId = $store->id;
        $name = $request->input('name');
        $path = '/';

        DB::update('Insert into categories(store_id, path, name) values (?,?,?)', [$storeId, $path, $name]);
    }

    public function addSubCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'parent' => 'required|integer'
        ]);

        $user_id = Auth::id();
        $store = DB::selectOne('select * from stores where users_id = ? limit 1', [$user_id]);
        $storeId = $store->id;
        $name = $request->input('name');
        $parentId = $request->input('parent');
        $parent = DB::selectOne('select * from categories where id = ? limit 1', [$parentId]);
        $path = $parent->path . $parent->id . '/';
        DB::update('Insert into categories(store_id, path, name, parent_id) values (?,?,?,?)', [$storeId, $path, $name, $parentId]);
    }

    public function getCategoriesByStoreId(Request $request) {
        $request->validate([
            'store_id' => 'required|integer'
        ]);

        $storeId = $request->query('store_id');
        $categories = DB::select('select * from categories where store_id = ?', [$storeId]);
        $filteredCategories = $this->refactorCategories($categories);
        return ['categories' => $filteredCategories];
    }

    public function getCategories()
    {
        $user_id = Auth::id();
        $store = DB::selectOne('select * from stores where users_id = ? limit 1', [$user_id]);
        $categories = DB::select('select * from categories where store_id = ?', [$store->id]);
        $filteredCategories = $this->refactorCategories($categories);
        return ['categories' => $filteredCategories];
    }

    private function refactorCategories($categories): array
    {
        $maxParts = 0;
        $filteredCategories = [];
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $parts = explode('/', $category->path);
                $num = count($parts);
                if ($num > $maxParts) {
                    $maxParts = count($parts);
                }
            }

            for ($i = $maxParts; $i >= 2; $i--) {
                foreach ($categories as $category) {
                    $parts = explode('/', $category->path);
                    $num = count($parts);
                    if ($num == $i) {
                        $parentId = $parts[$num - 2];
                        $item = $category;
                        foreach ($categories as $findCategory) {
                            if ($findCategory->id == $parentId) {
                                $findCategory->childs[] = $item;
                            }
                        }
                    }
                }
            }

            foreach ($categories as $category) {
                $parts = explode('/', $category->path);
                $num = count($parts);
                if ($num == 2) {
                    $filteredCategories[] = $category;
                }
            }

            foreach ($filteredCategories as $category) {
                if (is_object($category) && property_exists($category, 'childs')) {
                    foreach ($category->childs as $child) {
                        $child->forProducts = true;
                    }
                } else {
                    $category->forProducts = true;
                }
            }
        }

        return $filteredCategories;
    }

    public function deleteSubcategory(Request $request) {

        $request->validate([
            'category' => 'required|integer'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $products = DB::select('select * from products where category_id = ?', [$data['category']]);

            foreach ($products as $product) {
                DB::delete('delete from product_category_has_products where product_id = ?', [$product->id]);
                DB::delete('delete from product_category_has_products where parent_id = ?', [$product->id]);
                DB::delete('delete from product_categories where product_id = ?', [$product->id]);
                $image = DB::selectOne('select imagePath from products where id = ?', [$product->id]);
                Storage::delete('/public/images/products/' . $image->imagePath);
                DB::delete('delete from products where id = ?', [$product->id]);
            }

            DB::delete('delete from categories where id = ?', [$data['category']]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Транзакция не удалась: ' . $e->getMessage());
        }
    }

    public function deleteCategory(Request $request) {

        $request->validate([
            'category' => 'required|integer'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $subCategories = DB::select('select * from categories where parent_id = ?', [$data['category']]);

            foreach ($subCategories as $subCategory) {
                $products = DB::select('select * from products where category_id = ?', [$subCategory->id]);

                foreach ($products as $product) {
                    DB::delete('delete from product_category_has_products where product_id = ?', [$product->id]);
                    DB::delete('delete from product_category_has_products where parent_id = ?', [$product->id]);
                    DB::delete('delete from product_categories where product_id = ?', [$product->id]);
                    $image = DB::selectOne('select imagePath from products where id = ?', [$product->id]);
                    Storage::delete('/public/images/products/' . $image->imagePath);
                    DB::delete('delete from products where id = ?', [$product->id]);
                }
                DB::delete('delete from categories where id = ?', [$subCategory->id]);
            }

            DB::delete('delete from categories where id = ?', [$data['category']]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Транзакция не удалась: ' . $e->getMessage());
        }
    }

}
