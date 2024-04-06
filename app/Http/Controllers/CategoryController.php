<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use stdClass;

class CategoryController extends Controller
{
    public function addMainCategory(Request $request)
    {
        $user_id = Auth::id();
        $store = DB::selectOne('select * from stores where users_id = ? limit 1', [$user_id]);

        $storeId = $store->id;
        $name = $request->input('name');
        $path = '/';

        DB::update('Insert into categories(store_id, path, name) values (?,?,?)', [$storeId, $path, $name]);
    }

    public function addSubCategory(Request $request)
    {
        $user_id = Auth::id();
        $store = DB::selectOne('select * from stores where users_id = ? limit 1', [$user_id]);
        $storeId = $store->id;
        $name = $request->input('name');
        $parentId = $request->input('parent');
        $parent = DB::selectOne('select * from categories where id = ? limit 1', [$parentId]);
        $path = $parent->path . $parent->id . '/';
        DB::update('Insert into categories(store_id, path, name, parent_id) values (?,?,?,?)', [$storeId, $path, $name, $parentId]);
    }

    public function getCategories()
    {
        $user_id = Auth::id();

        $store = DB::selectOne('select * from stores where users_id = ? limit 1', [$user_id]);

        $categories = DB::select('select * from categories where store_id = ?', [$store->id]);

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
        return ['categories' => $filteredCategories, 'maxPath' => $maxParts];
    }

    public function deleteCategory() {

    }
}
