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
| Routes pour la documentation Swagger UI
|
*/
Route::get('/api/documentation', function () {
    return view('vendor.l5-swagger.index', [
        'documentation' => 'default',
        'urlToDocs' => url('/storage/api-docs/api-docs.json'),
        'operationsSorter' => null,
        'configUrl' => null,
        'validatorUrl' => null,
        'useAbsolutePath' => false
    ]);
})->name('l5-swagger.default.api');

Route::get('/docs/asset/{asset}', function ($asset) {
    $path = base_path('vendor/swagger-api/swagger-ui/dist/' . $asset);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path, [
        'Content-Type' => mime_content_type($path),
        'Cache-Control' => 'public, max-age=31536000'
    ]);
})->where('asset', '.*');
