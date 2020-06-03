<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use function GuzzleHttp\Promise\all;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $user = User::all();
        return $this->sendResponse($user->toArray(), 'Users retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        return $this->sendResponse($user->toArray(), 'User created successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|min:3',
            'last_name' => 'nullable|min:3',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|min:6',
            'new_password' => 'nullable|min:6',
            'c_new_password' => 'nullable|same:new_password'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $user = User::find($id);
        if (!$user) {
            return $this->sendError('Not found', 'User with id: ' . $id . ' not found', 400);
        }
        if (Hash::check($request->password, $user->password)) {
            isset($request['first_name']) ? $user->first_name = $request['first_name'] : '';
            isset($request['last_name']) ? $user->last_name = $request['last_name'] : '';
            isset($request['email']) && $request['email'] !== $user->email ? $user->email = $request['email'] : '';
            isset($request['new_password']) ? $user->password = Hash::make($request['new_password']) : '';
            $user->save();
            return $this->sendResponse($user->toArray(), 'User updated successfully.');
        } else {
            return $this->sendError('Validation Error.', 'Password miss match', 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->sendError('Not found', 'User with id: ' . $id . ' not found', 400);
        }
        $user->delete();
        return $this->sendResponse($user->toArray(), 'User deleted successfully.');
    }
}
