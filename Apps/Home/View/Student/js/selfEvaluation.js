$(function(){
	// 获取窗口高度
	if (window.innerHeight)
	winHeight = window.innerHeight;
	else if ((document.body) && (document.body.clientHeight))
	winHeight = document.body.clientHeight;
	var wheight=winHeight-56;
	// 动态赋值背景高度
	$(".back_img").css("height",wheight);
})
