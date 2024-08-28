<?php
namespace App\Http\Controllers;

use App\Models\User;
use Validator;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login', 'register']]);
    }


    public function login(Request $request)
{
    $credentials = $request->only('phone', 'password');


    $validator = Validator::make($credentials, [
        'phone' => 'required|string|min:10',
        'password' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 401);
    }

    if (! $token = auth()->guard('api')->attempt($validator->validated())) {
       

        $user = User::where('phone', $request->phone)->first();
         // echo "testing". $user;
            // exit;
        if (!$user) {
            Log::error('User not found', ['phone' => $request->phone]);
            // echo "testing". $user;
            // exit;

        } else {
            if (!Hash::check($request->password, $user->password)) {
                Log::error('Password mismatch', ['entered_password' => $request->password, 'stored_hash' => $user->password]);
            }
        }
        
        return response()->json(['error' => 'Unauthorized', 'code' => 401]);
    }

    Log::info('Successful authentication', ['token' => $token]);

    return $this->createNewToken($token);
}

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'phone'  => 'required|min:10|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
            $password =  bcrypt('afrinext@2024');

            $user = User::create(array_merge(
                $validator->validated(),
                ['password' => $password]
            ));

        return response()->json([
            'user' => $user,
            'message' => 'User successfully registered'
        ], 200);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    protected function createNewToken($token)
    {
        $user = auth()->guard('api')->user();

        // $user = auth()->user();
        // $roleId = isset($user->role_id) ? $user->role_id : ''; // Corrected role_id retrieval
        // $permission = RoleHasPermission::with('permission')->where(['status' => '1', 'role_id' => $roleId])->get();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
            'message' => 'User Login Successfully',
            'code' => 200
        ]);
    }

   
}
