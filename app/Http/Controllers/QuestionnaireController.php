<?php

namespace App\Http\Controllers;

use App\Models\Questionnaire;
use App\Models\Response;
use App\Models\Statistic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // Si l'utilsateur est un étudiant, vérifier si le questionnaire est visible et s'il fait partie des questionnaires d'une matière dont l'étudiant est inscrit au programme
        $user = Auth::user();
        $questionnaire = Questionnaire::findOrFail($id);
        if($user->hasRole('student')){
            try {
                $questionnaire = Questionnaire::where('is_visible', true)
                    ->where('id', $id)
                    ->whereHas('subject.program', function ($query) {
                        $query->whereHas('students', function ($query) {
                            $query->where('user_id', auth()->user()->id);
                        });
                    })
                    ->first();
                if(!$questionnaire){
                    return response()->json(['message' => 'Questionnaire not found'], 404);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => "You are not allowed to access this document" . $e->getMessage()], 403);
            }
        }
        elseif ($user->hasRole('teacher') || $user->hasRole('admin')){
            $questionnaire = Questionnaire::findOrFail($id);
        }
        elseif (!$user->hasRole('teacher') && !$user->hasRole('admin')){
            return response()->json(['message' => "You are not allowed to access this document", 403]);
        }
        // Retourner le questionnaire
        return response()->json(['questionnaire' => $questionnaire]);
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
                ->whereHas('subject.program', function ($query) {
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
     * Get Questionnaire by Subject
     *
     * @param Request $request
     * @response array{questionnaire: Questionnaire}
     * @return JsonResponse
     */
    public function getBySubject(int $subjectId): JsonResponse
    {
        try {
            $questionnaires = Questionnaire::where('subject_id', $subjectId)->get();
            return response()->json(['questionnaires' => $questionnaires], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * Create a full questionnaire with questions and options
     *
     * @param Request $request
     * @response array{questionnaire: Questionnaire}
     * @return JsonResponse
     */
    public function createFull(Request $request): JsonResponse
    {
        //return response()->json(['requete' => $request->all()], 501);
        try {
            // validate the request
            $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'subject_id' => 'required|integer',
                'questions' => 'required|array',
                'questions.*.text' => 'required|string',
                'questions.*.order' => 'required|integer',
                'questions.*.options' => 'required|array',
                'questions.*.options.*.text' => 'required|string',
            ]);
            // Create the questionnaire
            $questionnaire = new Questionnaire();
            $questionnaire->title = $request->title;
            $questionnaire->description = $request->description;
            $questionnaire->subject_id = $request->subject_id;
            $questionnaire->save();

            foreach ($request->questions as $question) {
                $newQuestion = $questionnaire->questions()->create([
                    'text' => $question['text'],
                    'order' => $question['order'],
                ]);
                foreach ($question['options'] as $option) {
                    $newQuestion->options()->create([
                        'text' => $option['text'],
                        'is_correct' => $option['is_correct'] == "true",
                    ]);
                }
            }
            $questionnaire->load('questions');
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
     * Chaque bonne réponse vaut 1 point, chaque mauvaise réponse retire 1 point
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
                'id' => 'required|integer',
                'questions' => 'required|array',
                'questions.*.id' => 'required|integer',
                'questions.*.options' => 'required|array',
                'questions.*.options.*.id' => 'required|integer',
                'questions.*.options.*.selected' => 'required|boolean',
            ]);
            // Trouver le questionnaire
            $questionnaire = Questionnaire::findOrFail($request->id);

            // Vérifier si le questionnaaire n'a pas déjà été traité
            $statistic = Statistic::where('user_id', auth()->user()->id)
                ->where('questionnaire_id', $questionnaire->id)
                ->first();
            if ($statistic) {
                return response()->json(['message' => 'Questionnaire already treated'], 400);
            }

            $score = 0;
            // Pour chaque question répondue
            foreach ($request->questions as $questionAnswered) {
                // Pour chaque option
                foreach ($questionAnswered['options'] as $optionAnswered) {
                    // Si l'option est sélectionnée
                    if ($optionAnswered['selected'] == "true") {
                        // Enregistrer la réponse
                        $response = new Response();
                        $response->user_id = auth()->user()->id;
                        $response->question_id = $questionAnswered['id'];
                        $response->option_id = $optionAnswered['id'];
                        $response->save();
                    }
                }

                // Corriger le questionnaire
                $question = $questionnaire->questions->where('id', $questionAnswered['id'])->first();
                // Pour chaque option de $questionAnswered qui est correcte, ajouter 1 point
                // Pour chaque option de $questionAnswered qui est incorrecte, retirer 1 point
                foreach ($questionAnswered['options'] as $optionAnswered) {
                    $option = $question->options->where('id', $optionAnswered['id'])->first();
                    if ($option->is_correct && $optionAnswered['selected']) {
                        $score++;
                    } elseif (!$option->is_correct && $optionAnswered['selected']) {
                        $score--;
                    }
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
            $statistic->result = max($score, 0);
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
     * Get the score of a student for all questionnaires with the answers given.
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
                    $answers = Response::where('user_id', auth()->user()->id)
                        ->whereHas('question', function ($query) use ($questionnaire) {
                            $query->where('questionnaire_id', $questionnaire->id);
                        })
                        ->get();
                    $score = ($statistic->result / $statistic->total_correct_answers) * 100;
                    $scores[$questionnaire->title] = ["score"=>$score, "questionnaire"=>$questionnaire, "answers"=>$answers];
                }
            }
            return response()->json(['scores' => $scores], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a questionnaire already treated by a student with the answers given or null if questionnaire not treated.
     *
     * @param int $questionnaireId
     * @return JsonResponse
     */
    public function getTreatedQuestionnaire(int $questionnaireId): JsonResponse
    {
        try {
            $questionnaire = Questionnaire::findOrFail($questionnaireId);
            $statistic = Statistic::where('user_id', auth()->user()->id)
                ->where('questionnaire_id', $questionnaireId)
                ->first();
            if (!$statistic) {
                return response()->json(['message' => 'No score found'], 404);
            }
            $answers = Response::where('user_id', auth()->user()->id)
                ->whereHas('question', function ($query) use ($questionnaire) {
                    $query->where('questionnaire_id', $questionnaire->id);
                })
                ->get();
            $score = ($statistic->result / $statistic->total_correct_answers) * 100;
            return response()->json(['questionnaire' => $questionnaire, 'answers' => $answers, "score" => $score], 200);
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
