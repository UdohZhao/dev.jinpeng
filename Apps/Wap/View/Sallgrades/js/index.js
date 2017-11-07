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
		$(".aa").css("height",allcoreNum*0.133+8+"px");
		$(".bian").css("height",allcoreNum*0.133+"px");
	},1000);



    //调用案例，需要在被调用的标签内 写上最终的数值
    jQuery(function($) {
        $(".timer").countTo({
            lastSymbol:" ", //显示在最后的字符
            from: 0,  // 开始时的数字
            speed: 2000,  // 总时间
            refreshInterval:100,  // 刷新一次的时间
            beforeSize:0, //小数点前最小显示位数，不足的话用0代替 
            decimals: 1,  // 小数点后的位数，小数做四舍五入
            onUpdate: function() {
            },  // 更新时回调函数
            onComplete: function() {
                for(i in arguments){
                    //console.log(arguments[i]);
                }
            }
        });
    });
});