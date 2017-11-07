<?php
namespace Admin\Controller;
use Think\Controller;
class StudentController extends BaseController {
    public $id;
    public $db;
    // 构造方法
    public function _auto(){
      $this->id = intval($_GET['id']);
    }
    // 班级学生列表页面
    public function index(){
      // search
      if( $_POST['search'] ){
        $sWhere = " AND SBI.name like '%".$_POST['search']."%'";
      }
      // id
      $this->assign('id',$this->id);
      $sdata = M('class')->where('id='.$this->id)->find();
      $pdata = M('class')->where('id='.$sdata['pid'])->find();
      $this->assign('gradeClass',$pdata['cname'].$sdata['cname']);
      // 数据分页
      $count = M('students_basicinfo')->where('cid='.$this->id)->count();// 查询满足要求的总记录数
      $Page = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
      $show = $Page->show();// 分页显示输出
      // 查询该班级学生信息
      $sesbiSQL = "
          SELECT
                  SBI.name , SBI.addup , U.username , U.status , U.ctime , U.id
          FROM
                  `students_basicinfo` AS SBI
          LEFT JOIN
                  `user` AS U
          ON
                  SBI.uid = U.id
          WHERE
                  1 = 1
          AND
                  SBI.cid = $this->id

          {$sWhere}

          ORDER BY
                  SBI.addup DESC
          LIMIT
                  $Page->firstRow , $Page->listRows
      ";
      $sesbiRE = M()->query($sesbiSQL);
      $this->assign('data',$sesbiRE);
      $this->assign('page',$show);// 赋值分页输出
      // display
      $this->display();
    }
    // 修改学生信息
    public function editUserinfo(){
      $data = $this->getData();
      if( $data['newPassword'] ){
        M('user')->where('id='.$_POST['id'])->save(array('password'=>$data['newPassword']));
      }
      if( M('students_basicinfo')->where('uid='.$_POST['id'])->save($data) ){
        echo alert('修改成功！',$_SERVER["HTTP_REFERER"],6);
        die;
      }else{
        echo alert('修改失败！',$_SERVER["HTTP_REFERER"],5);
        die;
      }
    }
    // 初始化数据
    private function getData(){
      $data = array();
      $data['name'] = I('post.name','','strip_tags,htmlspecialchars');
      $data['addup'] = I('post.addup','','strip_tags,htmlspecialchars');
      // newPassword
      if( $_POST['newPassword'] ){
        $data['newPassword'] = invincibleEncrypt(I('post.newPassword','','strip_tags,htmlspecialchars'));
      }
      return $data;
    }
    // 冻结&激活
    public function freeze(){
      // GET
      if( IS_GET === true ){
        $id = intval($_GET['id']);
        $status = intval($_GET['status']);
        if( M('user')->where('id='.$id)->save(array('status'=>$status)) ){
          echo alert('更新成功！',$_SERVER["HTTP_REFERER"],6);
          die;
        }else{
          echo alert('更新失败！',$_SERVER["HTTP_REFERER"],5);
          die;
        }
      }
    }

}