<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	
</head>
<body>
	<form action="{{url('/admin/loginDo')}}" style="padding-top: 200px" method="post">
	@csrf
	
		<table align="center">
			<tr>
				<td>姓名:</td>
				<td><input type="text" name="account" style="width:800px;height:50px">
				<p style="color:red">
				@if(!empty($errors->first()))
				{{$errors->first()}}
				@endif
				</p>
				</td>
			</tr>
			<tr>
				<td>密码:</td>
				<td><input type="password" name="user_pwd" style="width:800px;height:50px">
				
				</td>
			</tr>
			<tr>
				<td></td>
				<td align="center"><button type="submit" style="width:800px;height:50px;background-color:green">登录</button></td>
			</tr>
			
				
			
		</table>
		<div>
		
		
		<div style="float:left;padding-left: 500px"><h1 align="center"><a href="/admin/show"><img src="/1.png"></a></h1></div>
		<div style="float:left;"><h1><a href="/admin/wechat">扫描二维码直接登录</a></h1></div>
		</div>
		
	</form>
</body>
</html>
