$(document).ready(function() {
  //在键盘按下并释放及提交后验证提交表单
  $("#classconfigForm").validate({
    rules: {
      cname: "required"
    },
    messages: {
      cname: "请输入班级名称"
    }
  });
});
