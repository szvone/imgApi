<?php
namespace app\index\controller;

use app\util\SinaApi;
use app\util\SougouApi;
use think\facade\Cache;
use think\facade\Session;


class Index
{
    public function index()
    {
        return '超简图床 -- 为您提供Api服务！';
    }



    public function upImg(){
        // 指定允许其他域名访问
        header("Access-Control-Allow-Origin: *");

        if (!Cache::has("admin")){
            Cache::set("admin","123456");
            Cache::set("SinaUser","");
            Cache::set("SinaPass","");
            Cache::set("admin","");
            Cache::set("key","123456");
            Cache::set("type","1");
        }

        $key = Cache::get("key");
        if ($key!=input("key")){
            return json(array("code"=>"-1","msg"=>"通讯密钥错误","img"=>null));
        }

        $type = Cache::get("type");
        if ($type == 1){
            //使用搜狗图床
            $res = SougouApi::Upload();
            return json($res);
        }else if ($type == 2){
            //使用新浪图床
            $res = SinaApi::Upload();
            return json($res);
        }else{
            return json(array("code"=>"-1","msg"=>"类型错误","img"=>null));
        }


    }

    public function setConfig(){
        if (Session::has("admin")){
            Cache::set("SinaUser",input("SinaUser"));
            Cache::set("SinaPass",input("SinaPass"));
            Cache::set("admin",input("admin"));
            Cache::set("key",input("key"));
            Cache::set("type",input("type"));
            return json(array("code"=>1,"msg"=>"操作成功！"));
        }else{
            return json(array("code"=>-1,"msg"=>"请登录！"));
        }

    }

    public function getConfig(){
        if (!Cache::has("admin")){
            Cache::set("admin","123456");
            Cache::set("SinaUser","");
            Cache::set("SinaPass","");
            Cache::set("admin","");
            Cache::set("key","123456");
            Cache::set("type","1");
        }
        if (Session::has("admin")){
            return json(array("code"=>1,"msg"=>"拉取成功","data"=>array(
                "SinaUser"=>Cache::get("SinaUser"),
                "SinaPass"=>Cache::get("SinaPass"),
                "admin"=>Cache::get("admin"),
                "key"=>Cache::get("key"),
                "type"=>Cache::get("type"),
            )));
        }else{
            return json(array("code"=>-1,"msg"=>"请登录！"));
        }
    }

    public function login(){
        $pass = input("pass");
        if ($pass == Cache::get("admin")){
            Session::set("admin","1");
            return json(array("code"=>1,"msg"=>"登录成功！"));
        }else{
            return json(array("code"=>-1,"msg"=>"管理员密码错误！"));
        }
    }

    public function loginOut(){
        Session::clear();
        return json(array("code"=>1,"msg"=>"操作成功！"));
    }

    //上传文件
    public function upload(){


        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        $info = $file->validate(['size'=>2555678,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            return json(array("code"=>"1","msg"=>"上传成功","img"=>$info->getSaveName()));
        }else{
            return json(array("code"=>"-1","msg"=>"类型错误:"+getError(),"img"=>null));
        }
    }

}
