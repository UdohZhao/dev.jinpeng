$(function(){

});
// 开始&过期
function freeze(id,status){
  // status
  var msg = '';
  if( status == 2 ){
    msg += '周期开始后学生将在有效时间内完成答题，确认吗？';
  }else{
    msg += '过期后将不可更改，确认吗？';
  }
  //询问框
  layer.confirm(msg, {
    btn: ['确定','取消'] //按钮
  }, function(){
    layer.closeAll();
    window.location.href = '/Admin/index.php/Period/freeze/id/'+id+'/status/'+status;
  }, function(){
    layer.closeAll();
  });
}
// 删除
function del(id){
  //询问框
  layer.confirm('删除后不可恢复，确认吗？', {
    btn: ['确定','取消'] //按钮
  }, function(){
    layer.closeAll();
    window.location.href = '/Admin/index.php/Period/del/id/'+id;
  }, function(){
    layer.closeAll();
  });
}
// 评价显示条数
function quantity(qa){
  //默认prompt
  layer.prompt({title:'默认评价试题为「'+qa+'道」'},function(val, index){
    // 验证数字
    if(/^[0-9]*$/.test(val)){
      // 0
      if( val <= 0 ){
        layer.alert('请输入大于0的整数！',{icon:5});
        return false;
      }else{
        // 跳转方法
        window.location.href = '/Admin/index.php/Period/editQa/qa/'+val;
      }
    }else{
      layer.alert('请输入数字！',{icon:5});
      return false;
    }
    layer.close(index);
  });
}
