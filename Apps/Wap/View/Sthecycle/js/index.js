$(function(){
	$(".historyList").click(function(){
		$(this).css("border","2px solid #f4c16f");
		$(this).siblings(".historyList").css("border","2px solid #bbbbbb");
		$(this).children("a").css("text-decoration","none");
	});


})
