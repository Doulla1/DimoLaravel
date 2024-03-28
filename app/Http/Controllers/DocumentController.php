<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Subject;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DocumentController extends Controller
{
    /**
     * Add a document to a subject
     *
     * @param Request $request
     * @response array{documents: Document[]}
     * @return JsonResponse
     * @throws ValidationException
     */
    public function addDocuments(Request $request)
    {
        // Vérification du role de l'utilisateur
        $user = Auth::user();
        if (!$user || !$user->hasRole('teacher')) {
            return response()->json([
                "message" => "You are not a teacher"
            ], 404);
        }
        // Validation des données
        $this->validate($request,[
            'title' => 'string|required',
            'subject_id' => 'integer|required',
        ]);

        // Sauvegarde des fichiers
        if ($request->hasFile('files')) {
            // Parcourir tous les fichiers et les sauvegarder
            $files = $request->file('files');
            $documents = [];
            foreach ($files as $file) {
                $fileName = $file->store('uploads', 'public');
                $document = new Document();
                $document->title = $request->title;
                $document->file_path = $fileName;
                $document->subject_id = $request->subject_id;
                $document->save();
                $documents[] = $document;
            }
            return response()->json(["documents"=>$documents], 200);
        } else {
            return response()->json([
                "message" => "File not found"
            ], 404);
        }
    }

    /**
     * Get all documents of a subject
     *
     * @param $subject_id
     * @response array{documents: Document[]}
     * @return JsonResponse
     */
    public function getDocuments($subject_id): JsonResponse
    {
        try {
            // Vérifier si l'utilisateur a le rôle de teacher ou student ayant cette matière dans son programmme
            $user = Auth::user ();
            if (!$user || (!$user->hasRole ('teacher') && !$user->hasRole ('student'))) {
                return response ()->json ([
                    "message" => "You are not allowed to access this resource"
                ], 403);
            }
            // Si l'utilisateur est un student, vérifier s'il a cette matière dans son programme
            if ($user->hasRole ('student')) {
                $programs = $user->attendedPrograms;
                $subject = Subject::find ($subject_id);
                if (!$subject || !$programs->contains ($subject->program_id)) {
                    return response ()->json ([
                        "message" => "You are not allowed to access this resource"
                    ], 403);
                }
            }
            $documents = Document::where ('subject_id', $subject_id)->get ();
            return response ()->json (["documents" => $documents], 200);
        }
        catch (Exception $e){
            return response()->json([
                "message" => "An error occurred while getting documents" . $e->getMessage()
            ], 500);
        }
    }
}
