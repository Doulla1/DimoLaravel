<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LobbyController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\Skin2Controller;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Routes accéssibles à tout le monde
Route::get ('/', function () {
    return response()->json(['message' => 'Welcome to the API of DimoVR.'], 200);
});
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// Récupérer toutes les programmes
Route::get('/programs', [ProgramController::class, 'getAll']);
// Récupérer un programme par son id
Route::get('/programs/{id}', [ProgramController::class, 'getById']);
// Récupérer les matières d'un programme
Route::get('/programs/{id}/subjects', [ProgramController::class, 'getSubjectsByProgram']);
// Récupérer une matière par son id
Route::get('/subjects/{id}', [SubjectController::class, 'getById']);



// Routes accessibles à tout ceux qui sont connectés
Route::middleware('auth:sanctum')->group(function () {
    // Récupérer les informations de l'utilisateur connecté
    Route::get('/fetchUser',[UserController::class, 'getConnectedUser']);

    // Récupère un questionnaire par son id
    Route::get('/questionnaires/{id}', [QuestionnaireController::class, 'getUnique']);

    // Récupérer les documents d'une matière
    Route::get('/documents/{subject_id}', [DocumentController::class, 'getDocuments']);

    // Mettre à jour les informations de l'utilisateur connecté
    Route::put('/updateUser', [UserController::class, 'updateConnectedUser']);

    // Mettre à jour le mot de passe de l'utilisateur connecté
    Route::put('/updateUserPassword', [UserController::class, 'updatePassword']);

    // Récupérer les cours d'une matière
    Route::get('/courses/subject/{subject_id}', [CourseController::class, 'getBySubject']);

    // Récupérer son skin
    Route::get('/skin', [Skin2Controller::class, 'getByUser']);

    // Mettre à jour son skin
    Route::put('/skin', [Skin2Controller::class, 'update']);

    // Récupère le nombre de participants à un cours
    Route::get('/courses/{course_id}/participants', [CourseController::class, 'getParticipants']);
});

// Routes accessibles aux students
Route::middleware(["auth:sanctum", "checkrole:student"])->group(function () {
    // Récupérer les programmes auxquels le student connecté est inscrit
    Route::get('/student-programs', [ProgramController::class, 'getByConnectedStudent']);

    // Vérifier si un student est inscrit à un programme
    Route::get('/is-registered/{programId}', [ProgramController::class, 'isRegistered']);

    // S'inscrire à un programme
    Route::post('/programs/register', [ProgramController::class, 'registerStudent']);

    // Récupérer les questionnaires d'une matière auquel le student est inscrit
    Route::get('/student-questionnaires/{subjectId}', [QuestionnaireController::class, 'getByConnectedStudent']);

    // Enregistrer les réponses d'un questionnaire
    Route::post('/questionnaires/answers', [QuestionnaireController::class, 'saveAnswers']);

    // Consulter les résultats d'un questionnaire
    Route::get('/questionnaires/{questionnaireId}/results', [QuestionnaireController::class, 'getScoreOfQuestionnaire']);

    // Consulter les résultats des questionnaires passés
    Route::get('/results', [QuestionnaireController::class, 'getScoreOfAllQuestionnaires']);

    // Savoir si un questionnaire est traité
    Route::get('/treated/{questionnaireId}', [QuestionnaireController::class, 'getTreatedQuestionnaire']);

    // Consulter son emploi du temps (liste des cours)
    Route::get('/student-courses', [CourseController::class, 'getByConnectedStudent']);

    // Rejoindre un cours en tant que participant
    Route::post('/join-course', [CourseController::class, 'joinCourse']);

    // Quitter un cours en tant que participant
    Route::post('/leave-course', [CourseController::class, 'leaveCourse']);

});

