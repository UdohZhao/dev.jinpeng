$(function(){
	// 触发年级点击事件
  $("#cid").on("change",function(){
      // 清空

      $("#scid").empty();
      var cid = $("#cid").val();
      // Ajax根据年级动态查询班级
      $.ajax({
        type: 'POST',
        url: 'returnClass',
        data: {cid:cid},
        success: function(data){
          $.each(data,function(){
            $("#scid").append("<option value="+this.id+">"+this.cname+"</option>");
          });
        },
        dataType: 'JSON'
      });
  });
  // 验证表单
    $(".btnSignin").click(function(){
      var a=1;

  $("#signinForm").validate({
      rules: {
        cid: "required",
        username: {
          required: true,
          minlength: 6
        },
        password: {
          required: true,
          minlength: 6
        },
        confirmPassword: {
          required: true,
          minlength: 6,
          equalTo: "#keyWords"
        }
      },
      messages: {
        cid: "请选择班级",
        username: {
          required: "<script>$.toast('用户名不能为空!','cancel');</script>",
          minlength: "<script>$.toast('用户名长度不能小于6个数字或字母!','cancel');</script>"
        },
        password: {
          required: "<script>$.toast('密码不能为空!','cancel');</script>",
          minlength: "<script>$.toast('密码长度不能小于6个数字或字母!','cancel');</script>"
        },
        confirmPassword: {
          required: "<script>$.toast('确认密码不能为空!','cancel');</script>",
          minlength: "<script>$.toast('密码长度不能小于6个数字或字母!','cancel');</script>",
          equalTo: "<script>$.toast('两次密码不一致!','cancel');</script>"
        }
      },
      submitHandler : function(form) {
        $(form).ajaxSubmit({
            dataType:"json",
            success:function( msg ){
              console.log(msg);
              if( msg === false ){
                // 提示密码错误
                $.toast("注册失败", "cancel");
                // window.setTimeout("window.location.reload();",2000);
              }else if( msg == 2){
                $.toast("用户名已经存在,请勿重复注册!", "cancel");
                // 跳转到教师界面
                // window.setTimeout("window.location.href='/Wap/Teacher/index';",1000);
                // swal("发送失败", "请稍后重试!", "error");
              }else if(msg==1){
                window.setTimeout("window.location.href='/Wap/Login/index';",2000);
              }
            },
            error:function(e){
              console.log(e);
              //swal("发送失败", "请稍后重试!", "error");
            }
        });
      }
  });
});
  $(".btnSignin").click(function(){
    $(this).css({"background-color":"#f4c16f","color":"#000"});
  })
})