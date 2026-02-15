<?php
namespace App\Interfaces;

interface AuthRepositoryInterface{
    public function login(string $email, string $password);
    public function getUser();
    public function getUsers();

    public function getUserRoles();
    public function addUser(array $data = []);
    public function updateUser(int $id, array $data = []);
    public function deleteUser(int $id);
}