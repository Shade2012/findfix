<?php
namespace App\Repositories;

use App\Interfaces\FoundRepositoryInterface;
use App\Models\FoundImages;
use App\Utils\Status;
use Carbon\Carbon;
use App\Models\Found;
use App\Models\FoundCategory;
use App\Models\FoundStatus;
use App\Models\Room;

class FoundRepository implements FoundRepositoryInterface{
    public function getNewestFound(){
        $found =  Found::with(['room','user'])->where('found_status_id',Status::DITEMUKAN)->first();
        $lost =  Found::with(['room','user'])->where('found_status_id',Status::HILANG->value)->first();
        
        return response()->success([
           'Ditemukan' => $found,
           'Hilang' => $lost,
        ]);
    }

    public function getCountReport(){
        $statusCounts = Found::selectRaw('found_status_id, COUNT(*) as total')
            ->groupBy('found_status_id')
            ->pluck('total', 'found_status_id');

        $found_count_status_found   = $statusCounts[Status::DITEMUKAN->value] ?? 0;
        $found_count_status_lost    = $statusCounts[Status::HILANG->value] ?? 0;
        $found_count_status_return  = $statusCounts[Status::DIKEMBALIKAN->value] ?? 0;
        $found_count_status_archive = $statusCounts[Status::TERSIMPAN->value] ?? 0;

        return response()->success([
           'Ditemukan' => $found_count_status_found,
           'Hilang' => $found_count_status_lost,
           'Dikembalikan' => $found_count_status_return,
           'Tersimpan' => $found_count_status_archive,
        ]);
    }

    public function getFounds(array $params = []){

        
        $query = Found::query();
        if(!empty($params['found_name'])){
            $query->where('found_name','LIKE','%'.$params['found_name'].'%');
        }
        if (!empty($params['room_id'])) {
            $query->where('room_id', "=",$params['room_id']);
        }

        if (!empty($params['found_category_id'])) {
            $query->where('found_category_id', '=', $params['found_category_id']);
  
        }

        if(!empty($params['last_date'])){
            $query->where('found_date','>=',Carbon::parse($params['last_date']));
        }

        $data = $query->with(['user','room.building','category','status','foundImages'])->get();
         return response()->success([
            'founds' => $data
        ]);
    }

    public function createReport(array $data = []){
         return Found::create($data);
    }

    public function getFoundStatus(){
        $status =  FoundStatus::all();
        return response()->success([
            'data' => $status
        ]);
    }
    public function getFoundCategory(){
        $category =  FoundCategory::all();
        return response()->success([
            'data' => $category
        ]);
        
    }

    public function createFoundImages(array $data = []){
        return FoundImages::create($data);
    }
}


