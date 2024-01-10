<?php

namespace Database\Seeders;

use App\Http\Controllers\Controller;
use App\Models\Csdb;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CsdbSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $lists = Controller::get_file(storage_path('app/csdb/MALE'));
    $lists = (array_filter($lists, fn ($v) => str_contains($v, '.')));
    foreach ($lists as $obj) {
      if (!Csdb::where('path', "csdb/{$obj}")->latest('updated_at')->first('id')) {
        $model = Csdb::create([
          'filename' => $obj,
          'path' => "csdb/MALE",
          'status' => 'new',
          'description' => '',
          'initiator_id' => 1,
          'project_name' => 'MALE',
        ]);
        $model->setRemarks('title');
      }
    }
  }
}