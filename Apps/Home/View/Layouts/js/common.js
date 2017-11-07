$(function(){
  // 获取窗口宽度
  if (window.innerWidth)
  winWidth = window.innerWidth;
  else if ((document.body) && (document.body.clientWidth))
  winWidth = document.body.clientWidth;
  // 获取窗口高度
  if (window.innerHeight)
  winHeight = window.innerHeight;
  else if ((document.body) && (document.body.clientHeight))
  winHeight = document.body.clientHeight;
  // 通过深入 Document 内部对 body 进行检测，获取窗口大小
  if (document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth)
  {
  winHeight = document.documentElement.clientHeight;
  winWidth = document.documentElement.clientWidth;
  }
  // 动态赋值背景高度
  $(".container-fluid").css("min-height",winHeight);
  console.log(winHeight);
  // 遮罩层
  $('.container-fluid').modal({
    backdrop:'static',
    keyboard:false,
    show:true
  });
  // 验证修改密码表单
  $("#changePassform").validate({
    rules: {
      password: {
        required: true,
        minlength: 6,
        remote: {
          url: "/Signin/changePassword",
          type: "post",
          dataType: "json",
          data: {
             password: function() {
                return $("#password").val();
             }
          }
        }
      },
      newPassword: {
        required: true,
        minlength: 6
      },
      confirmPassword: {
        required: true,
        minlength: 6,
        equalTo: "#newPassword"
      }
    },
    messages: {
      password: {
        required: "<span style='color:red;'>原始密码不能为空！</span>",
        minlength: "<span style='color:red;'>密码由至少6位数字或者字母组成！</span>"
      },
      newPassword: {
        required: "<span style='color:red;'>新密码不能为空！</span>",
        minlength: "<span style='color:red;'>密码由至少6位数字或者字母组成！</span>"
      },
      confirmPassword: {
        required: "<span style='color:red;'>确认新密码不能为空！</span>",
        minlength: "<span style='color:red;'>密码由至少6位数字或者字母组成！</span>",
        equalTo: "<span style='color:red;'>两次新密码不一致！</span>"
      }
    }
  });

})

// 触发点击修改密码事件
function changePassword(){
  // 显示在最顶层
  $('#changePassModal').css('z-index','9999');
  $('#changePassModal').modal({
      backdrop: 'static',
      keyboard: false,
      show: true
  });
}