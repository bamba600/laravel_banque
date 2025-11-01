<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Swagger Documentation Routes
|--------------------------------------------------------------------------
|
| Les routes Swagger sont automatiquement enregistrées par L5-Swagger
| Configuration dans config/l5-swagger.php
|
| Routes disponibles:
| - GET /api/documentation - Interface Swagger UI
| - GET /docs - Fichier JSON de la documentation
|
*/
