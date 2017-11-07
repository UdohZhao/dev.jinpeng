<?php
namespace Wap\Controller;
use Think\Controller;
class SthecycleController extends BaseController {
    // 构造方法
    public $uid;
    public function _auto(){
        $this->uid= $_SESSION['userinfo']['uid'];
    	$this->assign('Sthecycle','1');
    }
    //当年周期页面
    public function index(){

        if($_GET['uid']){
            $uid=$_GET['uid'];  //传入用户id
        }else{
            $uid=$this->uid;
        }
        $typeArr=M("user")->where("id=$uid")->find();
        $Usertype=$typeArr['type'];

        if($Usertype==0){//学生
           $infoSel=M("students_basicinfo");

        }else{
            $this->assign("teacherModel",1);
            $infoSel=M("teachers_basicinfo");
        }
        $classInfo=$infoSel->where("uid=$uid")->find();
        $cid=$classInfo['cid'];
        $infoC=M("class")->where("id=$cid")->find();
        $pcid=$infoC['pid'];
        //若是点击历史周期传入年级信息id
        if($_GET['pcid']){
            $pcid=$_GET['pcid'];
        }
        $where="";
        if($Usertype==0){  //若判断类型未学生，查询当前传入用户的本年级周期
            $where="pcid={$pcid} AND cid={$cid} AND uid={$uid}";
        }else{          //若判定类型为老师，查询当前传入用户所在班级，所在年级的所有周期
            $where="pcid={$pcid} AND cid={$cid}";
            $this->assign('status',1);
        }
        if($_POST['content']){
            $arr=M("period")->where("periods like '%{$_POST['content']}%'")->select();
            foreach($arr as $v){
                $needId[]=$v['id'];
            }
            $id=implode(",",$needId);

            if($id){
                $where.=" and perid in ($id)";
            }else{
              echo  alert("没有找到相关期数",__APP__."/Sthecycle/index",5);
                exit;
            }
        }
        $estimate=M("synthesize_estimate");
        $info=$estimate->where("$where")//当前年级当前班级
            ->join("period ON synthesize_estimate.perid=period.id")
            ->field("synthesize_estimate.*,period.periods")
            ->group("synthesize_estimate.perid")
            ->order("end_time desc")
            ->select();
        $this->assign('info',$info);

    	$this->display();


    }
}