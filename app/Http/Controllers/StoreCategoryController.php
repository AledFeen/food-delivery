<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StoreCategoryController extends Controller
{
    public function getCategories() {
        $categories = DB::select('select * from store_categories');
        return ['categories' => $categories];
    }
}
