<?php

namespace App\Http\Controllers;

use App\Http\Resources\resource_me;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'password_generator']]);
    }

    public function is_default_password(){
        $auth = auth()->user();
        $user = User::find($auth['id']);
        $defaultKey = '$2y$10$0JPQeEiraeqcgbrF7R5txe7MsfnWXnsEo7lpeziHBuLTNi7P7ZX8q'; //root

        if($user->password == $defaultKey){
            return response()->json(['message' => 'Masih memakai default password. ayo ganti!'], 411);
        }
        return response()->json(['message' => 'Oke aman']);

    }


    public function reset_password(Request $request){
        $validate = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required'
        ]);

        $auth = auth()->user();

        if(!Hash::check($request['old_password'], $auth['password'])){
            return response()->json(['error' => "old password doenst match"]);
        }

        $data = User::find($auth['id']);
        $data['password'] = Hash::make($request['new_password']);
        $data->save();

        return response()->json(['succes' => 'The password has changed']);
        // dd($data);
    }


    public function password_generator(Request $request){
        $passwordHash = Hash::make($request['password']);
        return $passwordHash;
    }
    
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'The email and password dint match'], 402);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $auth = Auth::user();
        // dd($auth);
        // $auth['email'] = 
        // return response()->json($auth);
        return new resource_me($auth);
        // return UserResource::collection(User::all()); //jika data lebih dari 1
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => 'Bearer ' . $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60 * 24 * 3
        ]);
    }
}
