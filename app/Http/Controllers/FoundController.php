<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use App\Models\Found;
use Illuminate\Support\Str;
use App\Utils\Status;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Interfaces\FoundRepositoryInterface;
use Illuminate\Http\Request;

class FoundController extends Controller
{
    private FoundRepositoryInterface $foundRepository;
    public function __construct(FoundRepositoryInterface $foundRepository){
        $this->foundRepository = $foundRepository;
    }

    private function isUnauthorized(Found $found){
            $user = auth()->user();
            $user_id = $user->id;
            $role = $user->role->name;
        return $found->user_id != $user_id && $role != 'admin';
    }
    public function getFounds(Request $request){
        $validated = $request->validate([
            'room_id' => 'nullable|integer|exists:rooms,id',
            'found_name' => 'nullable|string|max:255',
            'found_category_id' => 'nullable|integer|exists:found_categories,id',
            'found_status_id' => 'nullable|integer|exists:found_statuses,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'last_date' => 'nullable|date'
        ]);
        return $this->foundRepository->getFounds($validated);
    }

    public function getFound($id){
        try{
             $relations = ['user','room.building','category','status','foundImages'];
             $found = $this->foundRepository->getFound($id, $relations);
             return response()->success($found,'Berhasil mendapatkan found detail id '.$id);
         }catch(\Exception $e){
            return response()->error("Gagal mendapatkan found detail id ".$id.$e->getMessage());
        }

    }


    public function getFoundCountByStatusId(){
        $found_status_lost    = Status::HILANG->value;
        $found_status_return  = Status::DIKEMBALIKAN->value;
        $found_statistic_lost = $this->foundRepository->getFoundCountsByStatus($found_status_lost);
        $found_statistic_return = $this->foundRepository->getFoundCountsByStatus($found_status_return);
         return response()->success([
            'statistic_lost' => $found_statistic_lost,
            'statistic_return' => $found_statistic_return
        ],"Berhasil mendapatkan data statistik");
    }
    public function update($id, Request $request){
        try{
            $validated = $request->validate([
                'room_id'=>'nullable|integer',
                'found_img' => 'nullable',
                'found_img.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
                'found_category_id'=> 'nullable|integer',
                'found_status_id'=> 'nullable|integer',
                'found_description' => 'nullable|string|max:1000',
                'found_name' => 'nullable|string|max:255',
                'found_phone_number' => 'nullable|string|max:255',
                'found_date' => 'nullable|date', 
            ]);

            $files = $request->file('found_img');
    
            if ($files && !is_array($files)) {
                $files = [$files];
            }
            $found = $this->foundRepository->getFound($id);
            if ($this->isUnauthorized($found)){
                return response()->error("Gagal mengubah catatan",'Unauthorized',403);
            }
            $result = DB::transaction(function () use ($validated,$found, $id, $files) {
                $updatedFound = $this->foundRepository->updateFound($id,$validated);
                if ($files) {
                        $this->foundRepository->addFoundImages($found,$files);
                }
                return $updatedFound;
            });
            return response()->success($result,'Berhasil mengubah');
        }catch(\Exception $e){
            return response()->error("Gagal mengubah catatan",$e->getMessage());
        }
    }

    public function deleteImages(Request $request){
        $validated = $request->validate([
            'id_images' => 'required|array',
            'id_images.*' => 'integer|exists:found_images,id',
        ]);
        DB::transaction(function() use ($validated){
            $this->foundRepository->deleteFoundImages($validated['id_images']);
        });

        return response()->success(null,'Berhasil dihapus');
    }

    public function deleteReport($id){
        $found = $this->foundRepository->getFound($id);
        if ($this->isUnauthorized($found)){
            return response()->error("Gagal mengubah catatan",'Unauthorized',403);
        }
        return $this->foundRepository->deleteFound($id);
    }

    public function getCountReport(Request $request){
       $validated = $request->validate([
            'month' => 'nullable|integer'
        ]);

        $month = $validated['month'] ?? -1;
       
        return $this->foundRepository->getCountReport($month);
    }

    public function getNewestReport(){
        return $this->foundRepository->getNewestFound();
    }

    public function getFoundCategory(){
        return $this->foundRepository->getFoundCategory();
    }

    public function getFoundStatus(){
        return $this->foundRepository->getFoundStatus();
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'=>'required|integer',
            'found_img' => 'nullable',
            'found_img.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'found_category_id'=> 'required|integer',
            'found_status_id'=> 'required|integer',
            'found_description' => 'required|string|max:1000',
            'found_name' => 'required|string|max:255',
            'found_phone_number' => 'nullable|string|max:255',
            'found_date' => 'required|date', 
        ]);
      
        $files = $request->file('found_img');
    
        if ($files && !is_array($files)) {
            $files = [$files];
        }

        $validated['user_id'] = auth()->id();
        $data = Arr::except($validated, ['found_img']);
        $found = $this->foundRepository->createReport($data);
        if ($files) {
            foreach ($files as $image) {
            $filename = (string) Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('images', $filename, 'public');

            $this->foundRepository->createFoundImages([
                'found_id' => $found->id,
                'image_path' => $path,
            ]);
        }
    }

        return response()->success($found,'Berhasil Nambah',201);
    }

    public function confirmStatusFound(Request $request){
        try{
            $validated = $request->validate([
                'report_missing_id'=>'required|integer',
                'report_found_id' => 'required|integer',
            ]);
            $reportMissingId = $validated['report_missing_id'];
            $reportFoundId = $validated['report_found_id'];
            $result = $this->foundRepository->switchStatusFound($reportMissingId,$reportFoundId);
            return response()->success($result,'Berhasil penemuan barang',200);

        }catch(\Exception $e){
            return response()->error("Gagal melakukan konfirmasi penemuan barang ".$e->getMessage());
        }
    }
}


