$(function(){
	// 表单验证
	$("#loginForm").validate({
    	focusInvalid : true,
	    rules: {
	      username: "required",
	      password: "required"
	    },
	    messages: {
	      username: "<script>$.toast('用户名不能为空!','cancel');</script>",
	      password: "<script>$.toast('密码不能为空!','cancel');</script>"
	    },
	    submitHandler : function(form) {
	      $(form).ajaxSubmit({
	          dataType:"json",
	          success:function( msg ){
	          	console.log(msg);
	            if( msg === false ){
	              // 提示密码错误
	          		$.toast("账号或密码错误", "cancel");
	              // window.setTimeout("window.location.reload();",2000);
	            }else if( msg == 1){
	              // 跳转到教师界面
	              window.setTimeout("window.location.href='/Wap/Teacher/index';",1000);
	              // swal("发送失败", "请稍后重试!", "error");
	            }else if(msg.info=="4"){
	            	$.toast(msg.msg, "cancel");
	            }else{
	           	  // 跳转到学生界面
	           	  window.setTimeout("window.location.href='/Wap/Student/index';",1000);
	            }
	          },
	          error:function(e){
	          	console.log(e);
	            //swal("发送失败", "请稍后重试!", "error");
	          }
	      });
	    }
	});
})