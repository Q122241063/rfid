<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


Route::view('/','admin@Rfid');
Route::rule('/ck','admin/CkController/ck','post');

Route::rule('/ml','admin/CkController/ml','post');
Route::rule('/tm','admin/CkController/tm','post');
Route::rule('/qc','admin/CkController/qc','post');
Route::rule('/pl','admin/CkController/pl','post');

Route::rule('/kk','admin/CkController/index');

Route::rule('/ck','admin/CkController/default');

Route::rule('/sav','admin/CkController/tosave','post');

Route::rule('/inde','admin/CkController/inde');