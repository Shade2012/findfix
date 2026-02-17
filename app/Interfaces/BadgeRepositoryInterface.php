<?php
namespace App\Interfaces;

interface BadgeRepositoryInterface{
    public function getAllBadges();
    public function getBadgeById(int $id);
    public function createBadge(array $params = []);
    public function updateBadge(int $id, array $params = []);
    public function deleteBadge(int $id);
}
