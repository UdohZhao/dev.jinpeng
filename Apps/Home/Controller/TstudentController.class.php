<?php
namespace Home\Controller;
use Think\Controller;
class TstudentController extends BaseController {
    // 构造方法
    public function _auto(){
        if($_SESSION['userinfo']['type']==0){
            header("Location:".U('Login/loading'));
            exit;
        }
    }
    // 教师查看学生每期评分页面
    public function index(){
      // GET
      if( IS_GET === true ){
        // display
          $uid=I("uid");//点击查看的用户id
          $studentInfo=M("students_basicinfo")->where("uid=$uid")->find();
          $cid=$studentInfo['cid'];//班级id
          $classInfo=M("class")->where("id=$cid")->find();
          $pcid=$classInfo['pid'];//班级父级id
          $count      = M("synthesize_estimate")->where("uid=$uid  and cid=$cid and pcid=$pcid")->count();  // 查询满足要求的总记录数
          $pageSize=5;
          $Page       = new selfPage($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数(25)

          $show       = $Page->pages();// 分页显示输出
          // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
          $history=M("synthesize_estimate")
                    ->where("s.uid=$uid and s.cid=$cid and s.pcid=$pcid")//点击的学生用所在班级的历史记录
                    ->join("period as p on p.id=s.perid")
                    ->field("p.periods,s.*")
                    ->alias("s")
                    ->order("s.id desc")
                    ->limit($Page->getStart(),$pageSize)
                    ->select();
          $Counthistory=M("synthesize_estimate")
                      ->where("uid=$uid and cid=$cid and pcid=$pcid")//点击的学生用所在班级的历史记录
                      ->order("id desc")
                      ->select();
          $num=count($Counthistory);

          $p=$_GET['p'];//页数
          foreach($history as $key=>$val){

              if(!$history[$key+1]['grade']){
                if($p==NULL && $num>5){ //第一进去,没有页面p值
                        $p=1;
                }elseif($p==NULL && $num<=5){
                    $Counthistory[($p-1)*$pageSize+$key+1]['grade']=0;
                }
                  $comfirm=$history[$key]['grade']-$Counthistory[($p-1)*$pageSize+$key+1]['grade'];
              }
             else{
                 $comfirm=$history[$key]['grade']-$history[$key+1]['grade'];

             }

              if($comfirm>0){
                  $history[$key]['addUp']="上升".$comfirm."分";
              }else if($comfirm==0){
                  $history[$key]['addUp']="平稳";
              }else{
                  $history[$key]['addUp']="下降".abs($comfirm)."分";
              }
          }
          $name=M("students_basicinfo")->where("uid=$uid")->field("name")->find();
          $this->assign("name",$name);
            /*var_dump($history);exit;*/
          $this->assign("history",$history);

          $this->assign('page',$show);// 赋值分页输出
        $this->display();
        die;
      }
    }
}