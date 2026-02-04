<?php

namespace App\Http\Controllers;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'found_img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('found_img')) {
            $path = $request->file('found_img')->store('founds', 'public');
            // example: founds/abc123.jpg
        }

         $found = Found::create([
            // other fields...
            'found_img' => $path ?? null,
        ]);

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
