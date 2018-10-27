
超简Api图床  —— 专为Api而生
===============


超简Api图床 是基于ThinkPHP 5.1实现的一套Api图床程序，主要包含以下特色：

 + 无数据库模式，简单配置，一键搭建
 + 第三方接口接入，不占用服务器空间
 + 接入搜狗Api平台，无需配置，全球CDN加速，永久不限量图片存储
 + 接入新浪Api平台，无需配置，全球CDN加速，永久不限量图片存储
 + 支持服务器存储模式，代替普通图床
 + 超简单Api使用，提供统一Api实现图片上传
 + 通讯密钥过滤恶意上传
 + 支持跨域提交访问
 + 免费、开源
 + 支持简单返回，直接返回图片网址

> 超简Api图床的运行环境要求PHP5.6以上。

> 超简图床Java版本已经发布，欢迎使用【https://github.com/szvone/imgApiJava】

## 安装

 + 下载本套系统源码，解压至网站根目录
 + 访问 http://localhost/public/ 进入主页
 + 点击系统设置，进入设置页面，进行系统的首次配置，并修改管理员密码和通讯密钥
 + 默认管理密码为：123456
 + 默认通讯密钥为：123456
 + 保存配置后，即可开始使用

 > 如果登陆提示成功后还是一直弹出登陆，请修改php.ini 里面的 always_populate_raw_post_data = -1  （去掉前面的;）
 
 > 升级说明：请您直接下载新版本覆盖旧版本即可！
 
 > 如果页面显示【超简图床 -- 为您提供Api服务！】，说明您的服务器默认配置的首页为index.php,请您访问localhost/public/index.html进入主页

## 使用

 + 根据主页显示的Api接口，调用Api接口，将会返回对应的图片地址
 + 使用主页提供的测试工具，手动选择图片上传，会显示对应的图片地址

 > 如果您忘记密码，请您删除runtime下面的cache目录下面的所有文件夹，即可重置配置 

## Api接口说明
 + 请求地址：http://localhost/api  (localhost请自行替换成您的域名)
 + 请求方式：POST
 + 请求参数：
   + key=通讯密钥  （后台设置的通讯密钥，默认为123456）
   + imgBase64=需要上传图片的base64编码（请对该字段使用urlencode编码）
   + onlyUrl=0 （传入1则调用接口只会返回图片地址，传入其他或者不传会返回完整的json数据）
   
 + 返回数据：
 
    {"code":1,"msg":"操作成功","img":"http://img04.sogoucdn.com/app/a/100520146/d8e8b0f277d98fefaf73391f3e502ac7"}
    
    + code：返回1代表成功，-1代表失败
    + msg：返回接口调用的具体说明
    + img：失败返回null，成功返回图片的图床网址
 

## 注意

 + 如果图床模式为服务器存储，请务必参考下面的资料将public/uploads目录设定为无权限执行PHP程序目录，保证服务器的安全性
   + https://blog.csdn.net/alen_xiaoxin/article/details/60783141
   + https://www.jb51.net/article/94061.htm

## 更新记录
 + v1.3（2018.10.27）
   + 修复新浪图床上传图片失败的bug
   + java版本超简图床发布，使用SpringBoot构建，如果PHP版本使用遇到兼容问题，请您尝试使用Java版
 
 + v1.2（2018.10.16）
   + 增加新浪图床账号cookie自动保持在线机制
   + java版本超简图床准备开发，将使用Springboot构建，敬请期待

 + v1.1（2018.10.08）
   + Api增加新参数onlyUrl,
     + 当onlyUrl为1时，如果上传成功，只返回图片链接，如果上传失败，则返回字符串 null
     + 当onlyUrl不为1或者不传入时，返回内容同v1.0版本，请参照Api文档
   + 增加开发者调用示例，请在SDK目录查看
   + 修复已知Bug


 + v1.0（2018.10.02） 
   + 初版发布

## 版权信息

超简Api图床遵循 MIT License 开源协议发布，并提供免费使用。


版权所有Copyright © 28 by vone (http://szvone.cn)

All rights reserved。

