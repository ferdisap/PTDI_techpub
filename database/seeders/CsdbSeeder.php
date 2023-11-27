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
      $lists = Controller::get_file(storage_path('app/csdb'));
      foreach($lists as $obj){
        if(!Csdb::where('path',"csdb/{$obj}")->latest('updated_at')->first('id')){
          Csdb::create([
            'path' => "csdb/{$obj}",
            'description' => 'none',
            'status' => "seeded",
            'initiator_id' => 1,
          ]);
        }
      }
    }
}
