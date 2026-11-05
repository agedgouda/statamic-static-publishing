<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Publish Control Panel Edits
    |--------------------------------------------------------------------------
    |
    | When enabled, saving anything in the Control Panel commits the changed
    | content files and pushes them to GitHub, which rebuilds the public site.
    | Only turn this on for the CMS server, never for local development.
    |
    */

    'enabled' => (bool) env('CONTENT_PUBLISH_ENABLED', false),

    'remote' => env('CONTENT_PUBLISH_REMOTE', 'origin'),

    // Defaults to the branch the server has checked out.
    'branch' => env('CONTENT_PUBLISH_BRANCH'),

    // Only these paths are ever committed (users and their passwords are not).
    'paths' => ['content', 'public/assets'],

    // Used when a change has no logged-in user, such as a console command.
    'fallback_author' => [
        'name' => env('CONTENT_PUBLISH_AUTHOR_NAME', 'Website Editor'),
        'email' => env('CONTENT_PUBLISH_AUTHOR_EMAIL', 'editor@'.(parse_url((string) env('APP_URL'), PHP_URL_HOST) ?: 'localhost')),
    ],

];
