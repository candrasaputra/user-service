<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function getUsers(Request $request) {
        $user = User::all();

        return response()->json([
            'data' => $user
        ]);
    }
}
