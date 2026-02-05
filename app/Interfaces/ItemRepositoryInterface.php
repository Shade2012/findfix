<?php

namespace App\Interfaces;

interface ItemRepositoryInterface
{
    public function getAllItems(array $filters = []);
    public function getItemById(int $id);
    public function createItem(array $data);
    public function updateItem(int $id, array $data);
    public function deleteItem(int $id);
    public function getUserItems(int $userId);
}
