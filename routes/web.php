<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

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
| Routes pour la documentation Swagger UI
|
*/
Route::get('/api/documentation', function () {
    return view('vendor.l5-swagger.index', [
        'documentation' => 'default',
        'urlToDocs' => url('/api/docs'),
        'operationsSorter' => null,
        'configUrl' => null,
        'validatorUrl' => null,
        'useAbsolutePath' => true
    ]);
})->name('l5-swagger.default.api');

// Route pour le fichier JSON Swagger
Route::get('/api/docs', function () {
    $filePath = storage_path('api-docs/api-docs.json');
    if (!file_exists($filePath)) {
        abort(404, 'Documentation file not found');
    }
    return response()->file($filePath, [
        'Content-Type' => 'application/json',
        'Access-Control-Allow-Origin' => '*'
    ]);
})->name('l5-swagger.default.docs');

Route::get('/api/docs/api-docs.json', function () {
    $filePath = storage_path('api-docs/api-docs.json');
    if (!file_exists($filePath)) {
        abort(404, 'Documentation file not found');
    }
    return response()->file($filePath, [
        'Content-Type' => 'application/json',
        'Access-Control-Allow-Origin' => '*'
    ]);
})->name('l5-swagger.default.json');

Route::get('/api/docs/asset/{asset}', function ($asset) {
    try {
        $path = public_path('vendor/l5-swagger/' . $asset);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
            'Cache-Control' => 'public, max-age=31536000'
        ]);
    } catch (\Exception $e) {
        Log::error('Swagger asset error: ' . $e->getMessage());
        abort(500);
    }
})->name('l5-swagger.default.asset')->where('asset', '.*');
