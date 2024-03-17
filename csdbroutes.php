<?php 

/**
 * Run this app before deploying app
 */

require __DIR__.'/vendor/autoload.php';

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

// instance app
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$response = $kernel->handle(
  $request = Request::capture()
);

// get routes
$allRoutes = Controller::getAllRoutesNamed();

// write routes
$path = resource_path('others/routes.json');
$file = file_put_contents($path, json_encode($allRoutes));

// terminate
$kernel->terminate($request, $response);

return;
