<?php
namespace App\Repositories;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthRepository implements AuthRepositoryInterface{

    public function login(string $email, string $password){
        $user = User::where("email",$email)->first();
        if(!$user){
            return response()->error('User not found', null, 404); 
        }

        if (!Hash::check($password, $user->password)) {
            return response()->error("Invalid password",null,401);
        }

         $token = $user->createToken('api-token')->plainTextToken;

        return response()->success([
            'user' => $user,
            'token' => $token
        ]);
    }
    public function getUser(){
         $user = Auth::user()->load('role'); // eager load role
    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role->name ?? null,
    ];
    }

}