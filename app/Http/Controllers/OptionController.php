<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * Get all options
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        try {
            $options = Option::all();
            return response()->json(['options' => $options], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new option.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $option = new Option();
            $option->text = $request->text;
            $option->question_id = $request->question_id;
            $option->is_correct = $request->is_correct;
            $option->save();
            return response()->json(['option' => $option], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update an option.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $option = Option::findOrFail($id);
            $option->text = $request->text;
            $option->question_id = $request->question_id;
            $option->is_correct = $request->is_correct;
            $option->save();
            return response()->json(['option' => $option], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete an option.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $option = Option::findOrFail($id);
            $option->delete();
            return response()->json(['message' => 'Option deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


}
