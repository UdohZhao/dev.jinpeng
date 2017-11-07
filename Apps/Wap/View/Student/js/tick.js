$(function(){
	//打勾
	$(".single").click(function(){
		$(this).children("img").css("display","block");
		$(this).parents("li").siblings("li").children(".single").children("img").css("display","none");
	});

})