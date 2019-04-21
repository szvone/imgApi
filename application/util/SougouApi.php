<?php
namespace app\util;


/**
 * Created by Vone.
 * User: vone
 * Date: 2018/9/28
 * Time: 21:32
 */

//搜狗图床API，免登录
class SougouApi{


    public static function Upload(){
        if (isset($_POST['imgBase64'])) {
            $img = $_POST['imgBase64'];
        }else if (isset($_FILES['file'])) {
            $fp = fopen($_FILES["file"]["tmp_name"],"r");
            $img = base64_encode(fread($fp,$_FILES["file"]["size"]));
        }else{
            return array("code"=>"-1","msg"=>"图片格式有误","img"=>null);
        }



        $img = base64_decode($img);


        $data = base64_decode("LS0tLS0tV2ViS2l0Rm9ybUJvdW5kYXJ5R0xmR0IwSGdVTnRwVFQxaw0KQ29udGVudC1EaXNwb3NpdGlvbjogZm9ybS1kYXRhOyBuYW1lPSJwaWNfcGF0aCI7IGZpbGVuYW1lPSIxMS5wbmciDQpDb250ZW50LVR5cGU6IGltYWdlL3BuZw0KDQo=").$img.base64_decode("DQotLS0tLS1XZWJLaXRGb3JtQm91bmRhcnlHTGZHQjBIZ1VOdHBUVDFrLS0NCg==");

        $url = "http://pic.sogou.com/pic/upload_pic.jsp";


        $ch = curl_init();
        $headers=array(
            "Content-Type: multipart/form-data; boundary=----WebKitFormBoundaryGLfGB0HgUNtpTT1k",
            "Content-Length: ".strlen($data)
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


        $result=curl_exec($ch);
        curl_close($ch);
        return array("code"=>"1","msg"=>"上传成功","img"=>$result);

    }
}