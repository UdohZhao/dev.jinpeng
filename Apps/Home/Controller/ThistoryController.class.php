<?php
namespace Home\Controller;
use Think\Controller;
class ThistoryController extends BaseController {
    public $uid;
    // 构造方法
    public function _auto(){
        $this->uid = $_SESSION['userinfo']['uid'];
        if($_SESSION['userinfo']['type']==0){
            header("Location:".U('Student/index'));
            exit;
        }
    }
    // 教师查看历史页面
    public function index(){
      // GET
      if( IS_GET === true ){
        // display

          $teacherInfo=M("teachers_basicinfo")->where("uid=$this->uid")->find();
          $cid=$teacherInfo['cid'];//老师所在班级id
          $info=M("class")->where("id=$cid")->find();//查找班级的父级id
          $pcid=$info['pid'];

          // 查询满足要求的总记录数
          $countNum=M('synthesize_estimate')->where("cid=$cid and pcid=$pcid")
                                        ->field("count(perid)")
                                        ->group("perid")
                                        ->select();
          $count=count($countNum);
          $Page       = new selfPage($count,5);// 实例化分页类 传入总记录数和每页显示的记录数(25)

          $show       = $Page->pages();// 分页显示输出
          $history=M("synthesize_estimate")
              ->where("s.cid=$cid and s.pcid=$pcid")
              ->join("period as p on p.id=s.perid")
              ->field("s.*,p.periods as pername")
              ->group("s.perid")
              ->alias("s")
              ->order("s.id desc")
              ->limit($Page->getStart(),5)
              ->select();

          $this->assign("history",$history);
          $this->assign("page",$show);
        $this->display();
        die;
      }
    }

}