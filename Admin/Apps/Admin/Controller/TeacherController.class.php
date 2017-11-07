<?php
namespace Admin\Controller;
use Think\Controller;
class TeacherController extends BaseController {
    public $id;
    public $db;
    // 构造方法
    public function _auto(){
      $this->id = intval($_GET['id']);
    }
    // 教师配置页面
    public function index(){
      // GET
      if( IS_GET === true ){
        // pdata
        $pdata = M('class')->where('pid=0')->order('sort ASC')->select();
        $this->assign('pdata',$pdata);
        $sdata = M('class')->where('pid='.$pdata[0]['id'])->select();
        $this->assign('sdata',$sdata);
        if( $this->id ){
          // 年级pid，班级sid，用户uid
          $data = array();
          $data['pid'] = intval($_GET['pid']);
          $data['sid'] = $this->id;
          $data['uid'] = intval($_GET['uid']);
          $data['username'] = M('user')->where('id='.$data['uid'])->getField('username');
          // 教师姓名
          $data['name'] = M('teachers_basicinfo')->where('uid='.$data['uid'])->getField('name');
          // assign
          $this->assign('data',$data);
        }
        // display
        $this->display();
        die;
      }
      // data
      $data = $this->getData();
      // uid
      if( intval($_POST['uid']) ){
        if( $data['password'] ){
          M('user')->where('id='.intval($_POST['uid']))->save($data);
        }
        M('teachers_basicinfo')->where('uid='.intval($_POST['uid']))->save($data);
        echo alert('提交成功！',__APP__.'/Teacher/listss',6);
        die;
      }
      $uid = M('user')->add($data);
      if( $uid ){
        $data['uid'] = $uid;
        if( M('teachers_basicinfo')->add($data) ){
          echo alert('提交成功！',__APP__.'/Teacher/listss',6);
          die;
        }else{
          echo alert('提交失败！',$_SERVER['HTTP_REFERER'],5);
          die;
        }
      }else{
        echo alert('提交失败！',$_SERVER['HTTP_REFERER'],5);
        die;
      }
    }
    // 初始化数据
    private function getData(){
      $data = array();
      // uid
      if( intval($_POST['uid']) ){
        $data['cid'] = I('post.cid','','intval');
        $data['name'] = I('post.name','','strip_tags,htmlspecialchars');
        if( $_POST['password'] ){
          $data['password'] = invincibleEncrypt(I('post.password','','strip_tags,htmlspecialchars'));
        }
      }else{
        $data['type'] = I('post.type','','intval');
        $data['cid'] = I('post.cid','','intval');
        $data['name'] = I('post.name','','strip_tags,htmlspecialchars');
        $data['username'] = I('post.username','','strip_tags,htmlspecialchars');
        $data['password'] = invincibleEncrypt(I('post.password','','strip_tags,htmlspecialchars'));
        $data['ctime'] = time();
        $data['status'] = 0;
      }
      return $data;
    }
    // 返回班级数据
    public function returnClass(){
      // Ajax
      if( IS_AJAX === true ){
        $data = M('class')->where('pid='.$_POST['cid'])->order('sort ASC')->select();
        $this->ajaxReturn($data);
        die;
      }
    }
    // 验证用户名是否重复注册
    public function checkUsername(){
      // Ajax
      if( IS_AJAX === true ){
        $username = I('post.username','','strip_tags,htmlspecialchars');
        if( M('user')->where("username='$username'")->count() ){
          $this->ajaxReturn("<span style='color:red;'>用户名已经存在！</span>");
          die;
        }elseif( M('teachers_basicinfo')->where("cid=".$_POST['cid'])->count() ){
          $this->ajaxReturn("<span style='color:red;'>该班级已经分配负责人，请勿重复分配！</span>");
          die;
        }else{
          $this->ajaxReturn("true");
          die;
        }
      }
    }
    // 教师列表
    public function listss(){
      // pdata
      $pdata = M('class')->where('pid=0')->order('sort ASC')->select();
      $this->assign('pdata',$pdata);
      // id
      if( !$this->id ){
        $this->id = $pdata[0]['id'];
      }
      $this->assign('id',$this->id);
      // sdata
      $sdata = M('class')->where('pid='.$this->id)->order('sort ASC')->select();
      foreach( $sdata AS $k => $v ){
        $tbiRe = M('teachers_basicinfo')->where('cid='.$v['id'])->find();
        if( $tbiRe ){
          $sdata[$k]['name'] = $tbiRe['name'];
          $userRe = M('user')->where('id='.$tbiRe['uid'])->find();
        }else{
          $sdata[$k]['name'] = '暂无';
          $userRe['id'] = 0;
          $userRe['username'] = '暂无';
          $userRe['status'] = '暂无';
          $userRe['ctime'] = '暂无';
        }
        $sdata[$k]['uid'] = $userRe['id'];
        $sdata[$k]['username'] = $userRe['username'];
        $sdata[$k]['status'] = $userRe['status'];
        $sdata[$k]['ctime'] = $userRe['ctime'];
      }
      $this->assign('sdata',$sdata);
      // display
      $this->display();
    }
    // 冻结&激活
    public function freeze(){
      // GET
      if( IS_GET === true ){
        $uid = intval($_GET['uid']);
        $status = intval($_GET['status']);
        if( M('user')->where('id='.$uid)->save(array('status'=>$status)) ){
          echo alert('更新成功！',$_SERVER["HTTP_REFERER"],6);
          die;
        }else{
          echo alert('更新失败！',$_SERVER["HTTP_REFERER"],5);
          die;
        }
      }
    }
    // 删除班级负责人相关信息
    public function delss(){
      // GET
      if( IS_GET === true ){
        $uid = intval($_GET['uid']);
        if( M('teachers_basicinfo')->where('uid='.$uid)->delete() ){
          M('user')->where('id='.$uid)->delete();
          echo alert('删除成功！',$_SERVER["HTTP_REFERER"],6);
          die;
        }else{
          echo alert('删除失败！',$_SERVER["HTTP_REFERER"],5);
          die;
        }
        die;
      }
    }




}