<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
   // protected $table = 'student_info';
   // public $timestamps = false;
   public function getStuInfo($id){
        $data = DB::table('student_info')->where('student_id',$id)->get();
        if(count($data) > 0){
            dd($data);
            return true;
        }else{

            $data1 = DB::table('student_info')->where('student_class',$id)->get();
            if($data1 == null){
                return false;
            }
            else{
                dd($data1);
            }
        }
        
   }
   public function saveClassInfo($info){
        $db = DB::table('student_info')->insertGetId($info);
        if($db){
           dd($info);
        }else{
            echo '储存失败';
        }
   }
  // public function showClassInfo(){

  // }

}
?>
