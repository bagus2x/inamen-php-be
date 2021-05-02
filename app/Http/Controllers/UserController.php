<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['only' => 'find']);
    }

    public function signin(Request $request)
    {
        $validator = Validator::make($request->json()->all(), [
            'username' => 'required|min:4|max:50',
            'password' => 'required|min:6|max:50'
        ]);
        if ($validator->fails()) {
            return $this->failure($validator->errors()->first(), 401);
        }

        $user = User::where('username', $request->json('username'))->orWhere('email', $request->json('username'))->first();
        if (!$user) {
            return $this->failure('Username or email does not exist', 404);
        }

        if (Hash::check($request->json('password'), $user->password)) {
            return $this->success([
                'accessToken' => $this->generateAccessToken($user),
                'user' => $user
            ], 'Sign in successful');
        }

        return $this->failure('Password does not match');
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->json()->all(), [
            'username' => 'required|min:4|max:50',
            'email' => 'required|email|min:4|max:50',
            'password' => 'required|min:6|max:50'
        ]);
        if ($validator->fails()) {
            return $this->failure($validator->errors()->first(), 401);
        }

        $user = User::where('username', $request->json('username'))->orWhere('email', $request->json('email'))->first();
        if ($user) {
            if ($user->username == $request->json('username')) {
                return $this->failure('Username already exist', 400);
            }
            if ($user->email == $request->json('email')) {
                return $this->failure('Email already exist', 400);
            }
        }

        try {
            $user = new User();
            $user->username = $request->json('username');
            $user->email = $request->json('email');
            $user->password = Hash::make($request->json('password'));
            if ($user->save()) {
                return $this->success([
                    'accessToken' => $this->generateAccessToken($user),
                    'user' => $user
                ], 'Registration successful', 201);
            }
            throw new Exception('Registration failed');
        } catch (\Exception $error) {
            return $this->failure($error->getMessage(), 500);
        }
    }

    public function find(Request $request)
    {
        $user = User::find($request->userID);
        if (!$user) {
            return $this->failure('User does not exist', 404);
        }

        return $this->success($user);
    }

    protected function generateAccessToken(User $user)
    {
        $payload = [
            'iss' => 'inamen.vercel.app',
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + 60 * 60
        ];

        return JWT::encode($payload, env('ACCESS_TOKEN_SECRET'));
    }
}
