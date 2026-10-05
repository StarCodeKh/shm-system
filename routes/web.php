<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

// Single-page app: every web URL is handled by the Vue router.
Route::get('/{any}', ApplicationController::class)->where('any', '.*');
