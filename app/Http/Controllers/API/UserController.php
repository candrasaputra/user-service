<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;

class UserController extends Controller
{
    public function getUsers(Request $request) {
        $user = User::all();

        return response()->json([
            'data' => $user
        ]);
    }

    public function getUser($id) {
        $idValidator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:users,id',
        ]);

        if ($idValidator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'validations' => $idValidator->errors()
            ], 400);
        }

        $user = User::find($id);

        return response()->json([
            'data' => $user
        ]);
    }

    public function createUser(Request $request) {
        // Get the authenticated user
        $authenticatedUser = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4|max:100',
            'username' => 'required|min:4|max:100|unique:users',
            'password' => 'required|min:8|max:100',
            'confirm_password' => 'required|same:password|min:8|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'validations' => $validator->errors()
            ], 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['created_by'] = $authenticatedUser->id;
        $user = User::create($input);

        return response()->json([
            'message' => 'username berhasil disimpan'
        ]);
    }

    public function updateUser($id, Request $request) {
        $idValidator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:users,id',
        ]);

        if ($idValidator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'validations' => $idValidator->errors()
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4|max:100',
            'username' => 'required|min:4|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'validations' => $validator->errors()
            ], 400);
        }

        // Get the authenticated user
        $authenticatedUser = $request->user();

        $input = $request->all();
        $input['updated_by'] = $authenticatedUser->id;
        // Find the user by ID
        $user = User::findOrFail($id);

        // Update the user
        $user->update($input);

        return response()->json([
            'message' => 'username berhasil diperbarui'
        ]);
    }
}
