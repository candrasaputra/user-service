<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Hash;
use App\Services\UserService;
use InvalidArgumentException;
use Exception;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function getUsers(Request $request) {

        $user = $this->userService->getUsers();

        return response()->json([
            'data' => $user
        ]);
    }

    public function getUser($id) {
        $status = 200;

        try {
            $result['data'] = $this->userService->getUser($id);
        } catch (InvalidArgumentException $e) {
            $status = 400;
            $result = [
                'message' => 'Invalid input',
                'validations' => json_decode($e->getMessage())
            ];
        }

        return response()->json($result, $status);
    }

    public function createUser(Request $request) {
        $status = 200;

        try {
            $result['message'] = $this->userService->createUser($request);
        } catch (InvalidArgumentException $e) {
            $status = 400;
            $result = [
                'message' => 'Invalid input',
                'validations' => json_decode($e->getMessage())
            ];
        }

        return response()->json($result, $status);
    }

    public function updateUser($id, Request $request) {
        $status = 200;

        try {
            $result['message'] = $this->userService->updateUser($id, $request);
        } catch (InvalidArgumentException $e) {
            $status = 400;
            $result = [
                'message' => 'Invalid input',
                'validations' => json_decode($e->getMessage())
            ];
        }

        return response()->json($result, $status);
    }

    public function updatePassword($id, Request $request) {
        $status = 200;

        try {
            $result['message'] = $this->userService->updatePassword($id, $request);
        } catch (InvalidArgumentException $e) {
            $status = 400;
            $result = [
                'message' => 'Invalid input',
                'validations' => json_decode($e->getMessage())
            ];
        }

        return response()->json($result, $status);
    }

    public function deletePassword($id, Request $request) {
        $status = 200;

        try {
            $result['message'] = $this->userService->deletePassword($id, $request);
        } catch (InvalidArgumentException $e) {
            $status = 400;
            $result = [
                'message' => 'Invalid input',
                'validations' => json_decode($e->getMessage())
            ];
        }

        return response()->json($result, $status);
    }
}
