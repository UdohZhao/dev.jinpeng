$(function(){

});
// 删除
function del(id){
  //询问框
  layer.confirm('删除后不可恢复，确认吗？', {
    btn: ['确定','取消'] //按钮
  }, function(){
    layer.closeAll();
    window.location.href = '/Admin/index.php/Questionbank/del/id/'+id;
  }, function(){
    layer.closeAll();
  });
}
