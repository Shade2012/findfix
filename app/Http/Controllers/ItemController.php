<?php

namespace App\Http\Controllers;

use App\Interfaces\ItemRepositoryInterface;
use App\Models\ReportImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ItemController extends Controller
{
    private ItemRepositoryInterface $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    /**
     * Get all items with optional filters
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'category_id',
            'building_id',
            'status',
            'date_from',
            'date_to'
        ]);

        $items = $this->itemRepository->getAllItems($filters);

        return response()->success($items, 'Items retrieved successfully');
    }

    /**
     * Create a new lost/found item report
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:lost,found,returned',
            'date_incident' => 'required|date',
            'building_id' => 'nullable|exists:buildings,id',
            'room_id' => 'nullable|exists:rooms,id',
            'specific_location' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $result = DB::transaction(function () use ($validated, $request) {
                // Add user_id
                $validated['user_id'] = Auth::id();

                // Create item
                $item = $this->itemRepository->createItem($validated);

                // Handle image uploads
                if ($request->hasFile('images')) {
                    $this->handleImageUpload($request->file('images'), $item->id);
                }

                // Load relationships
                return $item->load(['user', 'category', 'building', 'room', 'images']);
            });

            return response()->success(
                $result,
                'Report created successfully',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return response()->error(
                'Failed to create report',
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get single item details
     */
    public function show($id)
    {
        try {
            $item = $this->itemRepository->getItemById($id);
            return response()->success($item, 'Item retrieved successfully');
        } catch (\Exception $e) {
            return response()->error('Item not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update an existing report
     */
    public function update(Request $request, $id)
    {
        try {
            $item = $this->itemRepository->getItemById($id);

            // Check ownership
            if ($item->user_id !== Auth::id() && !Auth::user()->role->name === 'admin') {
                return response()->error(
                    'Unauthorized',
                    'You can only update your own reports',
                    Response::HTTP_FORBIDDEN
                );
            }

            $validated = $request->validate([
                'category_id' => 'sometimes|exists:categories,id',
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'status' => 'sometimes|in:lost,found,returned',
                'date_incident' => 'sometimes|date',
                'building_id' => 'nullable|exists:buildings,id',
                'room_id' => 'nullable|exists:rooms,id',
                'specific_location' => 'nullable|string|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
                'remove_images' => 'nullable|array',
                'remove_images.*' => 'exists:report_images,id'
            ]);

            $result = DB::transaction(function () use ($validated, $request, $id) {
                // Update item
                $item = $this->itemRepository->updateItem($id, $validated);

                // Remove specified images
                if ($request->has('remove_images')) {
                    foreach ($request->remove_images as $imageId) {
                        $image = ReportImage::find($imageId);
                        if ($image && $image->lost_found_item_id == $id) {
                            Storage::disk('public')->delete($image->image_path);
                            $image->delete();
                        }
                    }
                }

                // Add new images
                if ($request->hasFile('images')) {
                    $this->handleImageUpload($request->file('images'), $id);
                }

                return $item->fresh(['user', 'category', 'building', 'room', 'images']);
            });

            return response()->success($result, 'Report updated successfully');
        } catch (\Exception $e) {
            return response()->error(
                'Failed to update report',
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Delete a report
     */
    public function destroy($id)
    {
        try {
            $item = $this->itemRepository->getItemById($id);

            // Check ownership
            if ($item->user_id !== Auth::id() && !Auth::user()->role->name === 'admin') {
                return response()->error(
                    'Unauthorized',
                    'You can only delete your own reports',
                    Response::HTTP_FORBIDDEN
                );
            }

            // Delete images from storage
            foreach ($item->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            // Delete item (will cascade delete images from DB)
            $this->itemRepository->deleteItem($id);

            return response()->success(null, 'Report deleted successfully');
        } catch (\Exception $e) {
            return response()->error(
                'Failed to delete report',
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get authenticated user's reports
     */
    public function myReports()
    {
        $items = $this->itemRepository->getUserItems(Auth::id());
        return response()->success($items, 'Your reports retrieved successfully');
    }

    /**
     * Handle multiple image uploads
     */
    private function handleImageUpload(array $images, int $itemId)
    {
        $order = 0;
        foreach ($images as $image) {
            $filename = time() . '_' . $order . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('reports/' . $itemId, $filename, 'public');

            ReportImage::create([
                'lost_found_item_id' => $itemId,
                'image_path' => $path,
                'order' => $order
            ]);

            $order++;
        }
    }
}
