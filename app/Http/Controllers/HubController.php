<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Interfaces\HubRepositoryinterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class HubController extends Controller{
    private HubRepositoryinterface $hubRepository;
    public function __construct(HubRepositoryinterface $hubRepository){
        $this->hubRepository = $hubRepository;
    }
    public function getAllHub(){
          try{
             $hubs = $this->hubRepository->getAllHub();
             return response()->success($hubs,'Berhasil mendapatkan hubs');
         }catch(\Exception $e){
            return response()->error("Gagal mendapatkan hubs ".$e->getMessage());
        }
    }

      public function getHub($id){
          try{
             $hub = $this->hubRepository->getHubByid($id);
             return response()->success($hub,'Berhasil mendapatkan hub');
         }catch(\Exception $e){
            return response()->error("Gagal mendapatkan hub ".$e->getMessage());
        }
    }

    public function create(Request $request){
        try{
            $validated = $request->validate([
                'hub_name'=>'string|max:255',
                'hub_description' => 'string|max:255',
                'room_id'=>'integer|exists:rooms,id',
            ]);

            $result = DB::transaction(function () use ($validated) {
                $hub = $this->hubRepository->createHub( $validated);
                return $hub;
            });
            return response()->success($result,'Berhasil membuat hub');
        }catch(\Exception $e){
            return response()->error("Gagal membuat hub",$e->getMessage());
        }
    }

    public function update($id,Request $request){
        try{
            $validated = $request->validate([
                'hub_name'=>'nullable|string|max:255',
                'hub_description' => 'nullable|string|max:255',
                'room_id'=>'nullable|integer|exists:rooms,id',
            ]);

            $result = DB::transaction(function () use ($validated, $id) {
                $updatedHub = $this->hubRepository->updateHub($id,$validated);
                return $updatedHub;
            });
            return response()->success($result,'Berhasil mengubah hub');
        }catch(\Exception $e){
            return response()->error("Gagal mengubah hub",$e->getMessage());
        }
    }
    public function delete($id){
        try {
        $this->hubRepository->deleteHub($id);
        return response()->success(null, 'Berhasil hapus hub');
    } catch (\Exception $e) {
        return response()->error($e->getMessage(),null,500);
        }
    }
}