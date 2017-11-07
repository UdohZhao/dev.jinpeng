<?php
namespace Wap\Controller;
use Think\Controller;
class SSupplementaryqController extends BaseController {
    // 构造方法
    public $uid;
    public function _auto(){
        $this->uid = $_SESSION['userinfo']['uid'];
    }
    //补充题页面
    public function index(){
        if($_GET['perid']){
            $perid=$_GET['perid'];
        }else{
            $period = M('period')->where('status=2')->find();
            if(!$period){
                echo alert("没有正在评测的周期",__APP__."/Student/index",5);
                exit;
            }
            $perid=$period['id'];
        }
        $data=M("replenish_estimate")->where("uid=$this->uid and perid=$perid")
            ->field("content")
            ->find();
        if($data){
            $this->assign('data',$data);
        }elseif(!$data && $_GET['perid']){
            $data=array('info'=>2,'msg'=>"本期你没有补充评论");
            $this->assign('data',$data);
        }
        $this->display();
    }

    public function supplement(){

        $period = M('period')->where('status=2')->find();

        $perid=$period['id'];//正在测试的周期id

        $content=I("content");

        if(!$content){
            $msg="不能为空";
            $re=array('info'=>0,'msg'=>$msg);
            return $this->ajaxReturn($re);
            exit;
        }
        $uid=$this->uid;//用户id
        $basicInfo=M("students_basicinfo")->where("uid=$uid")->find();//学生基本信息
        $cid=$basicInfo['cid'];//学生所在班级id
        $classInfo=M("class")->where("id=$cid")->find();//学生所在班级信息
        $pid=$classInfo['pid'];//班级父id

        $data=array('uid'=>$uid,'pcid'=>$pid,
            'cid'=>$cid,'perid'=>$perid,
            'content'=>$content,'ctime'=>time(),
            'type'=>0,);
        $res=M("replenish_estimate")->data($data)->add();
        if($res>0){
            $msg="补充已提交，请等待老师评价后查询最终分数";
            $re=array('info'=>1,'msg'=>$msg);
            return $this->ajaxReturn($re);
        }
    }
}
