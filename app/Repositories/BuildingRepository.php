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
        return Building::findOrFail($id);
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


    public function updateRoom(int $id, array $data = []){
        $room = Room::findOrFail($id);
        $room->update($data);
        return $room->fresh(['building']);
    }
    public function updateBuilding(int $id,array $data = []){
        $building = Building::findOrFail($id);
        $building->update($data);
        return $building->fresh();
    }
    public function deleteRoom(int $id){
        Room::findOrFail($id)->delete();
        return true;
    }
    public function deleteBuilding(int $id){
        Building::findOrFail($id)->delete();
        return true;
    }
}

