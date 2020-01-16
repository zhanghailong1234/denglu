<?php

namespace App\Http\Middleware;
use App\Models\UserModel;
use Closure;
use Session;
class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
         //session()->forget('userData');
        $sessionId=Session::getId();
        //dd($sessionId);
        $userData=$request->session()->get('info');
        if(!$userData){
            
            
             return back()->withErrors(['请先登陆!']);  
        }

       // var_dump($aa);
       $data=UserModel::where('id',$userData['id'])->first();
        if($sessionId!=$data['sessionId']){
          
            return back()->withErrors(['此号已经登录,请注意此号是否泄漏!']);  
        if(time()>$data['login_time']+120){
            session()->forget('info');
            return redirect("/admin/login");
        }else{
            UserModel::where('id',$userData['id'])->update(['login_time'=>time()+120]);
        }
        }


        return $next($request);
    }
}
