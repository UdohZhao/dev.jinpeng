$(function(){
  // 开始时间
  $("#datetimeStart").datetimepicker({
        format: 'yyyy-mm-dd',
        minView:'month',
        language: 'zh-CN',
        autoclose:true,
        startDate:new Date()
    }).on("click",function(){
        $("#datetimeStart").datetimepicker("setEndDate",$("#datetimeEnd").val())
    });
    // 结束时间
    $("#datetimeEnd").datetimepicker({
        format: 'yyyy-mm-dd',
        minView:'month',
        language: 'zh-CN',
        autoclose:true,
        startDate:new Date()
    }).on("click",function(){
        $("#datetimeEnd").datetimepicker("setStartDate",$("#datetimeStart").val())
    });

    // 验证表单
    $("#periodForm").validate({
      rules:{
        start_time: "required",
        end_time: "required",
        periods: {
          required: true,
          digits: true,
          remote: {
              url: "/Admin/index.php/Period/checkPeriods",     //后台处理程序
              type: "post",               //数据发送方式
              dataType: "json",           //接受数据格式
              data: {                     //要传递的数据
                  periods: function() {
                      return $("#periods").val();
                  }
              }
          }
        }
      },
      messages:{
        start_time: "<span style='color:red;'>请选择开始时间！</span>",
        end_time: "<span style='color:red;'>请选择结束时间！</span>",
        periods: {
          required: "<span style='color:red;'>周期不能为空！</span>",
          digits: "<span style='color:red;'>输入数字即可，例如：2017001</span>"
        }
      }
    });

});