<?php
namespace app\util;
use think\facade\Cache;

/**
 * Created by Vone.
 * User: vone
 * Date: 2018/9/28
 * Time: 22:10
 */

//新浪图床API，需要登录授权
class SinaApi{


    public static function Upload() {

        $url = "http://picupload.weibo.com/interface/pic_upload.php?cb=https%3A%2F%2Fweibo.com%2Faj%2Fstatic%2Fupimgback.html%3F_wv%3D5%26callback%3DSTK_ijax_1551096206285100&mime=image%2Fjpeg&data=base64&url=weibo.com%2Fu%2F5734329255&markpos=1&logo=1&nick=&marks=0&app=miniblog&s=rdxt&pri=0&file_source=2";


        $post='b64_data='.urlencode(input("imgBase64"));



        if(!Cache::get("SinaCookie")){
            self::sinaLogin(Cache::get("SinaUser"),Cache::get("SinaPass"));
        }

        if (!Cache::get("SinaUpdateTime")){
            Cache::set("SinaUpdateTime",time());
        }
        $UpdateTime = time() - Cache::get("SinaUpdateTime");
        //1小时自动更新一次cookie
        if($UpdateTime > 3600){
            self::sinaLogin(Cache::get("SinaUser"),Cache::get("SinaPass"));
            Cache::set("SinaUpdateTime",time()); // 更新之后时间重置，不然会一直触发。。
        }

        // Curl提交
        $ch = curl_init($url);


        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_VERBOSE => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array("Cookie:" . Cache::get("SinaCookie")),
            CURLOPT_POSTFIELDS => $post,
        ));
        $output = curl_exec($ch);
        curl_close($ch);



        $pid = self::getSubstr($output,"pid=","\r");




        if($pid=="") {
            return array("code"=>"-1","msg"=>"服务器繁忙","img"=>null);
        }
        $size = 0;      //图片尺寸 0-7(数字越大尺寸越大)
        $https = true;  //是否使用 https 协议
        $sizeArr = array('large', 'mw1024', 'mw690', 'bmiddle', 'small', 'thumb180', 'thumbnail', 'square');
        $pid = trim($pid);
        $size = $sizeArr[$size];

        if (preg_match('/^[a-zA-Z0-9]{32}$/', $pid) === 1) {
            $imgUrl =  ($https ? 'https' : 'http') . '://' . ($https ? 'ws' : 'ww')
                . ((crc32($pid) & 3) + 1) . ".sinaimg.cn/" . $size
                . "/$pid." . ($pid[21] === 'g' ? 'gif' : 'jpg');
        }else{
            $url = $pid;
            $imgUrl = preg_replace_callback('/^(https?:\/\/[a-z]{2}\d\.sinaimg\.cn\/)'
                . '(large|bmiddle|mw1024|mw690|small|square|thumb180|thumbnail)'
                . '(\/[a-z0-9]{32}\.(jpg|gif))$/i', function ($match) use ($size) {
                return $match[1] . $size . $match[3];
            }, $url, -1, $count);
            if ($count === 0) {
                $imgUrl = '';
            }
        }

        return array("code"=>"1","msg"=>"上传成功","img"=>$imgUrl);

    }


    public static function sinaLogin($u,$p){
        $url = 'https://login.sina.com.cn/sso/login.php?client=ssologin.js(v1.4.15)&_=';
        $post = 'entry=sso&gateway=1&from=null&savestate=30&useticket=0&pagerefer=&vsnf=1'
               .'&su='.base64_encode($u).'&service=sso&sp='.$p
               .'&sr=1024*768&encoding=UTF-8&cdult=3&domain=sina.com.cn&prelt=0&returntype=TEXT';

        $ret = self::sendPost($url,$post);

        $tmp = explode("\n\r",$ret);



        $res = json_decode($tmp[1]);

        if ($res->retcode=="0"){
            $cookie = 'SUB' . self::getSubstr($tmp[0],"Set-Cookie: SUB",'; ') . ';';
            Cache::set("SinaCookie",$cookie);
            return "登录成功";
        }else{
            return "登录失败";
        }



    }


    private static function sendPost($url,$data){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }


    public static function getSubstr($str,$leftStr,$rightStr){
        $left = strpos($str, $leftStr);
        //echo '左边:'.$left;
        $right = strpos($str, $rightStr,$left);
        //echo '<br>右边:'.$right;
        if($left <= 0 or $right < $left) return '';
        return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
    }
}
