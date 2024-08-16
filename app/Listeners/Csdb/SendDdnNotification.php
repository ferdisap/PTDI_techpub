<?php

namespace App\Listeners\Csdb;

use App\Events\Csdb\DdnCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendDdnNotification
{
  /**
   * Create the event listener.
   */
  public function __construct()
  {
  }

  /**
   * Handle the event.
   */
  public function handle(DdnCreated $event): void
  {
    dd('bb');
    // $csdb = $event->csdb;
    // $csdb->path = 'FOO';
    // $csdb->save();
    // dd('bb');
    // \App\Models\User::factory()->count(3)->create();
    // dd($event);
  }
}
