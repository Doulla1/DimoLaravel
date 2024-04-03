<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Get all questions
     *
     * @response array{questions: Question[]}
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        try {
            $questions = Question::all();
            $questions->load('options');
            return response()->json(['questions' => $questions], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a question by id.
     *
     * @param int $id
     * @response array{question: Question}
     * @return JsonResponse
     */
    public function getUnique(int $id): JsonResponse
    {
        try {
            $question = Question::findOrFail($id);
            $question->load('options');
            return response()->json(['question' => $question], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * Create a new question.
     *
     * @param Request $request
     * @response array{question: Question}
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $question = new Question();
            $question->text = $request->text;
            $question->questionnaire_id = $request->questionnaire_id;
            $question->order = $request->order;
            $question->save();
            return response()->json(['question' => $question], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a question.
     *
     * @param Request $request
     * @param int $id
     * @response array{question: Question}
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $question = Question::findOrFail($id);
            $question->text = $request->text;
            $question->questionnaire_id = $request->questionnaire_id;
            $question->order = $request->order;
            $question->save();
            return response()->json(['question' => $question], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a question.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $question = Question::findOrFail($id);
            $question->delete();
            return response()->json(['message' => 'Question deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


}
