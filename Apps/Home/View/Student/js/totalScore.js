$(function(){
	var ulobj=document.getElementsByTagName("ul")[0];
	var liobj=ulobj.getElementsByTagName("li");
	var liLen=liobj.length;
	var i=1;
	setInterval(function(){
		$("ul").css("margin-left",-20*i+"px");
		$("ul").addClass("active");
		i++;
		if(i==19){
			$("ul").css("margin-left","0px");
			$("ul").removeClass("active");
			ulobj.insertBefore(liobj[0],liobj[liLen-1].nextSiblings);
			i=1;
		};
		},1000);

	//山的高度变化
	var allcore=$(".mountain_right p .core").html();
	var allcoreNum=parseInt(allcore);
	$(".aa").css("height","8px");
	$(".bian").css("height","0px");
	setTimeout(function(){
		$(".aa").css("height",allcoreNum*0.15+8+"px");
		$(".bian").css("height",allcoreNum*0.15+"px");
	},1000);
})