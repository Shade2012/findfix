<?php
namespace App\Repositories;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\FoundRepositoryInterface;
use App\Models\FoundImages;
use App\Utils\Status;
use Carbon\Carbon;
use App\Models\Found;
use App\Models\FoundCategory;
use App\Models\FoundStatus;
use App\Models\Room;
use Illuminate\Support\Facades\DB;


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

    public function getFound(int $id){
        return Found::where('id' ,$id)->first();
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

        if (!empty($params['found_status_id'])) {
            $query->where('found_status_id', '=', $params['found_status_id']);
  
        }

        if(!empty($params['last_date'])){
            $query->where('found_date','>=',Carbon::parse($params['last_date']));
        }

        $data = $query->with(['user','room.building','category','status','foundImages'])->get();
         return response()->success([
            'founds' => $data
        ]);
    }

    public function getFoundCountsByStatus(array $params = []){
        $currentYear = Carbon::now()->year;
        $query = Found::query();

        $query->selectRaw('MONTH(found_date) as month, COUNT(*) as total')
        ->whereYear('found_date', $currentYear);

        $query->where('found_status_id', '=', $params['found_status_id']);

        $data = $query->groupByRaw('MONTH(found_date)')
              ->pluck('total', 'month');
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
           $monthlyData[$month] = $data[$month] ?? 0;
        }
         return response()->success([
            'found_status_id' => $params['found_status_id'],
            'statistic' => $monthlyData
            
        ]);
    }

    public function updateFound(int $id,array $data = []){
        $found = Found::findOrFail($id);
        $found->update($data);
        return $found->fresh(['user','room.building','category','status','foundImages']);
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

    public function deleteFound(int $id){
        try{
            Found::where('id', $id)->delete();
            return response()->success(null,'Berhasil Hapus');
        } catch (ModelNotFoundException $e) {
            return response()->error('Data tidak ditemukan', null, 404);
        } catch (\Exception $e) {
            return response()->error('Gagal menghapus laporan', $e->getMessage(), 500);
        }
    }

    public function addFoundImages(Found $found, array $files){
        if($files){
            foreach ($files as $image) {
                $filename = (string) Str::uuid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('images', $filename, 'public');
                $found->foundImages()->create(['image_path' => $path]);
            }
        }
    }

    public function deleteFoundImages(array $ids){
        $images = FoundImages::whereIn('id', $ids)->get();
        foreach($images as $img){
            Storage::disk("public")->delete($img->image_path);
        }
        FoundImages::whereIn("id", $ids)->delete();
    }
}


