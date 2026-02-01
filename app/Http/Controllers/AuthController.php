<?php

namespace App\Http\Controllers;

use App\Interfaces\AuthRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthRepositoryInterface $authRepository;
    public function __construct(AuthRepositoryInterface $authRepository){
        $this->authRepository = $authRepository;
    }
    public function index(){
        return $this->authRepository->getUser();
    }
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        return $this->authRepository->login($request->email,$request->password);
    }

    public function testAdmin(){
        return response()->success('khusus admin',"khusus admin");
    }
}
