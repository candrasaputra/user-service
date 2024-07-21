<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Hash;

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
        $input['password'] = Hash::make($input['password']);
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

        $input = $request->only(['name', 'username']);;
        $input['updated_by'] = $authenticatedUser->id;
        // Find the user by ID
        $user = User::findOrFail($id);

        // Update the user
        $user->update($input);

        return response()->json([
            'message' => 'username berhasil diperbarui'
        ]);
    }

    public function updatePassword($id, Request $request) {
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
            'password' => 'required|min:8|max:100',
            'confirm_password' => 'required|same:password|min:8|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'validations' => $validator->errors()
            ], 400);
        }

        // Get the authenticated user
        $authenticatedUser = $request->user();

        $input = $request->only(['password']);
        $input['password'] = Hash::make($input['password']);
        $input['updated_by'] = $authenticatedUser->id;
        // Find the user by ID
        $user = User::findOrFail($id);

        // Update the user
        $user->update($input);

        return response()->json([
            'message' => 'password berhasil diperbarui'
        ]);
    }

    public function deletePassword($id, Request $request) {
        $idValidator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:users,id',
        ]);

        if ($idValidator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'validations' => $idValidator->errors()
            ], 400);
        }

        // Validate the request body
        $bodyValidator = Validator::make($request->all(), [
            'confirm_password' => 'required|min:8|max:100',
        ]);

        if ($bodyValidator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'validations' => $bodyValidator->errors()
            ], 400);
        }
    
        // Get the authenticated user
        $authenticatedUser = $request->user();

        // Find the user by ID
        $user = User::findOrFail($id);

        $input = $request->only(['confirm_password']);

        // Check if the provided confirm_password matches the user's password
        if (!Hash::check($input['confirm_password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid input',
                'validations' => [
                    'confirm_password' => ['The provided password does not match our records.']
                ]
            ], 400);
        }

        // Delete the user
        $user->update([
            'password' => null
        ]);

        return response()->json([
            'message' => 'password berhasil dihapus'
        ]);
    }
}
