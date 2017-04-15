<?php

namespace App\Http\Controllers\Learn;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Student;

class StudentController extends Controller
{
	public $stu;
    public $length;
	public function __construct(){
		if($this->stu == null){
			$this->stu = new Student();
		}
	}

    public function curl(Request $request){

        $id = $request->get('id');
        $data = $this->stu->getStuInfo($id);
        if($data){
           echo '结果已经查询到';
        }else{
         if(strlen($id) < 10){
            $url = "http://jwzx.cqupt.edu.cn/jwzxtmp/showBjStu.php?bj=";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url.$id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); //是否抓取跳转后的页面
            $result = curl_exec($ch);
            curl_close($ch);
            $this->fliter($result);
        }else{
             $url = "http://jwzx.cqupt.edu.cn/jwzxtmp/kebiao/kb_stu.php?xh=";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url.$id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); //是否抓取跳转后的页面
            $result = curl_exec($ch);
            curl_close($ch);
            $this->getCourse($result);
        }
    }
}

    //匹配班级信息
    public function fliter($result){

        $pattern = "/<td[^>]*>([^<>]*)<\/td>/";
        preg_match_all($pattern,$result,$matches_result);
        $this->length = count($matches_result[1]);
        $number = ($this->length / 10) -1;//学生数量
        echo $number;
        //print_r($matches_result);
        // var_dump($matches_result);
        for($i = 1; $i <= $number; $i++){
            $m = $i * 10;
            //$info['number'][$i] = $matches_result[1][$m];
            $info['student_id'] = $matches_result[1][$m + 1];
            $info['student_name'] = $matches_result[1][$m + 2];
            $info['student_sex'] = $matches_result[1][$m + 3];
            $info['student_class'] = $matches_result[1][$m + 4];
            $info['student_major_id'] = $matches_result[1][$m + 5];
            $info['student_major_name'] = $matches_result[1][$m + 6];
            $info['student_college'] = $matches_result[1][$m + 7];
            $info['student_grade'] = $matches_result[1][$m + 8];
            $info['student_status'] = $matches_result[1][$m + 9];
            $this->stu->saveClassInfo($info);
        }

        
    }
//匹配个人课表 但是写的不对
    public function getCourse($result){
       
        $pattern1 = "/<td[^>]+>([\S\s]*?)<\/td>/";
        $pattern3 = "/<span[^>]+>([\s\S]*?)<\/span>/";
        $pattern2 = "/<hr>([\S\s]*?)/";
        preg_match_all($pattern1,$result,$matches_result);
        
        $new = array();
        $new1 = array();
        $new2 = array();
        $matches_course_name = array();
        $matches_course_room = array();
        $matches_course_teacher = array();
        $matches_course_teacher2 = array();
        $course = array();
        //去除多余数据
        for($i = 0; $i < 71; $i++){
            if(empty($matches_result[1][$i])){
                $new[$i] = $matches_result[1][$i];
            }else{
                $matches_result[1][$i] = explode('<br>', $matches_result[1][$i]);
                $new[$i] = $matches_result[1][$i];
            }

         }
         foreach ($new as $key => $value) {
            if(empty($new[$key])){
                continue;
            }else{
                 foreach ($new[$key] as $key1 => $value1) {
                    $new[$key][$key1] = explode('<font color=#FF0000', $new[$key][$key1]);  
                    
            }
            
         }
    }   
    //将三维数组转化为二维数组
         for($i = 0; $i<count($new); $i++){
            if($new[$i] == ""){
                $new1[$i] = $new[$i];
            }else{
                $m = 0;
                for($j = 0; $j < count($new[$i]);$j++){
                    
                    for($k = 0;$k < count($new[$i][$j]); $k++){
                        $temp = $new[$i][$j][$k];
                        $new1[$i][$m] = $temp;
                        $m++;

                    }
                }
            }
         }
        // var_dump($new1);
        //去除span 去除hr
        for($i = 0; $i < count($new1); $i++) {
            if(empty($new1[$i]) || count($new1[$i]) <6){
                continue;
            }
            else{   
                    if(count($new1[$i]) > 7){
                        preg_match_all($pattern3,$new1[$i][11],$matches);
                        $new1[$i][11] = $matches[1][0];
                        preg_match_all($pattern3,$new1[$i][5],$matches);
                        $new1[$i][5] = $matches[1][0];
                        for($j = 0; $j <count($new1[$i]); $j++){
                            $matches_hr[$i] = explode('hr>', $new1[$i][$j]);
                            if(count($matches_hr[$i]) > 1){
                                $new1[$i][6] = $matches_hr[$i][1];
                            }
                            
                        }

                    }else{
                        preg_match_all($pattern3,$new1[$i][5],$matches);
                        $new1[$i][5] = $matches[1][0];
                }
            }
            
        }
        // var_dump($new1);
        for($i = 0; $i < count($new1); $i++){
            if(count($new1[$i]) < 2 || empty($new1[$i])){
                continue;
            }else{
                    

                    if(count($new1[$i]) > 7){
                        //匹配课程名字和编号
                        $matches_course_name[$i] = explode('-', $new1[$i][7]);
                        $new1[$i][7] = $matches_course_name[$i][0];
                        $new1[$i][10] = $matches_course_name[$i][1];
                        //匹配教室
                        $matches_course_room[$i] = explode('：', $new1[$i][8]);
                        $new1[$i][8] = $matches_course_room[$i][1];
                        
                    }
                    //匹配课程名字和编号
                    $matches_course_name[$i] = explode('-', $new1[$i][1]);
                    $new1[$i][1] = $matches_course_name[$i][0];
                    $new1[$i][4] = $matches_course_name[$i][1];
                    //匹配教室
                    $matches_course_room[$i] = explode('：', $new1[$i][2]);
                    $new1[$i][2] = $matches_course_room[$i][1];
                    //匹配教师 学分 及课程类型
                    $matches_course_teacher[$i] = explode(' ',$new1[$i][5]);
                    $count = count($new1[$i]);
                    for($k = 0; $k < count($matches_course_teacher[$i]); $k++){

                        if(empty($matches_course_teacher[$i][$k])){
                            continue;
                        }else if(count($new1[$i]) < 13){
                            $new1[$i][$count - 2] = $matches_course_teacher[$i][$k];
                            $count++;
                    }

                }

            }
            
       } 
       var_dump($new1);
    }

}
?>