// Routes accessibles aux teachers
Route::middleware(["auth:sanctum", "checkrole:teacher"])->group(function () {
    // Récupérer tous les cours
    Route::get('/courses', [CourseController::class, 'getAll']);

    // Créer un cours
    Route::post('/courses', [CourseController::class, 'create']);

    // Commencer un cours
    Route::put('/courses/{course_id}/start', [CourseController::class, 'start']);

    // Terminer un cours
    Route::put('/courses/{course_id}/end', [CourseController::class, 'end']);

    // Récupérer les cours du teacher connecté
    Route::get('/teached-courses', [CourseController::class, 'getByConnectedTeacher']);

    // Mettre à jour un cours qui appartient au teacher connecté
    Route::put('/courses/{id}', [CourseController::class, 'update']);

    // Supprimer un cours qui appartient au teacher connecté
    Route::delete('/courses/{id}', [CourseController::class, 'delete']);

    // Créer un programme
    Route::post('/programs', [ProgramController::class, 'create']);

    // Récupérer les programs où le teacher connecté est responsable
    Route::get('/programs/iamheadteacher', [ProgramController::class, 'getByConnectedHeadTeacher']);

    // Récupérer les programs du teacher connecté
    Route::get('/teached-programs', [ProgramController::class, 'getByConnectedTeacher']);

    // Mettre à jour un programme
    Route::put('/programs/{id}', [ProgramController::class, 'update']);

    // Récupérer les étudiants d'un programme
    Route::get('/programs/{id}/students', [ProgramController::class, 'getStudents']);

    // Créer une matière
    Route::post('/subjects', [SubjectController::class, 'create']);

    // Modifier une matière
    Route::put('/subjects/{id}', [SubjectController::class, 'update']);

    // Ajouter un document à une matière
    Route::post('/add-document', [DocumentController::class, 'addDocument']);

    // Supprimer un document
    Route::delete('/documents/{id}', [DocumentController::class, 'deleteDocument']);

    // Rejoindre une matière en tant que teacher
    Route::post('/join-subject', [SubjectController::class, 'joinSubject']);

    // Quitter une matière en tant que teacher
    Route::post('/leave-subject', [SubjectController::class, 'leaveSubject']);

    // Supprimer une matière
    Route::delete('/subjects/{id}', [SubjectController::class, 'delete']);

    // CRUD des options

    // Récupérer toutes les options
    Route::get('/options', [OptionController::class, 'getAll']);

    // Récupérer une option par son id
    Route::get('/options/{id}', [OptionController::class, 'getUnique']);

    // Créer une option
    Route::post('/options', [OptionController::class, 'create']);

    // Mettre à jour une option
    Route::put('/options/{id}', [OptionController::class, 'update']);

    // Supprimer une option
    Route::delete('/options/{id}', [OptionController::class, 'delete']);

    // CRUD des questions

    // Récupérer toutes les questions
    Route::get('/questions', [QuestionController::class, 'getAll']);

    // Récupérer une question par son id
    Route::get('/questions/{id}', [QuestionController::class, 'getUnique']);

    // Créer une question
    Route::post('/questions', [QuestionController::class, 'create']);

    // Mettre à jour une question
    Route::put('/questions/{id}', [QuestionController::class, 'update']);

    // Supprimer une question
    Route::delete('/questions/{id}', [QuestionController::class, 'delete']);

    // CRUD des des questionnaires

    // Récupérer tous les questionnaires
    Route::get('/questionnaires', [QuestionnaireController::class, 'getAll']);

    // Récupérer les questionnaire par matière
    Route::get('/questionnaires/subject/{subject_id}', [QuestionnaireController::class, 'getBySubject']);

    // Créer un questionnaire
    Route::post('/questionnaires', [QuestionnaireController::class, 'create']);

    // Créer un questionnaire avec des questions et des options
    Route::post('/questionnaires/create-full', [QuestionnaireController::class, 'createFull']);

    // Mettre à jour un questionnaire
    Route::put('/questionnaires/{id}', [QuestionnaireController::class, 'update']);

    // Make a questionnaire available to students.
    Route::put('/questionnaires/{id}/publish', [QuestionnaireController::class, 'makeAvailable']);

    // Make a questionnaire unavailable to students.
    Route::put('/questionnaires/{id}/unpublish', [QuestionnaireController::class, 'makeUnavailable']);

    // Supprimer un questionnaire
    Route::delete('/questionnaires/{id}', [QuestionnaireController::class, 'delete']);

});

// Routes accessibles qu'aux admins
Route::middleware(["auth:sanctum", "checkrole:admin"])->group(function () {

    // Inscrire un nouveau professeur
    Route::post('/admin/register-teacher', [RegisterController::class, 'registerTeacher']);
    // Récupérer tous les utilisateurs
    Route::get ('/admin/users',[UserController::class, 'getAll']);

    // Récupérer tous les utilisateurs en attente d'être assignés à un rôle
    Route::get('/admin/users/pending', [UserController::class, 'getPending']);

    // Récupérer un utilisateur par son id
    Route::get('/admin/users/{id}', [UserController::class, 'getUnique']);

    // Récupérer un utilisateur par son email
    Route::get('/admin/users/email/{email}', [UserController::class, 'getByEmail']);

    // Mettre à jour un utilisateur
    Route::put('/admin/users/{id}', [UserController::class, 'update']);

    // Route pour supprimer un utilisateur
    Route::delete('/admin/users/{id}', [UserController::class, 'delete']);

    // Récupérer tous les rôles
    Route::get('/admin/roles', [UserController::class, 'getRoles']);

    // Attribuer un rôle à un utilisateur
    Route::post('/admin/assign-role', [UserController::class, 'assignRole']);

    // Récuperer un cours par son id
    Route::get('/admin/courses/{id}', [CourseController::class, 'getById']);

    // Récupérer les cours d'un teacher
    Route::get('/admin/courses/teacher/{teacherId}', [CourseController::class, 'getByTeacherId']);

    // Supprimer un programme
    Route::delete('/admin/programs/{id}', [ProgramController::class, 'destroy']);

    // Récupérer tous les participants de la table participants
    Route::get('/admin/participants', [ParticipantController::class, 'getAll']);

    // Créer un nouveau participant en tant qu'admin
    Route::post('/admin/participants', [ParticipantController::class, 'create']);

    // Mettre à jour un participant en tant qu'admin
    Route::put('/admin/participants/{id}', [ParticipantController::class, 'update']);

    // Supprimer un participant en tant qu'admin
    Route::delete('/admin/participants/{id}', [ParticipantController::class, 'delete']);

    // Récupérer tous les lobbies
    Route::get('/admin/lobbies', [LobbyController::class, 'getAll']);

    // Mettre à jour un lobby
    Route::put('/admin/lobbies/{id}', [LobbyController::class, 'update']);

});
