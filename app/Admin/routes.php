<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    $router->resource('/app/users', AppUserController::class);
    $router->resource('/app/projects', AppProjectController::class);
    $router->resource('/app/projects-nochecked', AppProjectNoCheckedController::class);
    $router->resource('/app/companyreview', AppCompanyreviewController::class);
    $router->post('/app/companyreview/{id}/approve', 'AppCompanyreviewController@approve');
    $router->resource('/app/company', AppCompanyController::class);
    $router->resource('/app/adpublish', AppAdpublishController::class);
    $router->post('/app/adpublish/{id}/approve', 'AppAdpublishController@approve');
    $router->resource('/app/ad', AppAdController::class);
});
