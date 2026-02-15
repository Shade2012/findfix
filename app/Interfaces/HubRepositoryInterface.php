<?php
namespace App\Interfaces;

interface HubRepositoryinterface{
    public function getAllHub();
    public function getHubByid(int $id);
    public function createHub(array $params = []);
    public function updateHub(int $id, array $params = []);
    public function deleteHub(int $id);
}
