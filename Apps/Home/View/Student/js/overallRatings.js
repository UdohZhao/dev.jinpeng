$(function(){
	var ulobj=document.getElementsByTagName("ul")[0];
	console.log(ulobj);
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
	var core=$(".mountain_right p .core").html();
	var coreNum=parseInt(core);//本期得分
	var historyScore=$(".historyScore").val();
	var historyScoress=parseInt(historyScore);//历史总分

	$(".aa").css("height",(historyScoress-coreNum)*0.3+6+"px");//初始高度
	$(".bian").css("height",(historyScoress-coreNum)*0.3+"px");

	var aaHeight=$(".aa").height();
	console.log(aaHeight);
	if(aaHeight<320){
		setTimeout(function(){
			// $(".nowHere img").css("height","0px");
			$(".ulBox").css("border-bottom","1px solid #358d20");
		},2000);
		setTimeout(function(){
			$(".aa").css("height",historyScoress*0.3+6+"px");//增加得分*0.15倍高度
			$(".bian").css("height",historyScoress*0.3+"px");
			// $(".redImg img").css("height",21+"px");
		},500);
	}else if(aaHeight>=320&&aaHeight<370){
		// $(".nowHere img").css("height","0px");
		// $(".redImg img").css("height",21+"px");
		$(".ulBox").css("border-bottom","1px solid #358d20");
	}else{
		$(".aa").css("height","380px");
		$(".bian").css("height","380px");
	};

})