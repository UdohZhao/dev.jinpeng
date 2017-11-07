$(function(){

	//点击学生名字
	$(".stuName").click(function(){
			var uid=$(this).children("a").attr("value"); //学生用户uid


		$.modal({
		  title: "",
		  text: "",
		  buttons: [
		    { text: "查看补充题", onClick: function(){
		    	window.location.href='/Wap/Tsupplement/index/'+uid;//跳转地址位置
		    } },
		    { text: "查看答题详情", onClick: function(){ 
		    	window.location.href='/Wap/Student/index/TeacherType/1/'+uid;//跳转地址位置
		    } },
		    { text: "返回", className: "default", onClick: function(){ 
		    	$.closeModal();
		    } },
		  ]
		});
	});


	//点击手指进行评价
	$(".fa-hand-o-up").click(function(){
		var uid=$(this).attr("value") //用户uid
		var id=$(this).attr("id")  //评价表id值
		var ego=$("#ego"+id).html();      // 家长评价
		var patriarch=$("#patriarch"+id).html();// 自我评价
		var schoolmate=$("#schoolmate"+id).html();//同学评价
		var type=0;                            //评价类型，0 综合评价

		$.modal({
		  title: "请给出您的评价",
		  text: "",
		  buttons: [
		  	{ text: "<img src='/Apps/Wap/View/Teacher/img/3331.png' />", className: "default", onClick: function(){
				console.log($(this).children().eq(1).children().eq(1).children().attr("src"))


				var Evalutype="y";						//评价指标 ,y 优 l 良 z 中 c 差
				$.ajax({
					url:'calculate',
					data:'uid='+uid+'&id='+id+"&type="+type+"&Etype="+Evalutype+"&ego="+ego+"&patriarch="+patriarch+"&schoolmate="+schoolmate,
					dataType:'json',
					type:'post',
					success:function(re) {

						$("#evalu" + id).html(re.info);

						if (re.msg == 'y') {
							$("#p" + id).html("优&nbsp;");
						} else if (re.msg == 'l') {
							$("#p" + id).html("良&nbsp;")
						} else if (re.msg == 'z') {
							$("#p" + id).html("中&nbsp;")
						} else if (re.msg == 'c') {
							$("#p" + id).html("差&nbsp;")
						}
						$("#"+id).hide();
					},
					error:function(re){
						alert("系统崩溃");
					}
				})





				//window.location.href='/Wap/Teacher/index';//跳转地址位置
		    } },
		    { text: "<img src='/Apps/Wap/View/Teacher/img/3332.png' />", className: "default", onClick: function(){

				var Evalutype="l";						//评价指标 ,y 优 l 良 z 中 c 差
				$.ajax({
					url:'calculate',
					data:'uid='+uid+'&id='+id+"&type="+type+"&Etype="+Evalutype+"&ego="+ego+"&patriarch="+patriarch+"&schoolmate="+schoolmate,
					dataType:'json',
					type:'post',
					success:function(re) {

						$("#evalu" + id).html(re.info);

						if (re.msg == 'y') {
							$("#p" + id).html("优&nbsp;");
						} else if (re.msg == 'l') {
							$("#p" + id).html("良&nbsp;")
						} else if (re.msg == 'z') {
							$("#p" + id).html("中&nbsp;")
						} else if (re.msg == 'c') {
							$("#p" + id).html("差&nbsp;")
						}
						$("#"+id).hide();
					},
					error:function(re){
						alert("系统崩溃");
					}
				})


		    	//window.location.href='/Wap/Teacher/index';//跳转地址位置
		    } },
		    { text: "<img src='/Apps/Wap/View/Teacher/img/3333.png' />", className: "default", onClick: function(){

				var Evalutype="z";						//评价指标 ,y 优 l 良 z 中 c 差
				$.ajax({
					url:'calculate',
					data:'uid='+uid+'&id='+id+"&type="+type+"&Etype="+Evalutype+"&ego="+ego+"&patriarch="+patriarch+"&schoolmate="+schoolmate,
					dataType:'json',
					type:'post',
					success:function(re) {

						$("#evalu" + id).html(re.info);

						if (re.msg == 'y') {
							$("#p" + id).html("优&nbsp;");
						} else if (re.msg == 'l') {
							$("#p" + id).html("良&nbsp;")
						} else if (re.msg == 'z') {
							$("#p" + id).html("中&nbsp;")
						} else if (re.msg == 'c') {
							$("#p" + id).html("差&nbsp;")
						}
						$("#"+id).hide();
					},
					error:function(re){
						alert("系统崩溃");
					}
				})

		    	//window.location.href='/Wap/Teacher/index';//跳转地址位置
		    } },
		    { text: "<img src='/Apps/Wap/View/Teacher/img/3334.png' />", className: "default", onClick: function(){

				var Evalutype="c";						//评价指标 ,y 优 l 良 z 中 c 差
				$.ajax({
					url:'calculate',
					data:'uid='+uid+'&id='+id+"&type="+type+"&Etype="+Evalutype+"&ego="+ego+"&patriarch="+patriarch+"&schoolmate="+schoolmate,
					dataType:'json',
					type:'post',
					success:function(re) {

						$("#evalu" + id).html(re.info);

						if (re.msg == 'y') {
							$("#p" + id).html("优&nbsp;");
						} else if (re.msg == 'l') {
							$("#p" + id).html("良&nbsp;")
						} else if (re.msg == 'z') {
							$("#p" + id).html("中&nbsp;")
						} else if (re.msg == 'c') {
							$("#p" + id).html("差&nbsp;")
						}
						$("#"+id).hide();
					},
					error:function(re){
						alert("系统崩溃");
					}
				})

		    	//window.location.href='/Wap/Teacher/index';//跳转地址位置
		    } },
		    { text: "取消评价", className: "default", onClick: function(){ 
		    	$.closeModal();
		    } },
		  ]
		});
		$(".weui-dialog__ft a").addClass("diffrent");
	});
})