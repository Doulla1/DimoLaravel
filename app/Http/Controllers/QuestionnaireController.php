<?php

namespace App\Http\Controllers;

use App\Models\Questionnaire;
use App\Models\Response;
use App\Models\Statistic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    // CRUD for questionnaires with try-catch and JsonResponse

    /**
     * Get all questionnaires
     *
     * @response array{questionnaires: Questionnaire[]}
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
     * Get a questionnaire by id.
     *
     * @param int $id
     * @response array{questionnaire: Questionnaire}
     * @return JsonResponse
     */
    public function getUnique(int $id): JsonResponse
    {
        try {
            $questionnaire = Questionnaire::findOrFail($id);
            $questionnaire->load('questions');
            return response()->json(['questionnaire' => $questionnaire], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * Get all questionnaires for a subject from a program where the current user is a student
     *
     * @param int $subjectId
     * @response array{questionnaires: Questionnaire[]}
     * @return JsonResponse
     */
    public function getByConnectedStudent(int $subjectId): JsonResponse
    {
        try {
            // Retrouver tous les questionnaires dont la matière fait partie d'un programme où l'étudiant est inscrit
            $questionnaires = Questionnaire::where('subject_id', $subjectId)
                ->where('is_visible', true)
                ->whereHas('subject.programs', function ($query) {
                    $query->whereHas('students', function ($query) {
                        $query->where('user_id', auth()->user()->id);
                    });
                })
                ->get();
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
     * @response array{questionnaire: Questionnaire}
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // validate the request
            $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'subject_id' => 'required|integer',
            ]);
            $questionnaire = new Questionnaire();
            $questionnaire->title = $request->title;
            $questionnaire->description = $request->description;
            $questionnaire->subject_id = $request->subject_id;
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
     * @response array{questionnaire: Questionnaire}
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            //validate the request
            $request->validate([
                'title' => 'string',
                'description' => 'string',
                'subject_id' => 'integer',
            ]);
            $questionnaire = Questionnaire::findOrFail($id);
            $questionnaire->title = $request->title;
            $questionnaire->description = $request->description;
            $questionnaire->subject_id = $request->subject_id;
            $questionnaire->save();
            return response()->json(['questionnaire' => $questionnaire], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Correct a questionnaire sent by a student and save it.
     *
     * Chaque bonne réponse vaut 1 point.
     *
     * @param Request $request
     * @response array{score: int}
     * @return JsonResponse
     */
    public function saveAnswers(Request $request): JsonResponse
    {
        try {
            //valider the request
            $request->validate([
                'questionnaire_id' => 'required|integer',
                'answers' => 'required|array',
                'answers.*.question_id' => 'required|integer',
                'answers.*.option_id' => 'required|integer',
            ]);
            // Trouver le questionnaire
            $questionnaire = Questionnaire::findOrFail($request->questionnaire_id);

            $score = 0;
            foreach ($request->answers as $answer) {
                // Enregistrer la réponse
                $response = new Response();
                $response->user_id = auth()->user()->id;
                $response->question_id = $answer['question_id'];
                $response->option_id = $answer['option_id'];
                $response->save();

                // Corriger le questionnaire
                $question = $questionnaire->questions->where('id', $answer['question_id'])->first();
                if ($question->options->where('id', $answer['option_id'])->first()->is_correct) {
                    $score++;
                }
            }
            // Calculer le total réponses correctes
            $totalCorrectAnswers = $questionnaire->questions->sum(function ($question) {
                return $question->options->where('is_correct', true)->count();
            });

            // Enregistrer le score du questionnaire dans la table des statistiques
            $statistic = new Statistic();
            $statistic->user_id = auth()->user()->id;
            $statistic->questionnaire_id = $questionnaire->id;
            $statistic->result = $score;
            $statistic->total_correct_answers = $totalCorrectAnswers;
            $statistic->save();

            // Calculer le score à envoyer en pourcentage
            $score = ($score / $totalCorrectAnswers) * 100;

            // Retourner le score à l'étudiant
            return response()->json(['score' => $score], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Make a questionnaire available to students.
     *
     * @param int $questionnaireId
     * @response array{message: string}
     * @return JsonResponse
     */
    public function makeAvailable(int $questionnaireId): JsonResponse
    {
        try {
            $questionnaire = Questionnaire::findOrFail($questionnaireId);
            $questionnaire->is_visible = true;
            $questionnaire->save();
            return response()->json(['message' => 'Questionnaire is now available'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Make a questionnaire unavailable to students.
     *
     * @param int $questionnaireId
     * @response array{message: string}
     * @return JsonResponse
     */
    public function makeUnavailable(int $questionnaireId): JsonResponse
    {
        try {
            $questionnaire = Questionnaire::findOrFail($questionnaireId);
            $questionnaire->is_visible = false;
            $questionnaire->save();
            return response()->json(['message' => 'Questionnaire is now unavailable'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the score of a student for a questionnaire.
     *
     * @param int $questionnaireId
     * @response array{score: int}
     * @return JsonResponse
     */
    public function getScoreOfQuestionnaire(int $questionnaireId): JsonResponse
    {
        try {
            $questionnaire = Questionnaire::findOrFail($questionnaireId);
            $statistic = Statistic::where('user_id', auth()->user()->id)
                ->where('questionnaire_id', $questionnaireId)
                ->first();
            if (!$statistic) {
                return response()->json(['message' => 'No score found'], 404);
            }
            $score = ($statistic->result / $statistic->total_correct_answers) * 100;
            return response()->json(['score' => $score ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the score of a student for all questionnaires.
     *
     * @response array{scores: array}
     * @return JsonResponse
     */
    public function getScoreOfAllQuestionnaires(): JsonResponse
    {
        try {
            $questionnaires = Questionnaire::all();
            $scores = [];
            foreach ($questionnaires as $questionnaire) {
                $statistic = Statistic::where('user_id', auth()->user()->id)
                    ->where('questionnaire_id', $questionnaire->id)
                    ->first();
                if ($statistic) {
                    $score = ($statistic->result / $statistic->total_correct_answers) * 100;
                    $scores[$questionnaire->title] = $score;
                }
            }
            return response()->json(['scores' => $scores], 200);
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
