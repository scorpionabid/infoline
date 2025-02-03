<?php

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Debug\ExceptionHandler;
use App\Http\Kernel;
use App\Console\Kernel as ConsoleKernelAlias;
use App\Exceptions\Handler as ExceptionHandlerAlias;

$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    HttpKernel::class,
    Kernel::class
);

$app->singleton(
    ConsoleKernel::class,
    ConsoleKernelAlias::class
);

$app->singleton(
    ExceptionHandler::class,
    ExceptionHandlerAlias::class
);

return $app;
