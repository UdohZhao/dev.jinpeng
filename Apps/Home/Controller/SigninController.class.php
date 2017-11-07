<?php
namespace Home\Controller;
use Think\Controller;
class SigninController extends Controller {
    public $uid;
    public $db;
    // 构造方法
    public function _initialize(){
      $this->uid = $_SESSION['userinfo']['uid'];
      $this->db = M('user');
    }
    // 注册页面
    public function index(){
      // GET
      if( IS_GET === true ){
        // pdata
        $pdata = M('class')->where('pid=0')->order('sort ASC')->select();
        $this->assign('pdata',$pdata);
        $sdata = M('class')->where('pid='.$pdata[0]['id'])->select();
        $this->assign('sdata',$sdata);
        // display
        $this->display();
        die;
      }
      // data
      $data = $this->getData();
      // 防止重复注册
      $username = $data['username'];
      if( $this->db->where("username='$username'")->count() ){
        echo alert('用户名已经存在,请勿重复注册!',__APP__.'/Signin/index',5);
        die;
      }
      $uid = $this->db->add($data);
      if( $uid ){
        // sdata
        $sdata = array();
        $sdata['uid'] = $uid;
        $sdata['cid'] = $_POST['cid'];
        if( M('students_basicinfo')->add($sdata) ){
          echo alert('注册成功!',__APP__.'/Login/loading',6);
          die;
        }else{
          echo alert('注册失败!',__APP__.'/Signin/index',5);
          die;
        }
      }else{
        echo alert('注册失败!',__APP__.'/Signin/index',5);
        die;
      }
    }

    // 初始化数据
    private function getData(){
      $data = array();
      $data['username'] = I('post.username','','strip_tags,htmlspecialchars');
      $data['password'] = I('post.password','','strip_tags,htmlspecialchars');
      $data['type'] = I('post.type','','intval');
      $data['ctime'] = time();
      $data['status'] = 0;
      $data['password'] = invincibleEncrypt($data['password']);
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

    // 修改密码
    public function changePassword(){
      // Ajax
      if( IS_AJAX === true ){
        $password = I('post.password','','strip_tags,htmlspecialchars');
        $password = invincibleEncrypt($password);
        if(  $password == $_SESSION['userinfo']['password'] ){
          $this->ajaxReturn('true');
        }else{
          $this->ajaxReturn("<span style='color:red;'>原始密码错误！</span>");
        }
        die;
      }
      // 新密码
      $password = I('post.newPassword','','strip_tags,htmlspecialchars');
      $password = invincibleEncrypt($password);
      // update
      if( $this->db->where('id='.$this->uid)->save(array('password'=>$password)) ){
        echo alert('修改密码成功，请重新登录！',__APP__.'/Login/logout',6);
        die;
      }else{
        echo alert('修改密码失败！',$_SERVER['HTTP_REFERER'],5);
        die;
      }
    }


}