<?php
namespace App\Repositories;

use App\Interfaces\HubRepositoryinterface;
use App\Models\Building;
use App\Models\Hub;

class HubRepository implements HubRepositoryinterface{
    public function getAllHub()
    {
        $buildings = Building::whereHas('rooms.hub')
            ->with([
                'rooms' => function ($q) {
                $q->whereHas('hub');
                },
                'rooms.hub'
            ])
            ->get();

        return $buildings;
    }
    public function getHubByid(int $id){
        $hub = Hub::with(['room.building'])->findOrFail($id);
        return $hub;
    }

    public function createHub(array $data = []){
        return Hub::create($data);
    }

    public function updateHub(int $id, array $data = []){
        $hub = Hub::findOrFail($id);
        $hub->update($data);
        return $hub->fresh(['room.building']);
    }

    public function deleteHub(int $id){
        Hub::findOrFail($id)->delete();
        return true;
    }
}