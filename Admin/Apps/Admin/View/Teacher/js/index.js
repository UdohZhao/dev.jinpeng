$(function(){
  // 触发年级点击事件
  $("#cid").on("change",function(){
      // 清空
      $("#scid").empty();
      var cid = $("#cid").val();
      // Ajax根据年级动态查询班级
      $.ajax({
        type: 'POST',
        url: '/Admin/index.php/Teacher/returnClass',
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
  $("#teacherForm").validate({
      rules: {
        cid: "required",
        name: "required",
        username: {
          required: true,
          minlength: 6,
          remote: {
              url: "/Admin/index.php/Teacher/checkUsername",     //后台处理程序
              type: "post",               //数据发送方式
              dataType: "json",           //接受数据格式
              data: {                     //要传递的数据
                  username: function() {
                      return $("#username").val();
                  },
                  cid: function() {
                      return $("#scid option:selected").val();
                  }
              }
          }
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
        cid: "<span style='color:red;'>请选择班级！</span>",
        name: "<span style='color:red;'>教师姓名不能为空！</span>",
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
  // 验证表单 update
  $("#upteacherForm").validate({
      rules: {
        cid: "required",
        name: "required",
        password: {
          minlength: 6
        },
        confirmPassword: {
          minlength: 6,
          equalTo: "#password"
        }
      },
      messages: {
        cid: "<span style='color:red;'>请选择班级！</span>",
        name: "<span style='color:red;'>教师姓名不能为空！</span>",
        password: {
          minlength: "<span style='color:red;'>密码长度不能小于6个数字或字母!</span>"
        },
        confirmPassword: {
          minlength: "<span style='color:red;'>密码长度不能小于6个数字或字母!</span>",
          equalTo: "<span style='color:red;'>两次密码不一致!</span>"
        }
      }
  });
})