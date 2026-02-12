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
    public function createRoom(Request $request){
        $validated = $request->validate([
            'building_id' => 'nullable|exists:buildings,id',
            'building_name' => 'required_if:building_id,null|string|max:255',
            'description' => 'required_if:building_id,null|string',
            'name_room' =>'required|string|max:255',
            'room_description' => 'required|string|max:255',
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
    }
}
