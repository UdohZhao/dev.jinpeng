$(function(){
	var liWidth=$(".ul>li").width();
	$(".ul>li").css("height",liWidth);
	$(".ul>li").children("a").css("line-height",liWidth+'px');
})