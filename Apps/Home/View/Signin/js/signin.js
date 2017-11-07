$(function(){
  // 获取窗口高度
  if (window.innerHeight)
  winHeight = window.innerHeight;
  else if ((document.body) && (document.body.clientHeight))
  winHeight = document.body.clientHeight;
  // 动态赋值背景高度
  $(".container-fluid").css("min-height",winHeight);
  $(".back_img").css("min-height",winHeight);
  $(".cover").css("min-height",winHeight);
  //动画

    $(".back_img").fadeIn(2000);
    var a=setTimeout(function(){
      $(".cover").css("opacity",".5");
    },2100);
    var b=setTimeout(function(){
        $(".container-fluid>img").fadeIn(1000);
        $(".container-fluid>img").css("transform","scale"+"("+"1.5"+")");
      },4100);
    var c=setTimeout(function(){
          $(".container-fluid>img").css({"transform":"scale"+"("+"1"+")","top":"10px","left":"83%"});
        },7200);
    var d=setTimeout(function(){
            $(".form-horizontal").fadeIn(1500);
          },10800);

  $("body").click(function(){
    clearTimeout(a);
    clearTimeout(b);
    clearTimeout(c);
    clearTimeout(d);
    $(".cover").css("opacity",".5");
    $(".container-fluid>img").fadeIn(1000);
    $(".container-fluid>img").css({"transition":"all 1s","-ms-transition":"all 1s","-moz-transition":"all 1s","-webkit-transition":"all 1s","-o-transition":"all 1s","transform":"scale"+"("+"1"+")","top":"10px","left":"83%"});
    $(".form-horizontal").fadeIn(1500);
  });

  // 触发年级点击事件
  $("#cid").on("change",function(){
      // 清空
      $("#scid").empty();
      var cid = $("#cid").val();
      // Ajax根据年级动态查询班级
      $.ajax({
        type: 'POST',
        url: '/Signin/returnClass',
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
          equalTo: "#password"
        }
      },
      messages: {
        cid: "请选择班级",
        username: {
          required: "<span style='color:red;'>用户名不能为空!</span>",
          minlength: "<span style='color:red;'>用户名长度不能小于6个数字或字母!</span>"
        },
        password: {
          required: "<span style='color:red;'>密码不能为空!</span>",
          minlength: "<span style='color:red;'>密码长度不能小于6个数字或字母!</span>"
        },
        confirmPassword: {
          required: "<span style='color:red;'>确认密码不能为空!</span>",
          minlength: "<span style='color:red;'>密码长度不能小于6个数字或字母!</span>",
          equalTo: "<span style='color:red;'>两次密码不一致!</span>"
        }
      }
  });
  //点击注册时，按钮样式
  // $(".btn_width").click(function(){
  //   $(this).css({"background-color":"#8d6540","color":"#fff","border":"none"});
  // })
})