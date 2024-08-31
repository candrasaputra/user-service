<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use InvalidArgumentException;
use Validator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function getUsers() {
        return $this->userRepository->all();
    }

    public function getUser($id) {
        $idValidator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:users,id',
        ]);

        if ($idValidator->fails()) {
            throw new InvalidArgumentException($idValidator->errors());
        }

        $user = $this->userRepository->find($id);

        return $user;
    }

    public function createUser($request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4|max:100',
            'username' => 'required|min:4|max:100|unique:users',
            'password' => 'required|min:8|max:100',
            'confirm_password' => 'required|same:password|min:8|max:100'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors());
        }

        $input = $request->only(['name', 'username', 'password']);
        $input['password'] = Hash::make($input['password']);
        $input['created_by'] = $request->user()->id;
        $user = $this->userRepository->create($input);

        return 'username berhasil disimpan';
    }

    public function updateUser($id, $request) {
        $idValidator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:users,id',
        ]);

        if ($idValidator->fails()) {
            throw new InvalidArgumentException($idValidator->errors());
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4|max:100',
            'username' => 'required|min:4|max:100|unique:users,username,'.$id
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors());
        }

        $input = $request->only(['name', 'username']);;
        $input['updated_by'] = $request->user()->id;

        $this->userRepository->update($id, $input);

        return 'username berhasil diperbarui';
    }

    public function updatePassword($id, $request) {
        $idValidator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:users,id',
        ]);

        if ($idValidator->fails()) {
            throw new InvalidArgumentException($idValidator->errors());
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|max:100',
            'confirm_password' => 'required|same:password|min:8|max:100'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors());
        }

        $input = $request->only(['password']);
        $input['password'] = Hash::make($input['password']);
        $input['updated_by'] = $request->user()->id;

        $this->userRepository->update($id, $input);

        return 'password berhasil diperbarui';
    }

    public function deletePassword($id, $request) {
        $idValidator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:users,id',
        ]);

        if ($idValidator->fails()) {
            throw new InvalidArgumentException($idValidator->errors());
        }

        // Validate the request body
        $bodyValidator = Validator::make($request->all(), [
            'confirm_password' => 'required|min:8|max:100',
        ]);

        if ($bodyValidator->fails()) {
            throw new InvalidArgumentException($bodyValidator->errors());
        }
    
        // Find the user by ID
        $user = $this->userRepository->find($id);

        $input = $request->only(['confirm_password']);

        // Check if the provided confirm_password matches the user's password
        if (!Hash::check($input['confirm_password'], $user->password)) {
            throw new InvalidArgumentException(
                json_encode(['confirm_password' => ['The provided password does not match our records.']]));
        }

        // Delete the user
        $user->update([
            'password' => null
        ]);

        return 'password berhasil dihapus';
    }
}
