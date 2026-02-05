<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Interfaces\FoundRepositoryInterface;
use Illuminate\Http\Request;

class FoundController extends Controller
{
    private FoundRepositoryInterface $foundRepository;
    public function __construct(FoundRepositoryInterface $foundRepository){
        $this->foundRepository = $foundRepository;
    }
    public function getFounds(Request $request){
        $validated = $request->validate([
            'room_id' => 'nullable|integer',
            'found_name' => 'nullable|string|max:255',
            'found_category_id' => 'nullable|integer|exists:found_categories,id',
            'last_date' => 'nullable|date'
        ]);
        return $this->foundRepository->getFounds($validated);
    }
    public function getCountReport(){
        
        return $this->foundRepository->getCountReport();
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
            'room_id'=>'integer',
            'found_img' => 'nullable',
            'found_img.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'found_category_id'=> 'integer',
            'found_status_id'=> 'integer',
            'found_description' => 'string|max:1000',
            'found_name' => 'string|max:255',
            'found_phone_number' => 'nullable|string|max:255',
            'found_date' => 'nullable|date', 
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

        return response()->json($found, 201);
    }
// public function update(Request $request){
//     if ($request->hasFile('found_img')) {
//     if ($found->found_img) {
//         Storage::disk('public')->delete($found->found_img);
//     }

//     $found->found_img = $request->file('found_img')->store('founds', 'public');
//     $found->save();
// }

// }
}
