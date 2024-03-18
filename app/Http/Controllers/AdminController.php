<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
}