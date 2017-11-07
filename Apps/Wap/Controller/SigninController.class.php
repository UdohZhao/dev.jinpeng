<?php
namespace Wap\Controller;
use Think\Controller;
class SigninController extends Controller {
    // 构造方法
    public $db;
    public function _initialize(){
        $this->db=M("user");
    }
    //历史周期页面
    public function index(){
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
        // Ajax
        if( IS_AJAX === true ){
        // data
            $data = $this->getData();
            // 防止重复注册
            $username = $data['username'];
            if( $this->db->where("username='$username'")->count() ){
                // echo alert('用户名已经存在,请勿重复注册!',__APP__.'/Signin/index',5);
                // die;
                $this->ajaxReturn(2);
                die;
            }
            $uid = $this->db->add($data);
            if( $uid ){
                // sdata
                $sdata = array();
                $sdata['uid'] = $uid;
                $sdata['cid'] = $_POST['cid'];
                if( M('students_basicinfo')->add($sdata) ){
                    $this->ajaxReturn(1);
                    // echo alert('注册成功!',__APP__.'/Login/index',6);
                    // die;
                }else{
                    $this->ajaxReturn(false);
                    // echo alert('注册失败!',__APP__.'/Signin/index',5);
                    // die;
                }
            }else{
                $this->ajaxReturn(false);
                // echo alert('注册失败!',__APP__.'/Signin/index',5);
                // die;
            }
        }
    }

    //初始化数据
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
}