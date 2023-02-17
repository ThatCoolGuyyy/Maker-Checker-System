<?php

namespace App\Http\Controllers\api;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\AdminResource;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AdminRegisterRequest;

class AuthController extends Controller
{
    public function register(AdminRegisterRequest $AdminRegisterRequest){
        
        $admin = Admin::create([
            'name' => $AdminRegisterRequest->name,
            'email' => $AdminRegisterRequest->email,
            'password' => Hash::make($AdminRegisterRequest->password),

        ]);

        return new AdminResource($admin);
    }

    public function login(AdminLoginRequest $AdminLoginRequest){


            $admin = Admin::where('email',  $AdminLoginRequest->email)->first();

            if (! $admin || ! Hash::check($AdminLoginRequest->password, $admin->password)) {
                return response()->json([
                    'message' => ['Username or password incorrect'],
                ]);
            }
    
        $admin->tokens()->delete();
    
            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully',
                'nam' => new AdminResource($admin),
                'token' => $admin->createToken('auth_token')->plainTextToken,
            ]);
        }
    


    public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'User logged out successfully'
            ]);
    }
}
