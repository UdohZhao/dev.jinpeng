// 冻结&激活
function freeze(uid,status){
    // status
    var msg = '';
    if( status == 1 ){
       msg += '冻结后该教师将不可登录，确认吗？';
    }else{
       msg += '激活后该教师将可正常登录，确认吗？';
    }
    //询问框
    layer.confirm(msg, {
      btn: ['确定','取消'] //按钮
    }, function(){
      window.location.href = '/Admin/index.php/Teacher/freeze/uid/'+uid+'/status/'+status;
    }, function(){
      layer.closeAll();
    });
}
// 删除用户
function delss(uid){
  //询问框
  layer.confirm('确认删除该班级负责人相关信息吗？', {
    btn: ['确定','取消'] //按钮
  }, function(){
    window.location.href = '/Admin/index.php/Teacher/delss/uid/'+uid;
  }, function(){
    layer.closeAll();
  });
}