<?php
namespace Wap\Controller;
use Think\Controller;
class ShistorycycleController extends BaseController {

    public $uid;
    public $cid;
    public $pcid;
    // 构造方法
    public function _auto(){
        $this->uid = $_SESSION['userinfo']['uid'];
        $userInfo=M("user")->where("id=$this->uid")->find();
        if($userInfo['type']==0){
            $Info=M("students_basicinfo")->where("uid=$this->uid")->find();
        }else{
            $Info=M("teachers_basicinfo")->where("uid=$this->uid")->find();
        }

        $this->cid=$Info['cid']; //当前班级id
        $classInfo=M("class")->where("id=$this->cid")->find();
        $this->pcid=$classInfo['pid'];  //当前年级id
    }
    //历史周期页面
    public function index(){
        //年级历史记录
        $this->uid = $_SESSION['userinfo']['uid'];
        $userInfo=M("user")->where("id=$this->uid")->find();
        if($userInfo['type']==0){//学生查询自己的所有周期
            $pcInfo=M("synthesize_estimate")->where("s.uid=$this->uid")
                ->field("s.pcid,s.uid,s.cid,c.cname")
                ->join("class as c on s.pcid=c.id")    //取得年级名字
                ->alias("s")
                ->group("s.pcid")
                ->select();
            $this->assign("pcInfo",$pcInfo);
        }elseif($userInfo['type']==1){//老师查询所有学生所有年级所有周期

        }

    	$this->display();
    }
}