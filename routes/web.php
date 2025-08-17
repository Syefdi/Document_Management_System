<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AngularController;
use App\Http\Controllers\DocumentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('angular');
// });
    Route::get('/debug/index-content', [DocumentController::class, 'debugIndexContent']);
    Route::get('/debug/reindex/{documentId}', [DocumentController::class, 'forceReindexDocument']);
    Route::post('/debug/populate-index', [DocumentController::class, 'manuallyPopulateIndex']);
    Route::get('/debug/extractor/{documentId}', [DocumentController::class, 'debugFileExtractor']);
Route::post('/debug/fix-paths', [DocumentController::class, 'fixDocumentPaths']);
Route::get('/debug/test-reindex/{documentId}', [DocumentController::class, 'testReindexWithCorrectPath']);
Route::post('/debug/reindex-all', [DocumentController::class, 'reindexAllDocuments']);

Route::any('/{any}', [AngularController::class, 'index'])
    ->where('any', '^(?!api).*$')
    ->where('any', '^(?!install|update).*$');
    // ->where('any', '^(?!update).*$');
// Route::get('/category', [CategoryController::class, 'index']);
// Route::get('/category',function(){
// });
