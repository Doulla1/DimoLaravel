<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Get all items
     *
     * @return JsonResponse
     */
    public function getAll()
    {
        try {
            $items = Item::all();
            return response()->json(["items"=>$items]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new item
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $item = new Item();
            $item->name = $request->name;
            $item->save();
            return response()->json($item);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a single item
     *
     * @return JsonResponse
     */
    public function get($id)
    {
        try {
            $item = Item::find($id);
            return response()->json($item);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update an item
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $item = Item::find($id);
            $item->name = $request->name;
            $item->save();
            return response()->json($item);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete an item
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $item = Item::find($id);
            $item->delete();
            return response()->json($item);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
