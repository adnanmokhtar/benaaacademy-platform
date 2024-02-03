<?php

Route::group([
    "prefix" => ADMIN,
    "middleware" => ["web", "auth:backend"],
    "namespace" => "Dot\\Platform\\Controllers"
], function ($route) {
    $route->any('/', ["as" => "admin", "uses" => function () {
        $redirect_path = config("admin.default_path");
        return redirect(ADMIN . "/" . trim($redirect_path));
    }]);
});

Route::group([
    "middleware" => ["web", "auth:backend"]
], function ($route) {
    $route->get('docs', function () {
        $user = Auth::user();
        if (is_null($user->api_token)) {
            return app()->abort(500, "No API Token assigned to the current user.");
        }
        return view('docs.api.index', ["user" => $user]);
    });
});


Route::group([
    "prefix" => ADMIN,
    "middleware" => ["web", "auth:backend"],
    "namespace" => "Dot\\Platform\\Controllers"
], function ($route) {
    $route->any('/platform/update', ["as" => "admin.options.check_update", "uses" => "OptionsController@check_update"]);
});
