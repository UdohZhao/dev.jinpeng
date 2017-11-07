<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends BaseController {
    public $db;
    // 构造方法
    public function _initialize(){

        $this->db = M('admin_user');
    }


    // 登录页面
    public function index(){
        // GET
        if( IS_GET === true ){
            // display
            $this->display();
            die;
        }
        $data = $this->getData();
        // 查询是否为注册用户
        $username = $data['username'];
        $password = $data['password'];
        $userinfo = $this->db->where("username='$username' AND password='$password'")->find();
        if( $userinfo ){
            // status 0=>正常，1=>冻结
            if( $userinfo['status'] == 1 ){
                echo alert('该用户名已被冻结,请自行联系管理员!',__APP__.'/Login/loading',5);
                die;
            }

            // 用户信息存入session
            $_SESSION['user']['uid'] = $userinfo['id'];
            $_SESSION['user']['status'] = $userinfo['status'];
            $_SESSION['user']['type'] = $userinfo['type'];
            $_SESSION['user']['username'] = $username;
            $_SESSION['user']['password'] = $password;
            header("Location:".U('Index/index'));
        }else{
            echo alert('用户名或者密码错误!',__APP__.'/Login/index',5);
            die;
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

    // 退出
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