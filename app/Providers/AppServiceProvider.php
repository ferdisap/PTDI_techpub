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

  public function wp_normalize_path( $path ) {
    $path = str_replace( '\\', '/', $path );
    $path = preg_replace( '|(?<=.)/+|', '/', $path );
    if ( ':' === substr( $path, 1, 1 ) ) {
        $path = ucfirst( $path );
    }
    return $path;
}

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    $path_included = base_path() . DIRECTORY_SEPARATOR . 'ietp_n219' . DIRECTORY_SEPARATOR;
    set_include_path(get_include_path() . PATH_SEPARATOR . $path_included);

    Vite::useBuildDirectory(env('VITE_BUILD_DIR', 'build'));

    define("CSDB_VIEW_PATH", $this->wp_normalize_path(resource_path('views/csdb4')));
    define("CSDB_STORAGE_PATH", storage_path('csdb'));
    
    
    // Blade::anonymousComponentPath(base_path() . DIRECTORY_SEPARATOR . 'ietp_n219' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR);
  }
}
