$(function(){
  // <!-- 实例化编辑器 -->
  var ue = UE.getEditor('container',{
     toolbars: [
        [ 'undo', 'redo','bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc']
     ],
    autoHeightEnabled: true,
    autoFloatEnabled: true
  });

  // 验证表单
  $("#questionbankForm").validate({
    rules:{
      keyss:{
        required: true
      },
      valss:{
        required: true
      }
    },
    messages:{
      keyss:{
        required: "<span style='color:red;'>备选答案不能为空！</span>"
      },
      valss:{
        required: "<span style='color:red;'>备选答案分数不能为空！</span>"
      }
    },
    submitHandler:function(form){
       // 检测UEditor是否有内容
       if( ue.hasContents() === false ){
        layer.alert('试题不能为空！',{icon:5});
        return false;
       }else{
        form.submit();
       }
    }
  });


});
