<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StoreTypeController extends Controller
{
    public function getTypes() {
        $types = DB::select('select * from store_types');
        return ['types' => $types];
    }
}
