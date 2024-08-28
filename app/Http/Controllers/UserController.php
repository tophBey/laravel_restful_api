<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserController extends Controller
{

    public function register(UserRegisterRequest $request): JsonResponse|int
    {

        $data = $request->validated();

        // cek ke database ada tidak user yang sama
        if (User::where('username', $data['username'])->count() == 1) {
            throw new HttpResponseException(response([
                "errors" => [
                    "username" => ["username already registered"]
                ]
            ], 400));
        }

        $user = new User();
        $user->username = $data["username"];
        $user->name = $data["name"];
        $user->password = Hash::make($data['password']);

        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);

    }

    public function login(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();

        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => ["username & password wrong"]
                ]
            ], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return new UserResource($user);

    }

    public function get(Request $request): UserResource{

        // mendapatkan user yang sedang login

        $user = Auth::user();
        return new UserResource($user);

    }


    public function update(UserUpdateRequest $request): UserResource {

        $data = $request->validated();

        // ambi user yang sedang login
        // $user = Auth::user();
        $user = auth()->user();

        // cek ada yang di update salah satu atau tidak 
        if(isset($data['name'])){
            $user->name = $data['name'];
        }

        if(isset($data['password'])){
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return new UserResource($user);
    }

    public function logout(Request $request): JsonResponse{
        $user = Auth::user();
        $user->token = null;
        $user->save();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
