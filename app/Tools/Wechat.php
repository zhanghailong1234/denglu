<?php
namespace App\Tools;
use Illuminate\Support\Facades\Cache;
class Wechat{
    const appId="wx315c6407316e90fa";
    const appSecret="e220a728f43129faf22f0e334d6a8998";
	 /**
     * 恢复文本消息
     * @param  [type] $msg    [description]
     * @param  [type] $xmlObj [description]
     * @return [type]         [description]
     */
public static function responseText($msg,$xmlObj){

                echo "<xml>
                     <ToUserName><![CDATA[".$xmlObj->FromUserName."]]></ToUserName>
                    <FromUserName><![CDATA[".$xmlObj->ToUserName."]]></FromUserName>
                    <CreateTime>".time()."</CreateTime>
                     <MsgType><![CDATA[text]]></MsgType>
                     <Content><![CDATA[".$msg."]]></Content>
                    </xml>";die;
}

/**
 * 获取access_token令牌
 * 
 */
public static function getToken(){
//缓存里有数据  直接从缓存读
// Cache::flush();
//$access_token=Cache::get("access_token");
//dd($access_token);
if(empty($access_token)){
//如果token过期了 重新获取
    
    $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".Self::appId."&secret=".Self::appSecret;
     //dd($url);
    //发请求
    $data=file_get_contents($url);
     //dd($data);
    $data=json_decode($data,true);
    
    $access_token=$data['access_token'];
    //dd($access_token);
    //令牌如何储存两小时?
    //数据库Redis  memcache  文件
    Cache::put("access_token",$access_token,7200);
    }
    return $access_token;
    }



    public static function uploadMedia($path,$format){
        //调用接口添加到微信
        $access_token=Wechat::getToken();
        $url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type={$format}";
        //$img="D:/phpstudy_pro/WWW/1.png";
        // dd($img);
        
        $data['media']=new \CURLFile($path);
        $res=Curl::curlPost($url,$data);
        //dd($res);
        $res=json_decode($res,true);
        $mediawechat_id=$res['media_id'];
        return $mediawechat_id;
    }



    public static function getUserInfo($openid){

        //开始开发access_token
            $access_token=self::getToken();
            //调用用户管理-获取用户基本信息接口
            $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid=".$openid."&lang=zh_CN";
            //发请求
            $userInfo=file_get_contents($url);
            $userInfo=json_decode($userInfo,true);
            return $userInfo;
    }


    public static function getWeather($city){
        //调用天气接口
        $url="http://api.k780.com/?app=weather.future&weaid={$city}&&appkey=46494&sign=265c94c3840cf03ef4b7724e56bcc60f&format=json";
                //请求方式get post
                $data=file_get_contents($url);
                //转化成数组
                $data=json_decode($data,true);
                $msg="";
                foreach ($data['result'] as $key => $value) {
                    $msg.=$value['days']." ".$value['citynm']." ".$value['week']." ".$value['temperature']." ".$value['weather'].$value['wind']." ".$value['winp']."\r\n";
                }
                return $msg;
    }


    public static function getOpenid(){
        //先去session里取openid
        $openid=session('openid');
        if(!empty($openid)){
            return $openid;
        }
//微信授权成功后,跳转到咱们配置的地址(回调地址)带一个code参数
        $code=request()->input('code');
        
      if(empty($code)){
        //没有授权跳转到微信服务器进行授权
        $host=$_SERVER['HTTP_HOST'];//域名
        $uri=$_SERVER['REQUEST_URI'];//路由参数
        
         //用户同意授权,获取code
      $redirect_uri=urlencode("http://".$host.$uri);//地址用urencode处理
      
      $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".self::appId."&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
      header("location:".$url);die;
      }else{
        //通过code换取网页授权access_token
      $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".self::appId."&secret=".self::appSecret."&code={$code}&grant_type=authorization_code";
      $data=Curl::curlGet($url);
      $data=json_decode($data,true);
      $openid=$data['openid'];
      session(['openid'=>$openid]);
      return $openid;
      }

      
    }


    public static function getOpenidByUserInfo(){
        $userInfo=session('userInfo');
        if(!empty($userInfo)){
          return $userInfo;
        }

        //微信授权成功后,跳转到咱们配置的地址(回调地址)带一个code参数
        $code=request()->input('code');
        
      if(empty($code)){
        //没有授权跳转到微信服务器进行授权
        $host=$_SERVER['HTTP_HOST'];//域名
        $uri=$_SERVER['REQUEST_URI'];//路由参数
        
         //用户同意授权,获取code
      $redirect_uri=urlencode("http://".$host.$uri);//地址用urencode处理
      
      $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".self::appId."&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
      header("location:".$url);die;
      }else{
        //通过code换取网页授权access_token
      $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".self::appId."&secret=".self::appSecret."&code={$code}&grant_type=authorization_code";
      $data=Curl::curlGet($url);
      $data=json_decode($data,true);
      $openid=$data['openid'];
      $access_token=$data['access_token'];
      $url="https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
      $userInfo=file_get_contents($url);
      $userInfo=json_decode($userInfo,true);
      dd($userInfo);
      session(['userInfo'=>$userInfo]);
      return $userInfo;
          }

      
      }

    }
