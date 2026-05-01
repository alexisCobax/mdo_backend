<?php

use App\Http\Controllers\GhlTokenController;
use App\Http\Controllers\GhlTemplateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GoHighLevel Routes (módulo aislado)
|--------------------------------------------------------------------------
|
| Endpoints aislados para tokens y templates de GHL.
| No toca ningún controller ni ruta existente.
|
*/

/* --- Tokens --- */
Route::get('/ghl/token', [GhlTokenController::class, 'getToken']);
Route::get('/ghl/token-status', [GhlTokenController::class, 'tokenStatus']);
Route::get('/ghl/refresh-template-token', [GhlTokenController::class, 'refreshTemplateToken']);

/* OAuth completo (solo si el refresh_token expiró) */
Route::get('/ghl/authorize', [GhlTokenController::class, 'authorizeUrl']);
Route::get('/ghl/callback', [GhlTokenController::class, 'callback']);
Route::post('/ghl/exchange', [GhlTokenController::class, 'exchangeCodeEndpoint']);

/* --- Templates / Webhook --- */
Route::post('/ghl/webhook/nuevos-arribos-por-marca', [GhlTemplateController::class, 'enviarArribosPorMarca']);
Route::get('/ghl/preview/nuevos-arribos-por-marca', [GhlTemplateController::class, 'previewArribosPorMarca']);

// TEMPORAL: diagnóstico y sincronización. ELIMINAR después de usar.
Route::get('/ghl/debug', [GhlTokenController::class, 'debug']);
Route::get('/ghl/prod-config', [GhlTokenController::class, 'prodConfig']);
Route::post('/ghl/sync-from-prod', [GhlTokenController::class, 'syncFromProd']);
