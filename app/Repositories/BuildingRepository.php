<?php
namespace App\Repositories;

use App\Interfaces\BuildingRepositoryInterface;
use App\Models\Building;
use App\Models\Room;
class BuildingRepository implements BuildingRepositoryInterface{
    public function getBuilding(array $params = []){
        $query = Building::query();

        if (!empty($params['building_name'])) {
            $query->where('building_name', 'like', '%' . $params['building_name'] . '%');
        }

        if (!empty($params['description'])) {
            $query->where('description', 'like', '%' . $params['description'] . '%');
        }

        return $query->with('rooms.hub')->get();
    }

    public function getBuildingById(int $id){
        return Building::find($id);
    }

    public function getRoomByBuildingId(int $buildingId){
        return Building::findOrFail($buildingId)->rooms;
    }

    public function createRoom(array $data): Room {
        return Room::create($data);
    }

    public function createBuilding(array $data): Building{
        return Building::create($data);
    }
}

