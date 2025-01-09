<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
        $store = Store::where('users_id', $user_id)->first();
        return ['store' => $store];
    }

    public function getStoreById(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $id = $request->query('id');
        $store = Store::find($id);
        return ['store' => $store];
    }

    public function getStoresByCityId(Request $request)
    {
        $request->validate([
            'page' => 'required|integer',
            'city_id' => 'required|integer',
            'filter' => 'nullable|string',
            'searchFilter' => 'required|string'
        ]);

        $cityId = $request->query('city_id');
        $page = $request->query('page');
        $filter = $request->query('filter');
        $search = $request->query('searchFilter');
        $search = explode('_', $search);
        $searchTerm = $search[1];

        if ($filter) {
            $parts = explode('_', $filter);
            if (count($parts) === 3) {
                $type = $parts[1];
                $name = $parts[2];
                if ($type == 'type') {
                    $stores = Store::whereHas('cities', function ($query) use ($cityId) {
                        $query->where('cities.id', $cityId);
                    })
                        ->where('type_store', '=', $name)
                        ->where('name', 'like', '%' . $searchTerm . '%')
                        ->paginate(12, ['*'], 'page', $page);
                } else if ($type == 'category') {
                    $stores = Store::whereHas('cities', function ($query) use ($cityId) {
                        $query->where('cities.id', $cityId);
                    })
                        ->where('category', '=', $name)
                        ->where('name', 'like', '%' . $searchTerm . '%')
                            ->paginate(12, ['*'], 'page', $page);
                }
            }
        } else {
            $stores = Store::whereHas('cities', function ($query) use ($cityId) {
                $query->where('cities.id', $cityId);
            })
                ->where('name', 'like', '%' . $searchTerm . '%')
                ->paginate(12, ['*'], 'page', $page);
        }
        return ['pagination' => $stores, 'search' => $searchTerm];
    }

    public function updateStoreProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'type_store' => 'required|string',
            'store_category' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

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

        $store = Store::find($store->id);
        $store->update([
            'name' => $name,
            'description' => $description,
            'image' => $imageName,
            'type_store' => $type_store,
            'category' => $store_category,
            'updated_at' => $date,
        ]);

        return back()->with('success', 'Successfully added.');
    }
}
