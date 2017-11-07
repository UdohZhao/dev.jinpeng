$(function(){

  // 验证综合评价表单
  $("#synthesisForm").validate({
    rules:{
      y: {
        required: true,
        range: [1,100]
      },
      l: {
        required: true,
        range: [1,100]
      },
      z: {
        required: true,
        range: [1,100]
      },
      c: {
        required: true,
        range: [1,100]
      }
    },
    messages:{
      y: {
        required: "<span style='color:red;'>评优百分比不能为空！</span>",
        range: "<span style='color:red;'>百分比数值介于1～100</span>"
      },
      l: {
        required: "<span style='color:red;'>评良百分比不能为空！</span>",
        range: "<span style='color:red;'>百分比数值介于1～100</span>"
      },
      z: {
        required: "<span style='color:red;'>评中百分比不能为空！</span>",
        range: "<span style='color:red;'>百分比数值介于1～100</span>"
      },
      c: {
        required: "<span style='color:red;'>评差百分比不能为空！</span>",
        range: "<span style='color:red;'>百分比数值介于1～100</span>"
      }
    }
  });

  // 验证补充评价表单
  $("#replenishForm").validate({
    rules:{
      y: {
        required: true,
        range: [1,100]
      },
      l: {
        required: true,
        range: [1,100]
      },
      z: {
        required: true,
        range: [1,100]
      },
      c: {
        required: true,
        range: [1,100]
      }
    },
    messages:{
      y: {
        required: "<span style='color:red;'>评优百分比不能为空！</span>",
        range: "<span style='color:red;'>百分比数值介于1～100</span>"
      },
      l: {
        required: "<span style='color:red;'>评良百分比不能为空！</span>",
        range: "<span style='color:red;'>百分比数值介于1～100</span>"
      },
      z: {
        required: "<span style='color:red;'>评中百分比不能为空！</span>",
        range: "<span style='color:red;'>百分比数值介于1～100</span>"
      },
      c: {
        required: "<span style='color:red;'>评差百分比不能为空！</span>",
        range: "<span style='color:red;'>百分比数值介于1～100</span>"
      }
    }
  });


});