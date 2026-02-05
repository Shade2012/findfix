<?php

namespace App\Repositories;

use App\Interfaces\ItemRepositoryInterface;
use App\Models\LostFoundItem;

class ItemRepository implements ItemRepositoryInterface
{
    public function getAllItems(array $filters = [])
    {
        $query = LostFoundItem::with(['user', 'category', 'building', 'room', 'images'])
            ->where('is_active', true);

        // Search filter
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Building filter
        if (!empty($filters['building_id'])) {
            $query->where('building_id', $filters['building_id']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Date range filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('date_incident', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('date_incident', '<=', $filters['date_to']);
        }

        // Order by most recent
        $query->orderBy('date_incident', 'desc')
              ->orderBy('created_at', 'desc');

        return $query->paginate(15);
    }

    public function getItemById(int $id)
    {
        return LostFoundItem::with(['user', 'category', 'building', 'room', 'images'])
            ->findOrFail($id);
    }

    public function createItem(array $data)
    {
        return LostFoundItem::create($data);
    }

    public function updateItem(int $id, array $data)
    {
        $item = LostFoundItem::findOrFail($id);
        $item->update($data);
        return $item->fresh(['user', 'category', 'building', 'room', 'images']);
    }

    public function deleteItem(int $id)
    {
        $item = LostFoundItem::findOrFail($id);
        return $item->delete();
    }

    public function getUserItems(int $userId)
    {
        return LostFoundItem::with(['category', 'building', 'room', 'images'])
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }
}
