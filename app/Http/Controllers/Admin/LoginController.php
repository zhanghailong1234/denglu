<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Cache;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use Session;
use App\Tools\Curl;
use App\Tools\Wechat;
class LoginController extends Controller
{
    public function login(){

    return view('admin/login');
    }

    public function loginDo(){
    	$sessionId=Session::getId();
    	// dd($sessionId);
    	$data=request()->except('_token');
    	//dd($data);
    	$login_time=time();
    	//dd($data);
    	$info=UserModel::where('account',$data['account'])->first();
    	//dd($info);
       if(!empty($info)){

        $error_num=$info['error_num'];
        $last_error_time=$info['last_error_time'];
          $where=[
            ['id','=',$info['id']]
                 ];
             if($info['user_pwd']==$data['user_pwd']){
              if($error_num>=3&&time()-$last_error_time<3600){
              $mins=60-ceil((time()-$last_error_time)/60);
               
               
                return back()->withErrors(['账号已锁定请于'.$mins.'分钟后登录!']);
            }
            session()->put('info',$info);
             
        // dd(session()->get('info'));
            //清零
            $res=UserModel::where($where)->update(['error_num'=>0,'last_error_time'=>0,'sessionId'=>$sessionId,'login_time'=>$login_time]);
          	
          	return redirect('admin/index');
          	//return go(1)->with(['账号或密码有误您还有2次机会!']);
        }else{
            //累加
               if(time()-$last_error_time>3600){
            $res=UserModel::where($where)->update(['error_num'=>1,'last_error_time'=>time()]);
            
            
            return back()->withErrors(['账号或密码有误您还有2次机会!']);
        }else{
            if($error_num>=3){
                $mins=60-ceil((time()-$last_error_time)/60);
               
               
                return back()->withErrors(['账号已锁定请于'.$mins.'分钟后登录!']);
            }else{
                $res=UserModel::where($where)->update(['error_num'=>$error_num+1,'last_error_time'=>time()]);
                $last_num=3-($error_num+1);
                if($res){
              
                 return back()->withErrors(['账号或密码错误,您还有'.$last_num.'次机会!']);  
                }
            }
        }
     }
         }else{

        
         return back()->withErrors(['账号或密码错误!']);  
         }
    }

    // public function index(){
    	
    // 	echo "index";
    // }


	public function wechat(){
		$status=md5(uniqid());
		echo $status;
		//dd($status);
		$access_token=Wechat::getToken();
		$url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
		$postData='{"expire_seconds": 604800, "action_name": "QR_STR_SCENE", "action_info": {"scene": {"scene_str": "'.$status.'"}}}';
		$res=Curl::curlPost($url,$postData);
		$res=json_decode($res,true);
		$ticket=$res['ticket'];
		$tick="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
		// dd($tick);
		return view('admin/wechat',['tick'=>$tick,'status'=>$status]);

		
}

	public function wechatdo(Request $request){

		$echostr=$request->input("echostr");
		
		if(!empty($echostr)){
			echo $echostr;die;
		}
	//接受xml数据
	$xmlData=file_get_contents("php://input");
	// dd($xmlData);
	//转为对象
	$xmlObj=simplexml_load_string($xmlData);

	
	  // dd($xmlObj);
	  //未关注
	if($xmlObj->MsgType=='event'&&$xmlObj->Event=='subscribe'){

		$openid=(string)$xmlObj->FromUserName;
		// dd($openid);
		$EventKey=(string)$xmlObj->EventKey;

		$status=ltrim($EventKey,'qrscene_');
		// dd($status);
		if($status){
			$aa=Cache::put($status,$openid,120);

			//回复文本消息$msg,$xmlObj
			Wechat::responseText("未关注正在扫码登录中,请稍后!",$xmlObj);

		}
	}

	//已关注
	if($xmlObj->MsgType=='event'&&$xmlObj->Event=='SCAN'){
		//获取用户openid
		$openid=(string)$xmlObj->FromUserName;
		//用户标识
		$status=(string)$xmlObj->EventKey;
		// dd($status);
		if($status){
			Cache::put($status,$openid,120);
			//回复文本消息
			Wechat::responseText("已关注正在扫码登录中,请稍后!",$xmlObj);
		}
	}
}
//js轮询
public function checkWechatLogin(Request $request){

$status=$request->input("status");

$openid=Cache::get($status);

if(!$openid){
	return json_encode(['ret'=>0,'msg'=>"未扫码"]);
}else{
	return json_encode(['ret'=>1,'msg'=>"已扫码登录"]);
}
}

public function list(){
    	
    	echo "list";
    }

    public function show(){

    	$appId = "101353491";  //应用账号id
		$appSecret = 'df4e46ba7da52f787c6e3336d30526e4'; //应用账号密码
		$redirect_uri = "http://www.iwebshop.com/index.php";//跳转到qq服务器 显示登录
		$url="https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id={$appId}&redirect_uri={$redirect_uri}&state=1";
		header("location:".$url);
    }

   
   public function index(){
   	$appId = "101353491";  //应用账号id
   	$appSecret = 'df4e46ba7da52f787c6e3336d30526e4'; //应用账号密码
   	$redirect_uri = "http://www.iwebshop.com/index.php";//跳转到qq服务器 显示登录
   	$code=$_GET['code'];
   	$url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id={$appId}&client_secret={$appSecret}&code={$code}&redirect_uri={$redirect_uri}";
	$data = file_get_contents($url);
	//截取
	$start = strpos($data,"="); //获取开始位置
	$end = strpos($data,"&"); //结束位置
	$access_token = substr($data,$start+1,$end-$start-1);  //截取
	// dd($access_token);
	
	$url = "https://graph.qq.com/oauth2.0/me?access_token={$access_token}";
	$data = file_get_contents($url);
	//dd($data);
	$data = 'callback( {"client_id":"101353491","openid":"B42BCFAFD457AAEBCDDA3605F7F09441"} );';
	$start = strpos($data,"("); //获取开始位置
	$end = strpos($data,")"); //结束位置
	$openidData = substr($data,$start+1,$end-$start-1);  //截取
	$openidData = json_decode($openidData,true);
	$openid = $openidData['openid'];
	// dd($openid);

	$url = "https://graph.qq.com/user/get_user_info?access_token={$access_token}&oauth_consumer_key={$appId}&openid={$openid}";
	$data = file_get_contents($url);
	$data = json_decode($data,true);
	var_dump($data);die;

}
    
}
