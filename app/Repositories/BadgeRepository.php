<?php
namespace App\Repositories;

use App\Interfaces\BadgeRepositoryInterface;
use App\Models\Badge;

class BadgeRepository implements BadgeRepositoryInterface{
    public function getAllBadges()
    {
        return Badge::orderBy('min_found', 'asc')->get();
    }

    public function getBadgeById(int $id){
        return Badge::findOrFail($id);
    }

    public function createBadge(array $data = []){
        return Badge::create($data);
    }

    public function updateBadge(int $id, array $data = []){
        $badge = Badge::findOrFail($id);
        $badge->update($data);
        return $badge->fresh();
    }

    public function deleteBadge(int $id){
        Badge::findOrFail($id)->delete();
        return true;
    }
}
