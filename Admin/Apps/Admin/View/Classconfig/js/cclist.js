// 班级删除
function del(id,student,teacher){
  // student
  if( student > 0 || teacher != '暂无' ){
    layer.alert('请先删除该班级下绑定的教师和学生！',{icon:5});
  }else{
    //询问框
    layer.confirm('确认删除该班级吗？', {
      btn: ['确定','取消'] //按钮
    }, function(){
      window.location.href = "/Admin/index.php/Classconfig/del/id/"+id;
    }, function(){
      layer.closeAll();
    });
  }
}
// 毕业
function graduation(id){
  //询问框
  layer.confirm('确认该班级已经毕业吗？', {
    btn: ['确定','取消'] //按钮
  }, function(){
    //默认prompt
    layer.prompt({title:'请输入第几届毕业生（例如：2017，输入数字即可）'},function(val, index){
      // val
      if( val == false ){
        layer.alert('请输入第几届毕业生（例如：2017，输入数字即可）',{icon:5});
        return false;
      }else{
        // 数字
        if( isNaN(val) ){
          layer.alert('请输入第几届毕业生（例如：2017，输入数字即可）',{icon:5});
          return false;
        }else{
          layer.closeAll();
          layer.close(index);
          window.location.href = "/Admin/index.php/Classconfig/graduation/id/"+id+"/g/"+val;
        }
      }
    });
  }, function(){
    layer.closeAll();
  });
}



