<?php

namespace App\Models\Csdb;

use App\Models\Code;
use App\Models\Csdb;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class History extends Model
{
  use HasFactory;

  protected $table = 'history';

  protected $hidden = ['owner_class', 'owner_id'];

  public $timestamps = false;

  // protected $fillable = ['code', 'description', 'user_id', 'csdb_id'];
  // protected $fillable = ['code', 'description', 'owner_id', 'owner_class'];
  protected $fillable = ['code', 'description', 'owner_id', 'owner_class','created_at'];

  public static function generateWhereRawQueryString(Array $historyCode = [], string $classModel) :string
  {
    $model = new $classModel;
    $table = $model->getTable();
    if(!($length = count($historyCode) > 0)) return '';
    $query = "{$table}.id IN ( SELECT history.owner_id FROM history WHERE ";
    $query .= "(";
    for ($i=0; $i < $length; $i++) { 
      if(!(Code::where('name',$historyCode[$i])->first('id'))) return '';
      $query .= "history.code = '{$historyCode[$i]}'";
      if(isset($historyCode[$i+1])) $query .= " OR ";
    }
    $query .= ")";
    $query .= "AND history.created_at = (SELECT MAX(history.created_at) FROM history WHERE history.owner_id = {$table}.id AND history.owner_class = '".str_replace("\\", "\\\\",$classModel)."')";
    $query .= ")";
    return $query;
  }

  /**
   * save semua HISTORYModel yang masuk kedalam parameter fungsi
   * @return bool
   */
  public static function saveModel(Array $HISTORYModels) :bool
  {
    // $HISTORYModels = func_get_args();
    $length = count($HISTORYModels);
    for ($i=0; $i < $length; $i++) { 
      if(!($HISTORYModels[$i]->save())){
        for ($x=$i-1; $x >= 0; $x--) { 
          $HISTORYModels[$x]->delete();
          return false;
        }
      }
    }
    return true;
  }

  public static function revert_saveModel(Array $HISTORYModels) :bool
  {
    // $HISTORYModels = func_get_args();
    $length = count($HISTORYModels);
    $length = func_num_args();
    for ($i=0; $i < $length; $i++) { 
      if(!($HISTORYModels[$i]->delete())){
        for ($x=$i-1; $x >= 0; $x--) { 
          $HISTORYModels[$x]->save();
          return false;
        }
      }
    }
    return true;
  }

  /**
   * @return {App\Models\Csdb\History}
   */
  private static function make_history(Code $CODEModel, string $description, string $owner_id, string $owner_class) :History
  {
    $HISTORYModel = new self();
    $HISTORYModel->code = $CODEModel->name;
    $HISTORYModel->description = $description;
    $HISTORYModel->owner_id = $owner_id;
    $HISTORYModel->owner_class = $owner_class;
    $HISTORYModel->created_at = now()->format('Y-m-d H:i:s'); // format sama dengan class Csdb
    return $HISTORYModel;
  }

  ############################# START of CSDB HISTORY FUNCTION #############################

  /**
   * Delete CSDB Object
   */
  public static function MAKE_CSDB_DELL_History(Csdb $CSDBModel, string $description = '')
  {
    $CODEModel = Code::where('name', 'CSDB-DELL')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description;
    }
    return self::make_history($CODEModel, $description, $CSDBModel->id, get_class($CSDBModel));
  }

  /**
   * Permanent delete CSDB Object
   */
  public static function MAKE_CSDB_PDEL_History(Csdb $CSDBModel, string $description = '')
  {
    $CODEModel = Code::where('name', 'CSDB-PDEL')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description;
    }
    return self::make_history($CODEModel, $description, $CSDBModel->id, get_class($CSDBModel));
  }

  /**
   * Create CSDB Object
   */
  public static function MAKE_CSDB_CRBT_History(Csdb $CSDBModel, string $description = '')
  {
    $CODEModel = Code::where('name', 'CSDB-CRBT')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description;
    }
    return self::make_history($CODEModel, $description, $CSDBModel->id, get_class($CSDBModel));
  }

  /**
   * Update CSDB Object
   */
  public static function MAKE_CSDB_UPDT_History(Csdb $CSDBModel, string $description = '')
  {
    $CODEModel = Code::where('name', 'CSDB-UPDT')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description;
    }
    return self::make_history($CODEModel, $description, $CSDBModel->id, get_class($CSDBModel));
  }

  /**
   * Update path CSDB Object
   */
  public static function MAKE_CSDB_PATH_History(Csdb $CSDBModel, string $description = '')
  {
    $CODEModel = Code::where('name', 'CSDB-PATH')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description ."(". $CSDBModel->path .")";
    }
    return self::make_history($CODEModel, $description, $CSDBModel->id, get_class($CSDBModel));
  }

  /**
   * Update storage CSDB Object
   */
  public static function MAKE_CSDB_STRG_History(Csdb $CSDBModel, string $description = '')
  {
    $CODEModel = Code::where('name', 'CSDB-STRG')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description ."(". $CSDBModel->storage .")";
    }
    return self::make_history($CODEModel, $description, $CSDBModel->id, get_class($CSDBModel));
  }

  /**
   * Restore storage CSDB Object
   */
  public static function MAKE_CSDB_RSTR_History(Csdb $CSDBModel, string $description = '')
  {
    $CODEModel = Code::where('name', 'CSDB-RSTR')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description;
    }
    return self::make_history($CODEModel, $description, $CSDBModel->id, get_class($CSDBModel));
  }
  
  /**
   * Create DDN CSDB Object
   */
  public static function MAKE_CSDB_DDNC_History(Csdb $CSDBModel, string $description = '')
  {
    $CODEModel = Code::where('name', 'CSDB-DDNC')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description;
    }
    return self::make_history($CODEModel, $description, $CSDBModel->id, get_class($CSDBModel));
  }

  /**
   * CSDB Object has imported to requester storage
   */
  public static function MAKE_CSDB_IMPT_History(Csdb $CSDBModel, string $description = '')
  {
    $CODEModel = Code::where('name', 'CSDB-IMPT')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description . "(" .request()->user()->id . ")";
    }
    return self::make_history($CODEModel, $description, $CSDBModel->id, get_class($CSDBModel));
  }

