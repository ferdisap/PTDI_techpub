<?php

namespace App\Providers;

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
    $path_included = base_path() . DIRECTORY_SEPARATOR . 'ietp' . DIRECTORY_SEPARATOR;
    set_include_path(get_include_path() . PATH_SEPARATOR . $path_included);
  }
}
