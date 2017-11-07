<?php
namespace Wap\Controller;
use Think\Controller;
class LoginController extends Controller {

    public $id;
    public $db;
    // 构造方法
    public function _initialize(){
        if( $_SESSION['userinfo']['uid'] ){
            // type
            if( $_SESSION['userinfo']['type'] == 1 ){
                header('Location:'.U('Teacher/index'));
            }else{
                header('Location:'.U('Student/index'));
            }
        }
        $this->id = intval($_GET['id']);
        $this->db = M('user');

        $infoSite=M("site")->order("id desc")->find();

        $this->assign('infoSite',$infoSite);
    }
    //登陆页面
    public function index(){
        // GET
        if( IS_GET === true ){
            // display
            $this->display();
            die;
        }
        // Ajax
        if( IS_AJAX === true ){
            $data = $this->getData();
            // 查询是否为注册用户
            $username = $data['username'];
            $password = $data['password'];
            $userinfo = $this->db->where("username='$username' AND password='$password'")->find();
            if( $userinfo ){
                // status 0=>正常，1=>冻结
                if( $userinfo['status'] == 1 ){
                    $re=array('info'=>4,'msg'=>'该用户名已被冻结,请自行联系管理员!');
                    $this->ajaxReturn($re);
                                        die;
                }
                // 用户信息存入session
                $_SESSION['userinfo']['uid'] = $userinfo['id'];
                $_SESSION['userinfo']['status'] = $userinfo['status'];
                $_SESSION['userinfo']['type'] = $userinfo['type'];
                $_SESSION['userinfo']['username'] = $username;
                $_SESSION['userinfo']['password'] = $password;
                // type 0=>学生，1=>教师
                if( $userinfo['type'] == 1 ){
                    $this->ajaxReturn(1);
                    //header("Location:".U('Teacher/index'));
                }else{
                    $this->ajaxReturn(0);
                    //header("Location:".U('Student/index'));
                }
            }else{
                $this->ajaxReturn(false);
                //echo alert('用户名或者密码错误!',__APP__.'/Login/index',5);
            }
        }
    }

    // 初始化数据
    private function getData(){
        $data = array();
        $data['username'] = I('post.username','','strip_tags,htmlspecialchars');
        $data['password'] = I('post.password','','strip_tags,htmlspecialchars');
        $data['password'] = invincibleEncrypt($data['password']);
        return $data;
    }

    public function logout(){
        // 销毁所有session
        session('[destroy]');
        header('Location:'.U('Login/index'));
    }

    //验证码
    public function verifyCode(){
        // GET
        if( IS_GET === true ){

            ob_clean();
            $Verify =     new \Think\Verify();
            $Verify->fontSize = 30;
            $Verify->length   = 4;
            $Verify->useNoise = false;
            $Verify->entry();
            die;
        }
        // 核对验证码
        if(check_verify($_POST['code'])){
            $this->ajaxReturn(true);
        }else{
            $this->ajaxReturn(false);
        }
    }

}