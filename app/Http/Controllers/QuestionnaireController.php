<?php

namespace App\Http\Controllers;

use App\Models\Questionnaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    // CRUD for questionnaires with try-catch and JsonResponse

    /**
     * Get all questionnaires
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        try {
            $questionnaires = Questionnaire::all();
            $questionnaires->load('questions');
            return response()->json(['questionnaires' => $questionnaires], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new questionnaire.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $questionnaire = new Questionnaire();
            $questionnaire->title = $request->title;
            $questionnaire->description = $request->description;
            $questionnaire->lesson_id = $request->lesson_id;
            $questionnaire->save();
            return response()->json(['questionnaire' => $questionnaire], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a questionnaire.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $questionnaire = Questionnaire::findOrFail($id);
            $questionnaire->title = $request->title;
            $questionnaire->description = $request->description;
            $questionnaire->lesson_id = $request->lesson_id;
            $questionnaire->save();
            return response()->json(['questionnaire' => $questionnaire], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a questionnaire.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $questionnaire = Questionnaire::findOrFail($id);
            $questionnaire->delete();
            return response()->json(['message' => 'Questionnaire deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
