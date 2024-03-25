<?php

namespace App\Http\Controllers;

use App\Models\ItemType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemTypeController extends Controller
{

    /**
     * Get all item types
     *
     * @return JsonResponse
     */
    public function getAll()
    {
        try {
            $itemTypes = ItemType::all();
            return response()->json($itemTypes);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new item type
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $itemType = new ItemType();
            $itemType->name = $request->name;
            $itemType->save();
            return response()->json($itemType);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a single item type
     *
     * @return JsonResponse
     */
    public function get($id)
    {
        try {
            $itemType = ItemType::find($id);
            return response()->json($itemType);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update an item type
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $itemType = ItemType::find($id);
            $itemType->name = $request->name;
            $itemType->save();
            return response()->json($itemType);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete an item type
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $itemType = ItemType::find($id);
            $itemType->delete();
            return response()->json(['message' => 'Item type deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }



}
