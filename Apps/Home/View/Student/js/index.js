$(function(){
	// 获取窗口高度
	if (window.innerHeight)
	winHeight = window.innerHeight;
	else if ((document.body) && (document.body.clientHeight))
	winHeight = document.body.clientHeight;
	// 动态赋值背景高度
	$(".container-fluid").css("min-height",winHeight);
	//菜单点击三角形变化
	$(".click_menu").click(function(){
		$(this).siblings(".ul_list").css("display","block");
		$(this).siblings("img").css("transform","rotate(0deg)");
		$(this).parents("li").siblings("li").children(".ul_list").css("display","none");
		$(this).parents("li").siblings("li").children("img").css("transform","rotate(-90deg)");
	});

	//点击菜单li的样式
	$(".ul_list>li").click(function(){
		$(this).css({"background-color":"#7b5028"});
		$(this).children("a").css("color","#ffffff");
		$(this).siblings("li").css("background","none");
		$(this).siblings("li").children("a").css("color","#000000");
		$(this).parents(".ul_list").parents("li").siblings("li").children(".ul_list").children("li").css("background","none");
		$(this).parents(".ul_list").parents("li").siblings("li").children(".ul_list").children("li").children("a").css("color","#000");
	});

	//修改密码
	$("#changePassword").click(function(){
		$(".password_cover").css({"display":"block","z-index":"100"});
	})
	$(".btn_width").click(function(){
		alert("修改成功！请重新登陆")
	})
	$(".cancel").click(function(){
		$(".password_cover").css("display","none");
	});

})