$(function(){
	var ulobj=document.getElementsByClassName("nowHere")[0];
	var liobj=ulobj.getElementsByTagName("li");
	var liLen=liobj.length;
	var i=1;
	setInterval(function(){
		$(".nowHere").css("margin-left",-20*i+"px");
		i++;
		if(i==19){
			$(".nowHere").css("margin-left","0px");
			ulobj.insertBefore(liobj[0],liobj[liLen-1].nextSiblings);
			i=1;
		};
		},1000);
	// var core=$(".mountain_right p #perScore").html();
	// var coreNum=parseInt(core);//本期得分
	// var historyScore=$("#yearScore").html();
	// var historyScoress=parseInt(historyScore);//历史总分
	// alert(core);
	// alert(historyScore);
	// var historyScoress=1600;
	// var coreNum=10;
	// $(".aa").css("height",(historyScoress-coreNum)*0.15+6+"px");//初始高度
	// $(".bian").css("height",(historyScoress-coreNum)*0.15+"px");
	// var aaHeight=$(".aa").height();
	// if(aaHeight<340){
	// 	setTimeout(function(){
	// 		$(".ulBox").css("border-bottom","1px solid #358d20");
	// 	},2000);
	// 	setTimeout(function(){
	// 		$(".aa").css("height",historyScoress*0.15+6+"px");//增加得分*0.15倍高度
	// 		$(".bian").css("height",historyScoress*0.15+"px");
	// 	},2000);
	// }else if(aaHeight>=340&&aaHeight<350){
	// 	$(".ulBox").css("border-bottom","1px solid #358d20");
	// }else{
	// 	$(".aa").css("height","350px");
	// 	$(".bian").css("height","325px");
	// };

	
	//提交按钮弹出框
	  /*$(".evaluationSubmit").click(function(){
	    $.modal({
	      title: "<img src='/Apps/Wap/View/Layouts/img/开心-1.png' />",
	      text: "提交成功！<br />继续努力哦"
	    },function(){
	      setTimeout(function(){
	        // window.location.href='/Wap/Student/index';//跳转地址位置
			  $.closeModal();
			  $("#tab22").addClass("weui-bar__item--on");
			  $("#tab11").removeClass("weui-bar__item--on");
			  $("#tab1").hide()
			  $("#tab2").show()
	      },2000);
	    });
	  });*/

	  //点击其他tab删除之前加载内容
	  $("#tab11").click(function(){
	  	$("#tab2").find("p.newlist").remove();
	  	$("#tab3").find("p.newlist").remove();
	  });
	  $("#tab22").click(function(){
	  	$("#tab1").find("p.newlist").remove();
	  	$("#tab3").find("p.newlist").remove();
	  });
	  $("#tab33").click(function(){
	  	$("#tab1").find("p.newlist").remove();
	  	$("#tab2").find("p.newlist").remove();
	  });

});
		//滚动加载
		/*$(".weui-tab__bd-item--active").infinite();
	  	var loading = false;  //状态标记
		$(document.body).infinite().on("infinite", function() {
		  if(loading) return;
		  loading = true;
			//加载模板
		  setTimeout(function() {
		    $(".weui-tab__bd-item--active .content-padded .list").append("" +
				"<div class='row theTitle'>" +
					"<div class='col-xs-2 col-sm-1 titleNum'>" +
						"<sapn>#1</span>" +
					"</div>" +
					"<div class='col-xs-10 col-sm-11 theTitle-list'>" +
						"<p class='titleContent'>你对自己的学习情况满意吗？</p>" +
					"</div>" +
					"<div class='col-xs-12 col-sm-12 answers'>" +
						"<ul class='list-inline'>" +
							"<li>" +
								"<a class='answersK'>" +
									"<img src='/Apps/Wap/View/Student/img/勾1.png'>" +
								"</a>" +
							"<p>很满意很满意很满意很满意很满意很满意很满意很满意</p>" +
							"</li>" +
						"</ul>" +
					"</div>" +
				"</div><script src='/Apps/Wap/View/Student/js/tick.js'></script>");
		    loading = false;
		  }, 1500);   //模拟延迟
		});*/



		