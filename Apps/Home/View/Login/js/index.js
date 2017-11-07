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
  $(".container-fluid").css("height",winHeight);
  // 视频背景
  // $('.covervid-video').coverVid(winWidth,winHeight);
  $('.covervid-video').css({"width":"100%","height":"auto"});
  console.log(winWidth);
  console.log(winHeight);
  // 动态调用登录框
  $('#loginModal').modal({
    backdrop: 'static',
    keyboard: false,
  });
  // 表单验证
  $("#loginForm").validate({
      rules: {
        username: "required",
        password: "required"
      },
      messages: {
        username: "<span style='color:red;'>用户名不能为空!</span>",
        password: "<span style='color:red;'>密码不能为空!</span>"
      }
  });

})

