<?php

namespace App\Http\Controllers;
use App\Interfaces\BuildingRepositoryInterface;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class BuildingRoomController extends Controller
{
    private BuildingRepositoryInterface $buildingRepository;
    public function __construct(BuildingRepositoryInterface $buildingRepository){
        $this->buildingRepository = $buildingRepository;
    }

    public function getRoomByBuildingId($buildingId){
        return $this->buildingRepository->getRoomByBuildingId($buildingId);
    }

 

    public function getRoomAndBuilding(){
        try {
            $buildings = $this->buildingRepository->getBuilding();
            return response()->success($buildings,'Berhasil mendapatkan data building');
        }catch(\Exception $e){
            return response()->error("Gagal mendapatkan data building",$e->getMessage());
        }
    }

       public function createRoomSingle(Request $request){
            $validated = $request->validate([
                'description' => 'building_id|string',
                'name_room' =>'string|max:255',
                'room_description' => 'string|max:255',
            ]);
            try {
                $result = DB::transaction(function () use ($validated) {
                if (empty($validated['building_id'])) {
                    $building = $this->buildingRepository->createBuilding([
                        'building_name' => $validated['building_name'],
                        'description'   => $validated['description'],
                    ]);
                } else {
                    $building = $this->buildingRepository->getBuildingById($validated['building_id']);
                    if (!$building) {
                        $building = $this->buildingRepository->createBuilding([
                            'building_name' => $validated['building_name'],
                            'description'   => $validated['description'],
                        ]);
                    }
                }
                    $room = $this->buildingRepository->createRoom([
                        'name_room'     => $validated['name_room'],
                        'building_id' => $building->id,
                        'description' => $validated['room_description'],
                    ]);
                return [
                    'room' => $room,
                    'building' => $building,
                ];
        });

        return response()->success(
            $result,
            'Berhasil membuat room',
            Response::HTTP_CREATED
        );

    } catch (\Exception $e) {
        if ($e instanceof QueryException && $e->getCode() === '23000') {
            return response()->error(
                'Gagal membuat room',
                'Data sudah ada',
                Response::HTTP_CONFLICT
            );
        }
        return response()->error(
            'Gagal membuat room',
            $e->getMessage(),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
        }
        return $this->buildingRepository->getRoomByBuildingId($buildingId);
    }
    public function createRoom(Request $request){
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'name_room' =>'required|string|max:255',
            'room_description' => 'required|string|max:255',
        ]);
        try {
            $room = $this->buildingRepository->createRoom([
                'name_room'     => $validated['name_room'],
                'building_id' => $validated['building_id'],
                'description' => $validated['room_description'],
            ]);
            return response()->success(
                $room,
                'Berhasil membuat room',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return response()->error("Gagal membuat room",$e->getMessage());
        }
    }
    public function createBuilding(Request $request){
        $validated = $request->validate([
            'building_name' => 'required|string|max:255',
            'description' => 'required|string',
            'rooms' => 'nullable|array',
            'rooms.*.name_room' => 'required_with:rooms|string|max:255',
            'rooms.*.description' => 'required_with:rooms|string|max:255',
        ]);
        try {
            $result = DB::transaction(function () use ($validated) {
                $building = $this->buildingRepository->createBuilding([
                        'building_name' => $validated['building_name'],
                        'description'   => $validated['description'],
                ]);
                if (!empty($validated['rooms'])) {
                   foreach ($validated['rooms'] as $roomData) {
                        $roomData['building_id'] = $building->id;
                        $this->buildingRepository->createRoom($roomData);
                    }
                };
            return [
                'room' => $validated['rooms'],
                'building' => $building,
            ];
        });

        return response()->success(
            $result,
            'Berhasil membuat building',
            Response::HTTP_CREATED
        );

    } catch (\Exception $e) {
        if ($e instanceof QueryException && $e->getCode() === '23000') {
            return response()->error(
                'Gagal membuat room',
                'Data sudah ada',
                Response::HTTP_CONFLICT
            );
        }
        return response()->error(
            'Gagal membuat room',
            $e->getMessage(),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
        }
    }

     public function updateBuilding($id,Request $request){
        try{
            $validated = $request->validate([
                'building_name' => 'nullable|string|max:255',
                'description'=>'nullable|string|max:255',
                'rooms' => 'nullable|array',
                'rooms.*.room_id' => 'required_with:rooms|integer|exists:rooms,id',
                'rooms.*.building_id' => 'required_with:rooms|integer|exists:buildings,id',
            ]);

            $result = DB::transaction(function () use ($validated, $id) {
                $updatedBuilding = $this->buildingRepository->updateBuilding($id,$validated);
                $rooms = [];
                if (!empty($validated['rooms'])) {
                   foreach ($validated['rooms'] as $roomData) {
                        $room = $this->buildingRepository->updateRoom($roomData['room_id'],['building_id' => $roomData['building_id']]);
                        $rooms[] = $room;
                    }
                };
                return ["Building" => $updatedBuilding,"Rooms" => $rooms];
            });
            return response()->success($result,'Berhasil mengubah building');
        }catch(\Exception $e){
            return response()->error("Gagal mengubah building",$e->getMessage());
        }
    }
    public function updateRoom($id,Request $request){
        try{
            $validated = $request->validate([
                'building_id' => 'nullable|integer|exists:buildings,id',
                'name_room' => 'nullable|string|max:255',
                'description'=>'nullable|string|max:255',
            ]);

            $result = DB::transaction(function () use ($validated, $id) {
                $updatedRoom = $this->buildingRepository->updateRoom($id,$validated);
                return $updatedRoom;
            });
            return response()->success($result,'Berhasil mengubah room');
        }catch(\Exception $e){
            return response()->error("Gagal mengubah room",$e->getMessage());
        }
    }
    public function deleteBuilding($id){
        try {
        $this->buildingRepository->deleteBuilding($id);
        return response()->success(null, 'Berhasil hapus building');
    } catch (\Exception $e) {
        return response()->error($e->getMessage(),null,500);
        }
    }
     public function deleteRoom($id){
        try {
        $this->buildingRepository->deleteRoom($id);
        return response()->success(null, 'Berhasil hapus room');
    } catch (\Exception $e) {
        return response()->error($e->getMessage(),null,500);
        }
    }
}
