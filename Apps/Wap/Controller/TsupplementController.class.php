<?php
namespace Wap\Controller;
use Think\Controller;
class TsupplementController extends BaseController {
    // 构造方法
    public function _auto(){

    }
    //老师主页面
    public function index(){

        $uid=I("uid");  //用户id
        $basicInfo=M("students_basicinfo")->where("uid=$uid")->find();//学生基本信息
        $cid=$basicInfo['cid'];//学生所在班级id
        $classInfo=M("class")->where("id=$cid")->find();//学生所在班级信息
        $pid=$classInfo['pid'];//班级父id

        //$period=M("period")->where("status=2")->find();//正在测评的周期
        $perid=$_GET['perid'];
        $content=M("replenish_estimate")
            ->where("r.uid=$uid and r.perid=$perid and r.cid=$cid and r.pcid=$pid")
            ->field("r.*,b.name as studentname")
            ->join("join students_basicinfo as b on b.uid=r.uid")
            ->alias("r")
            ->find();

        $this->assign("content",$content);
        $this->display();
    }

    //补充评分计算分数

    public function calculate(){

        $id=I("id");   //补充评分id
        $info=M("replenish_estimate")->where("id=$id")->find();
        $uid=$info['uid'];  //用户id

        $cid=$info['cid'];  //班级id
        $pcid=$info['pcid'];//班级父级id
        $perid=$info['perid'];//周期id

        $perInfo=M("period")->where("id=$perid")->find();
        if($perInfo['status']!=2){
            $re=array('info'=>5,'msg'=>'已过期');//未进行综合评分，
            echo json_encode($re);
            exit;
        }

        $allScore=M("synthesize_estimate")
            ->where("uid=$uid and cid=$cid and pcid=$pcid and perid=$perid")
            ->find();
        /*$re=array("info"=>$allScore['grade']);*/


        if($allScore['status']==0){
            $re=array('info'=>0);//未进行综合评分，
            echo json_encode($re);
            exit;
        }

        $Sid=$allScore['id'];  //综合评分的id
        $score=$allScore['grade'];//综合评分
        $ratio=$_POST['Etype'];    //优y  良l  中z  差c
        $type=$_POST['type'];

        $arrRatio=M("estimate_config")->field("$ratio")->where("type=$type")->find();

        $needRatio=$arrRatio["$ratio"];//所需计算比例
        $ratioScore=$score*($needRatio/100);//补充评分结果
        $finalScore=$ratioScore+$score;// 最终评分结果  补充分加上综合分数
        if($ratio=='y'){
            $status=1;
        }else if($ratio=='l'){
            $status=2;
        }else if($ratio=='z'){
            $status=3;
        }else if($ratio=='c'){
            $status=4;
        }
        $data=array('grade'=>$finalScore);


        $arr=M("synthesize_estimate")->where("id=$Sid")->data($data)->save();//最终评分入综合评分表

        $studentUp=M("students_basicinfo")->where("uid=$uid")->find();//学生信息表查询当前累计分数
        $addup=$studentUp['addup']+$ratioScore;     //学生信息累计评分
        $studentUp=M("students_basicinfo")->where("uid=$uid")
            ->data(array('addup'=>$addup))
            ->save();//学生评价累计分数
        $dataR=array('grade'=>$ratioScore,'status'=>$status,'type'=>1);
        $arrR=M("replenish_estimate")->where("id=$id")->data($dataR)->save();//补充评分入补充评分表
        if($arr>=0 && $arrR){
            $re=array('info'=>1);//已对补充评分，并重新计算分数
        }else{
            $re=array('info'=>2);
        }
        echo json_encode($re);
    }
}