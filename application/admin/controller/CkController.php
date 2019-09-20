<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use Exception;

class CkController extends Controller
{
    
    //扫描条形码
    public function tm()
    {
            // 设置脚本最大运行时间
            set_time_limit(0);
            // 设置串口信息
            exec('mode COM3:baud=115200 data=8 stop=1 parity=n xon=on');
            //打开串口
            $ck = dio_open('COM3', O_RDWR);
            //写入命令3e79206e0d0a
            dio_write($ck, "\x3e\x6a\x20\x0d\x0a");
            // dio_write($ck, "\x3e\x79\x20\x6e\x0d\x0a");  

            $data = dio_read($ck,10);
            $data = ltrim($data," >");
            dio_close($ck);
            return $data;       
    }

    //清除数据
    public function qc(){
        // 设置脚本最大运行时间
        set_time_limit(0);
        // 设置串口信息
        exec('mode COM3:baud=115200 data=8 stop=1 parity=n xon=on');
        // 打开串口
        $ck = dio_open('COM3', O_RDWR);
        //写入清除命令
        dio_write ($ck, "\x3e\x63\x0d\x0a");        
        dio_close($ck);
        return '清除成功';
    }

    public static function pl3(int $port=1){
        while(true){
            try{
                // ...
            }catch(\Exception $e){
                self::pl2(++$port);
            }
        }
    }

    public static function pl2(int $port=1){
        try{
            exec("mode COM{$port}:baud=115200 data=8 stop=1 parity=n xon=on");
            $ck = dio_open("COM{$port}", O_RDWR);

            //获取字符数量
            dio_write($ck, "\x3e\x79\x20\x6e\x0d\x0a");

            $i = 6;
            $data = "";
            while(true) {
                $data = dio_read($ck, $i);
                if (strlen(trim(substr($data, -1)))) {
                    dio_write($ck, "\x3e\x79\x20\x6e\x0d\x0a");
                    $i ++;
                } else {
                    break;
                }           
            }
            $data = trim($data);
            $data = substr($data, 3);
            // dd($data);
            $data = $data * 28;  
            // dd($data);   
            //写入命令
            dio_write ($ck, "\x3e\x79\x20\x74\x0d\x0a");        
            $data = dio_read($ck,$data);
            
            $data = trim($data);
            $data = ltrim($data,">");
            
            return explode(">",$data);
        }catch(\Exception $e){
            pl2(++$port);
        }
    }

    //获取数据
    public function pl(){

        // 设置脚本最大运行时间
        set_time_limit(0);       
        // 设置串口信息       
        // 打开串口
        try{          
             exec('mode COM5:baud=115200 data=8 stop=1 parity=n xon=on');
             $ck = dio_open('COM5', O_RDWR);
        }catch(\Exception $e){  
            // return '111';
            exec('mode COM3:baud=115200 data=8 stop=1 parity=n xon=on');
             $ck = dio_open('COM3', O_RDWR);
        }    
        //获取字符数量
        dio_write($ck, "\x3e\x79\x20\x6e\x0d\x0a");

        $i = 6;
        $data = "";
		while(true) {
			$data = dio_read($ck, $i);
			if (strlen(trim(substr($data, -1)))) {
				dio_write($ck, "\x3e\x79\x20\x6e\x0d\x0a");
				$i ++;
			} else {
				break;
			}			
        }
        $data = trim($data);
        $data = substr($data, 3);
        // dd($data);
        $data = $data * 28;  
        // dd($data);   
        //写入命令
        dio_write ($ck, "\x3e\x79\x20\x74\x0d\x0a");        
        $data = dio_read($ck,$data);
        
        $data = trim($data);
        $data = ltrim($data,">");
        
        return explode(">",$data);
    }



    //上传数据
    public function tosave(){

        set_time_limit(0);
        // 设置串口信息
        exec('mode COM3:baud=115200 data=8 stop=1 parity=n xon=on');
        // 打开串口
        $ck = dio_open('COM3', O_RDWR);
        //获取字符数量
        dio_write($ck, "\x3e\x79\x20\x6e\x0d\x0a");
        $i = 6;
        $data = "";
		while(true) {
			$data = dio_read($ck, $i);
			if (strlen(trim(substr($data, -1)))) {
				dio_write($ck, "\x3e\x79\x20\x6e\x0d\x0a");
				$i ++;
			} else {
				break;
			}			
        }
        $data = trim($data);
        $data = substr($data, 2);
        // dd($data);
        $data = $data * 28; 
        //写入命令
        dio_write ($ck, "\x3e\x79\x20\x74\x0d\x0a");        
        $data = dio_read($ck,$data);

        dio_close($ck);
        $data = trim($data);
        $data = ltrim($data,">");
        // $data =  explode(">",$data);
        // 获取数据 -> 保存
    //    $response = $this->respon('http://148.70.197.72',$data);
       $response = $this->respon('http://www.maintain.com/rfid',$data);

        if ($response) {
            return '上传成功';
        } else{
            return "上传失败,请重新上传!";
        }
    }

    /**
         * 发送请求方法
         *
         * @param string $url      请求地址
         * @param array $data      请求数据
         * @param array $headers   请求头
         * @return string|array
         */
        function respon($url, $data = [], $headers = [])
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            $response = curl_exec($ch) ? curl_multi_getcontent($ch) : '';
            curl_close($ch);
            return $response;
        }


 
}
