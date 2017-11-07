/*
$(function(){
	$(".rightMove").click(function(){
		if($(this).children("a").children("b").html()=="优"){
			var Evalutype="y";				//评价选择比例
		}else if($(this).children("a").children("b").html()=="良"){
			var Evalutype="l";
		}else if($(this).children("a").children("b").html()=="中"){
			var Evalutype="z";
		}else if($(this).children("a").children("b").html()=="差"){
			var Evalutype="c";
		}
		var type=1;				//评价类型,补充评价
		var id=$("#divN").html();   //补充评论表id

		var thisImg=$(this).html();
		$.modal({
		  title: "您给出的评价如下",
		  text: thisImg,
		  buttons: [
		    { text: "确定", onClick: function(){

				$.ajax({
					url:'__APP__/Tsuplement/calculate',
					data:'id='+id+"&type="+type+"&Etype="+Evalutype,
					dataType:'json',
					type:'post',
					success:function(re){
						if(re.info==0){
							alert("请先对该学生进行综合评分，再作补充");
						}else if(re.info==1){
							alert("补充评价成功，已重新计算分数");
							window.location.href="__APP__/Teacher/index";
						}
					},
					error:function(){
						alert("系统崩溃");
					}
				})


		    	// window.location.href='Teacher/index';//跳转地址位置
		    } },
		    { text: "取消", className: "default", onClick: function(){ 
		    	$.closeModal();
		    } },
		  ]
		});
	})
})*/