############################# END of CSDB HISTORY FUNCTION #############################
############################# START of USER HISTORY FUNCTION #############################
  /**
   * User create CSDB Object
   */
  public static function MAKE_USER_CRBT_History(User $USERModel, string $description = '', string $CSDBFilename = '')
  {
    $CODEModel = Code::where('name', 'USER-CRBT')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description. "(". $CSDBFilename . ")";
    }
    return self::make_history($CODEModel, $description, $USERModel->id, get_class($USERModel));
  }

  /**
   * User create CSDB Object
   */
  public static function MAKE_USER_UPDT_History(User $USERModel, string $description = '', string $CSDBFilename = '')
  {
    $CODEModel = Code::where('name', 'USER-UPDT')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description. "(". $CSDBFilename . ")";
    }
    return self::make_history($CODEModel, $description, $USERModel->id, get_class($USERModel));
  }

  /**
   * User DELL CSDB Object
   */
  public static function MAKE_USER_DELL_History(User $USERModel, string $description = '', string $CSDBFilename = '')
  {
    $CODEModel = Code::where('name', 'USER-DELL')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description. "(". $CSDBFilename . ")";
    }
    return self::make_history($CODEModel, $description, $USERModel->id, get_class($USERModel));
  }

  /**
   * User permanent delete CSDB Object
   */
  public static function MAKE_USER_PDEL_History(User $USERModel, string $description = '', string $CSDBFilename = '')
  {
    $CODEModel = Code::where('name', 'USER-PDEL')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description. "(". $CSDBFilename . ")";
    }
    return self::make_history($CODEModel, $description, $USERModel->id, get_class($USERModel));
  }

  /**
   * User update path CSDB Object
   */
  public static function MAKE_USER_PATH_History(User $USERModel, string $description = '', string $CSDBFilename = '')
  {
    $CODEModel = Code::where('name', 'USER-PATH')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description. "(". $CSDBFilename . ")";
    }
    return self::make_history($CODEModel, $description, $USERModel->id, get_class($USERModel));
  }

  /**
   * User update storage CSDB Object
   */
  public static function MAKE_USER_STRG_History(User $USERModel, string $description = '', string $CSDBFilename = '')
  {
    $CODEModel = Code::where('name', 'USER-STRG')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description. "(". $CSDBFilename . ")";
    }
    return self::make_history($CODEModel, $description, $USERModel->id, get_class($USERModel));
  }

  /**
   * User restore storage CSDB Object
   */
  public static function MAKE_USER_RSTR_History(User $USERModel, string $description = '', string $CSDBFilename = '')
  {
    $CODEModel = Code::where('name', 'USER-RSTR')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description. "(". $CSDBFilename . ")";
    }
    return self::make_history($CODEModel, $description, $USERModel->id, get_class($USERModel));
  }

  /**
   * User create DDN CSDB Object
   */
  public static function MAKE_USER_DDNC_History(User $USERModel, string $description = '', string $CSDBFilename = '')
  {
    $CODEModel = Code::where('name', 'USER-DDNC')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description. "(". $CSDBFilename . ")";
    }
    return self::make_history($CODEModel, $description, $USERModel->id, get_class($USERModel));
  }

  /**
   * User import CSDB Object
   */
  public static function MAKE_USER_IMPT_History(User $USERModel, string $description = '', string $CSDBFilename = '')
  {
    $CODEModel = Code::where('name', 'USER-IMPT')->first(["id","name",'description']);
    if($CODEModel){
      if(!$description) $description = $CODEModel->description. "(". $CSDBFilename . ")";
    }
    return self::make_history($CODEModel, $description, $USERModel->id, get_class($USERModel));
  }

  ############################# END of USER HISTORY FUNCTION #############################
}
