<?php

namespace App\Http\Controllers;

use App\Interfaces\AuthRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    private AuthRepositoryInterface $authRepository;
    public function __construct(AuthRepositoryInterface $authRepository){
        $this->authRepository = $authRepository;
    }
    public function index(){
        $user =  $this->authRepository->getUser();
        return response()->success($user,'Berhasil mendapatkan akun user',200);
    }

    public function getUsers(){
        $users = $this->authRepository->getUsers();
        return response()->success($users,'Berhasil mendapatkan users',201);
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        return $this->authRepository->login($request->email,$request->password);
    }

    public function addUser(Request $request){
        $validated = $request->validate([
            'email'=> 'required|email',
            'name'=> 'required|string|max:255',
            'user_role_id'=> 'required|integer|exists:user_roles,id',
            'password'=> ['required', 'string', 'min:8'],
        ]);
        $validated['password'] =  Hash::make($validated['password']);
        try{
            $result = $this->authRepository->addUser($validated);
            return response()->success($result,'Berhasil menambah akun user',201);
        }catch(\Exception $e){
            return response()->error("Gagal menambah user",$e->getMessage());
        }
    }

    public function updateUser($id, Request $request){
        $validated = $request->validate([
            'email' => 'nullable|email',
            'name' => 'nullable|string|max:255',
            'user_role_id' => 'nullable|integer|exists:user_roles,id'
        ]);
        try{
            $result = DB::transaction(function () use ($validated,$id) {
                $updatedUser = $this->authRepository->updateUser($id,$validated);
                return $updatedUser;
            });
            return response()->success($result,'Berhasil mengubah akun user',200);
        }catch(\Exception $e){
            return response()->error("Gagal mengubah user",$e->getMessage());
        }
       
    }

    public function deleteUser($id){
        try{
            $this->authRepository->deleteUser($id);
            return response()->success(true,'Berhasil menghapus akun user',200);
        }catch(\Exception $e){
            return response()->error("Gagal menghapus akun user",$e->getMessage());
        }
    }

    public function getUserRole(){
        try{
            $roles = $this->authRepository->getUserRoles();
            return response()->success($roles,'Berhasil mendapatkan list role',200);
        }catch(\Exception $e){
            return response()->error("Gagal mendapatkan list role",$e->getMessage());
        }
    }
    public function testAdmin(){
        return response()->success('khusus admin',"khusus admin");
    }
}

