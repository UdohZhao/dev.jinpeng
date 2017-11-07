<?php
namespace Wap\Controller;
use Think\Controller;
class SmeController extends BaseController {
    public $uid;
    public $db;
    // 构造方法
    public function _auto(){
        $this->uid = $_SESSION['userinfo']['uid'];
        if($_GET['uid']){  //菜单栏传入
            $this->uid=$_GET['uid'];
            $this->assign("teacherModel",1);
            $this->db=M("teachers_basicinfo");//老师信息
        }else{
            $this->db=M("students_basicinfo");
        }
    	$this->assign('Sme','1');
    }
    //我页面
    public function index(){

        $nameInfo=M("teachers_basicinfo")->where("uid=$this->uid")->find();
        if($nameInfo){  //查找到就是老师的信息
            $name['name']=$nameInfo['name'];
            $name['type']=1;
            $this->assign('name',$name);
        }
        $userInfo=$this->db->where("uid=$this->uid")->find();
        $classId=$userInfo['cid'];

        $period=M("period")->where("status=2")->find();
        $this->assign('period',$period);

        $classInfo=M("class")->where("id=$classId")->find();
        $pcid=$classInfo['pid'];

        $Scoreyear=M("synthesize_estimate")->where("cid=$classId and pcid=$pcid and uid=$this->uid")
            ->order("perid desc")
            ->field("grade,status")
            ->find();//当前用户，当前用户所在班级，本期数限制  
        $this->assign("Scoreyear",$Scoreyear);//本年得分

        $ScoreAll=M("synthesize_estimate")->where("uid=$this->uid")->sum("grade");//当前用户，无班级限制，无期数限制
        $this->assign("ScoreAll",$ScoreAll);
    	$this->display();
    }
}