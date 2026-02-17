<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use App\Models\Found;
use App\Models\User;
use App\Models\Hub;
use Illuminate\Support\Str;
use App\Utils\Status;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Interfaces\FoundRepositoryInterface;
use App\Notifications\FindFixNotification;
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
                'location_hub_id' => 'nullable|integer|exists:hubs,id',
            ]);

            $files = $request->file('found_img');
    
            if ($files && !is_array($files)) {
                $files = [$files];
            }
            $found = $this->foundRepository->getFound($id);
            if ($this->isUnauthorized($found)){
                return response()->error("Gagal mengubah catatan",'Unauthorized',403);
            }

            $oldStatusId = $found->found_status_id;

            $result = DB::transaction(function () use ($validated,$found, $id, $files) {
                $updatedFound = $this->foundRepository->updateFound($id,$validated);
                if ($files) {
                        $this->foundRepository->addFoundImages($found,$files);
                }
                return $updatedFound;
            });

            // Notify report owner if status changed
            if (isset($validated['found_status_id']) && $validated['found_status_id'] != $oldStatusId) {
                $owner = User::find($found->user_id);
                if ($owner) {
                    $statusNames = [
                        Status::DITEMUKAN->value => 'Ditemukan',
                        Status::HILANG->value => 'Hilang',
                        Status::DIKEMBALIKAN->value => 'Dikembalikan',
                        Status::TERSIMPAN->value => 'Tersimpan',
                    ];
                    $newStatusName = $statusNames[$validated['found_status_id']] ?? 'diperbarui';
                    $message = 'Status barang "' . $found->found_name . '" telah diubah menjadi ' . $newStatusName;

                    // If stored in a hub, mention the hub name
                    if ($validated['found_status_id'] == Status::TERSIMPAN->value && !empty($validated['location_hub_id'])) {
                        $hub = Hub::find($validated['location_hub_id']);
                        if ($hub) {
                            $message = 'Barang "' . $found->found_name . '" telah disimpan di hub ' . $hub->hub_name;
                        }
                    }

                    $owner->notify(new FindFixNotification(
                        title: 'Status Barang Diperbarui',
                        message: $message,
                        type: 'success',
                        actionUrl: '/laporan/' . $found->id
                    ));
                }
            }

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

        // Notify all other users + admins about new report
        $statusLabel = $validated['found_status_id'] == Status::HILANG->value ? 'kehilangan' : 'menemukan';
        $otherUsers = User::where('id', '!=', auth()->id())->get();
        foreach ($otherUsers as $user) {
            $user->notify(new FindFixNotification(
                title: 'Laporan Baru',
                message: auth()->user()->name . ' telah melaporkan ' . $statusLabel . ' barang: ' . $validated['found_name'],
                type: 'info',
                actionUrl: '/laporan/' . $found->id
            ));
        }

        return response()->success($found,'Berhasil Nambah',201);
    }

    public function confirmStatusFound(Request $request){
        try{
            $validated = $request->validate([
                'report_missing_id'=>'required|integer|exists:founds,id',
                'report_found_id' => 'required|integer|exists:founds,id',
                'hub_id' => 'required|integer|exists:hubs,id',
            ]);
            $reportMissingId = $validated['report_missing_id'];
            $reportFoundId = $validated['report_found_id'];
            $hubId = $validated['hub_id'];
            $result = $this->foundRepository->switchStatusFound($reportMissingId,$reportFoundId, $hubId);

            // Notify both report owners
            $hub = Hub::find($hubId);
            $hubName = $hub ? $hub->hub_name : 'hub';

            $foundReport = Found::find($reportFoundId);
            $missingReport = Found::find($reportMissingId);

            if ($foundReport && $foundReport->user_id) {
                $owner = User::find($foundReport->user_id);
                if ($owner) {
                    $owner->notify(new FindFixNotification(
                        title: 'Barang Telah Disimpan',
                        message: 'Barang "' . $foundReport->found_name . '" telah disimpan di hub ' . $hubName,
                        type: 'success',
                        actionUrl: '/laporan/' . $foundReport->id
                    ));
                }
            }

            if ($missingReport && $missingReport->user_id) {
                $owner = User::find($missingReport->user_id);
                if ($owner) {
                    $owner->notify(new FindFixNotification(
                        title: 'Barang Ditemukan',
                        message: 'Barang "' . $missingReport->found_name . '" telah ditemukan dan disimpan di hub ' . $hubName,
                        type: 'success',
                        actionUrl: '/laporan/' . $missingReport->id
                    ));
                }
            }

            return response()->success($result,'Berhasil penemuan barang',200);

        }catch(\Exception $e){
            return response()->error("Gagal melakukan konfirmasi penemuan barang ".$e->getMessage());
        }
    }
}
