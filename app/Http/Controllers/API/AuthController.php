<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Auth;

class AuthController extends Controller
{
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:4|max:100',
            'password' => 'required|min:8|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'validations' => $validator->errors()
            ]);
        }

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = Auth::user();

            $success['token'] = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'data' => $success
            ]);
        } else {
            return response()->json([
                'message' => 'username atau password salah'
            ], 401);
        };
    }
}
