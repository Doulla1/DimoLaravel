<?php

namespace App\Http\Controllers;

use App\Models\SkinPart;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkinPartController extends Controller
{
    /**
     * Get all SkinParts
     *
     * @return JsonResponse
     */
    public function getAll()
    {
        try {
            $skinParts = SkinPart::all();
            return response()->json(["skinParts"=>$skinParts], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Create a new SkinPart
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $skinPart = new SkinPart();
            $skinPart->name = $request->name;
            $skinPart->save();
            return response()->json(["skinPart"=>$skinPart], 201);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Get SkinPart by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getById(int $id): JsonResponse
    {
        try {
            $skinPart = SkinPart::find($id);
            return response()->json(["skinPart"=>$skinPart], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Update SkinPart by ID
     *
     * @param  Request  $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $skinPart = SkinPart::find($id);
            $skinPart->name = $request->name;
            $skinPart->save();
            return response()->json(["skinPart"=>$skinPart], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Remove SkinPart by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $skinPart = SkinPart::find($id);
            $skinPart->delete();
            return response()->json('SkinPart deleted', 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
