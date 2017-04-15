<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
//route后市请求类型
Route::get('/', function () {
    return view('quertStu');
});
//"/test"是从根目录出发 根目录在public
Route::any('/test',function(){
	return "hello world";
});


Route::any('/test1','Learn\StudentController@index');
//路由参数{参数名}类似于springMVC中的@pathValue()
// Route::any('/user/{id}',function($id){
// 	return "初始化用户的id是".$id;
// });
// Route::any('user/{name?}',function($name = 'hhh'){


// });

// Route::any('/user/member',)
Route::any('/test1/{id}','Learn\StudentController@showStuInfo');
Route::any('/insert','Learn\StudentController@insertStuInfo');
Route::any('/curl','Learn\StudentController@curl');