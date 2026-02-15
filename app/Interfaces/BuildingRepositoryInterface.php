<?php
namespace App\Interfaces;

use App\Models\Building;
use App\Models\Room;
interface BuildingRepositoryInterface{
    public function getBuilding(array $params = []);

    public function getBuildingById(int $id);
    public function getRoomByBuildingId(int $buildingid);

    public function createRoom(array $data) : Room;
    public function createBuilding(array $data) : Building;

    public function updateRoom(int $id, array $data = []);
    public function updateBuilding(int $id,array $data = []);
    public function deleteRoom(int $id);
    public function deleteBuilding(int $id);
}
