<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Interfaces\BadgeRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BadgeController extends Controller{
    private BadgeRepositoryInterface $badgeRepository;

    public function __construct(BadgeRepositoryInterface $badgeRepository){
        $this->badgeRepository = $badgeRepository;
    }

    public function getAllBadges(){
        try{
            $badges = $this->badgeRepository->getAllBadges();
            return response()->success($badges,'Berhasil mendapatkan badges');
        }catch(\Exception $e){
            return response()->error("Gagal mendapatkan badges ".$e->getMessage());
        }
    }

    public function getBadge($id){
        try{
            $badge = $this->badgeRepository->getBadgeById($id);
            return response()->success($badge,'Berhasil mendapatkan badge');
        }catch(\Exception $e){
            return response()->error("Gagal mendapatkan badge ".$e->getMessage());
        }
    }

    public function create(Request $request){
        try{
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'icon' => 'required|string|max:50',
                'min_found' => 'required|integer|min:0',
            ]);

            $result = DB::transaction(function () use ($validated) {
                return $this->badgeRepository->createBadge($validated);
            });
            return response()->success($result,'Berhasil membuat badge');
        }catch(\Exception $e){
            return response()->error("Gagal membuat badge",$e->getMessage());
        }
    }

    public function update($id, Request $request){
        try{
            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'icon' => 'nullable|string|max:50',
                'min_found' => 'nullable|integer|min:0',
            ]);

            $result = DB::transaction(function () use ($validated, $id) {
                return $this->badgeRepository->updateBadge($id, $validated);
            });
            return response()->success($result,'Berhasil mengubah badge');
        }catch(\Exception $e){
            return response()->error("Gagal mengubah badge",$e->getMessage());
        }
    }

    public function delete($id){
        try {
            $this->badgeRepository->deleteBadge($id);
            return response()->success(null, 'Berhasil hapus badge');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), null, 500);
        }
    }
}
