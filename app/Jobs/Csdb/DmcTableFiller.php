<?php

namespace App\Jobs\Csdb;

use App\Models\Csdb\Dmc;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DmcTableFiller implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Create a new job instance.
   */
  public function __construct(
    public Dmc $Dmc,
  )
  {
    //
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    //
  }
}
