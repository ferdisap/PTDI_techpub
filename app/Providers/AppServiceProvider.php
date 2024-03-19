<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    $path_included = base_path() . DIRECTORY_SEPARATOR . 'ietp_n219' . DIRECTORY_SEPARATOR;
    set_include_path(get_include_path() . PATH_SEPARATOR . $path_included);

    Vite::useBuildDirectory(env('VITE_BUILD_DIR', 'build'));
    
    
    // Blade::anonymousComponentPath(base_path() . DIRECTORY_SEPARATOR . 'ietp_n219' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR);
  }
}
