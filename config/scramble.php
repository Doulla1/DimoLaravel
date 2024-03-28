<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    /*
     * Your API path. By default, all routes starting with this path will be added to the docs.
     * If you need to change this behavior, you can add your custom routes resolver using `Scramble::routes()`.
     */
    'api_path' => 'api',

    /*
     * Your API domain. By default, app domain is used. This is also a part of the default API routes
     * matcher, so when implementing your own, make sure you use this config if needed.
     */
    'api_domain' => null,

    /**
     * The path where your OpenAPI specification will be exported.
     */
    'export_path' => 'api.json',

    /*
     * Define the theme of the documentation.
     * Available options are `light` and `dark`.
     */
    'theme' => 'light',

    'info' => [
        /*
         * API version.
         */
        'version' => env('API_VERSION', '0.0.1'),

        /*
         * Description rendered on the home page of the API documentation (`/docs/api`).
         */
        'description' =>
            "## Fonctionnalités de l'API DimoVR

- **Anonyme :**
  - Consultation des programmes disponibles.
  - Consultation des matières de chaque programme.
  - Inscription à l'école avec envoi d'email de confirmation.

- **Inscrits :**
  - Consultation et modification des informations de compte.
  - Création de skins personnalisés.

- **Étudiants :**
  - S'inscrire à un programme (avec envoi d'email de confirmation).
  - Consultation des emplois du temps (cours).
  - Consultation et téléchargement des documents des matières.
  - Consultation des questionnaires rendus accessibles par les profs.
  - Remplissage et soumission de questionnaires avec notation automatique (en pourcentage).
  - Consultation des notes des questionnaires rendus.

- **Profs :**
  - Création de programmes d'étude (automatiquement chef de département).
  - Consultation de la liste des élèves inscrits à un programme.
  - Gestion des matières (CRUD).
  - Gestion des cours dans une matière (CRUD).
  - Débuter et finir s cours (limite de 5 cours actifs).
  - Ajout de documents dans une matière.
  - Rejoindre une matière en tant que prof.
  - Création de questionnaires.
  - Ajout de questionnaires à une matière.
  - Consultation des documents des programmes d'études.
  - Gestion de l'accès aux questionnaires des matières pour les étudiants.

- **Admins :**
  - Inscription des profs avec envoi d'email contenant le mot de passe au professeur.
  - Gestion de toutes les données de l'application.
",
    ],

    /*
     * Customize Stoplight Elements UI
     */
    'ui' => [
        /*
         * Hide the `Try It` feature. Enabled by default.
         */
        'hide_try_it' => false,

        /*
         * URL to an image that displays as a small square logo next to the title, above the table of contents.
         */
        'logo' => '',

        /*
         * Use to fetch the credential policy for the Try It feature. Options are: omit, include (default), and same-origin
         */
        'try_it_credentials_policy' => 'include',
    ],

    /*
     * The list of servers of the API. By default, when `null`, server URL will be created from
     * `scramble.api_path` and `scramble.api_domain` config variables. When providing an array, you
     * will need to specify the local server URL manually (if needed).
     *
     * Example of non-default config (final URLs are generated using Laravel `url` helper):
     *
     * ```php
     * 'servers' => [
     *     'Live' => 'api',
     *     'Prod' => 'https://scramble.dedoc.co/api',
     * ],
     * ```
     */
    'servers' => [
          'Local' => 'api',
          'Prod' => 'https://api.dimovr.com/api',
      ],

    'middleware' => [
        'web',
        //Décommenter la ligne ci-dessous pour activer la restriction d'accès à la documentation
        //RestrictedDocsAccess::class,
    ],

    'extensions' => [],
];
