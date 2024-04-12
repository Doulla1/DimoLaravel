<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ParticipantController extends Controller
{
    // CRUD methods with try catch and verification of the request and return of the JSON response

    /**
     * Get all participants existing in the database
     *
     * @response array{participant: Participant}
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        try {
            $participants = Participant::all();
            return response()->json(["participants" => $participants], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching the participants', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new participant in the database
     *
     * @param Request $request
     * @response array{participant: Participant}
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'course_id' => 'required|integer',
                'is_currently_present' => 'required|boolean',
            ]);

            $participant = new Participant();
            $participant->user_id = $request->user_id;
            $participant->course_id = $request->course_id;
            $participant->is_currently_present = $request->is_currently_present;
            $participant->save();

            return response()->json(['participant' => $participant], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while creating the participant', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a participant in the database
     *
     * @param int $id
     * @param Request $request
     * @response array{participant: Participant}
     * @return JsonResponse
     */
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'course_id' => 'required|integer',
                'is_currently_present' => 'required|boolean',
            ]);

            $participant = Participant::find($id);
            $participant->user_id = $request->user_id;
            $participant->course_id = $request->course_id;
            $participant->is_currently_present = $request->is_currently_present;
            $participant->save();

            return response()->json(['participant' => $participant], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while updating the participant', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a participant in the database
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $participant = Participant::find($id);
            $participant->delete();
            return response()->json(['message' => 'Participant deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while deleting the participant', 'error' => $e->getMessage()], 500);
        }
    }
}
