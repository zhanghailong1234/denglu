<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<script src="/jquery.js"></script>
</head>
<body>
	<h1 align="center">扫描下面二维码直接登录</h1>
	<h1 align="center" style="padding-top: 130px"><img src="{{$tick}}"></h1>
	<h1 align="center"><a href="/admin/login">账号密码登录</a></h1>
</body>
</html>
<script type="text/javascript">
var t = setInterval("text();",2000);
var status="{{$status}}";
function text(){
$.ajax({
    url: "{{url('admin/checkWechatLogin')}}",
    data:{status:status},
    dataType: "json",
    success: function (res) {
        //console.log(res);
         //返回提示
         if(res.ret == 1){
                //关闭定时器
                clearInterval(t);
                //扫码登录成功
                alert(res.msg);
                location.href = "{{url('http://49.235.78.223/admin/index')}}";
            }
    }
});
};    
</script>

