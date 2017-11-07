<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends BaseController{
    //构造方法
    public $db;
    public function _initialize(){

        $this->db = M('admin_user');
    }

    public function loginAdmin(){
        if( IS_GET === true ){
            // display
            $this->display();
            die;
        }

        $data = $this->getData();
        // 查询是否为注册用户
        $username = $data['username'];
        $userinfo = $this->db->where("username='{$username}'")->find();
        if( $userinfo ){
            echo alert('该用户名已被注册!',__APP__.'/User/loginAdmin',5);
            die;
        }else{
            $arr=M("admin_user")->data($data)->add();
            if($arr){
             echo  alert("注册成功",__APP__."/User/loginAdmin",6);
            }
        }


    }

    // 初始化数据
    private function getData(){
        $data = array();
        $data['type']=intval(I('type'));
        $data['ctime']=time();
        $data['username'] = I('post.username','','strip_tags,htmlspecialchars');
        $data['password'] = I('post.password','','strip_tags,htmlspecialchars');
        $data['password'] = invincibleEncrypt($data['password']);
        return $data;
    }


    public function adminOper(){
        $info=M("admin_user")->select();
        $this->assign("info",$info);
        $this->display();
    }


    public function del(){
        $id=I("id");
        $re=M("admin_user")->where("id=$id")->delete();
        if($re){
            echo alert("删除成功",__APP__."/User/adminOper",6);
        }
    }

    public function update(){
        $id=I("id");
        $info=M("admin_user")->where("id=$id")->find();
        $this->assign('info',$info);
        $this->display();
    }
    public function save(){

            $id=$_GET['id'];

        if($id!=$_SESSION['user']['uid']){
            echo alert("只能修改当前用户个人信息",__APP__."/User/adminOper",5);
            exit;
        }
        $data=$this->getData();
        $arr=M("admin_user")->where("id=$id")->data($data)->save();
        if($arr){
            echo alert("修改成功",__APP__."/User/adminOper",6);
        }
    }

    public function dongjie(){
        $uid=$_SESSION['user']['uid'];
        $id=I("id");
        $info=M("admin_user")->where("id=$uid")->find();
        if($info['type']==1){
            $re=array('info'=>3);
            echo json_encode($re);
            exit;
        }
        $arr=M("admin_user")->where("id=$id")->data(array('status'=>1))->save();
        if($arr){
            $re=array('info'=>1);
        }else{
            $re=array('info'=>0);
        }

        echo json_encode($re);
    }
    public function jiedong(){
        $uid=$_SESSION['user']['uid'];
        $id=I("id");
        $info=M("admin_user")->where("id=$uid")->find();
        if($info['type']==1){
            $re=array('info'=>3);
            echo json_encode($re);
            exit;
        }
        $arr=M("admin_user")->where("id=$id")->data(array('status'=>0))->save();
        if($arr){
            $re=array('info'=>1);
        }else{
            $re=array('info'=>0);
        }

        echo json_encode($re);
    }
}