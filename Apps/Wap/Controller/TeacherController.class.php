<?php
namespace Wap\Controller;
use Think\Controller;
class TeacherController extends BaseController {
    // 构造方法
    public $uid;
    public function _auto(){
        $this->uid = $_SESSION['userinfo']['uid'];
        if($_SESSION['userinfo']['type']==0){
            header("Location:".U('Student/index'));
        }
      $this->assign('Teacher','1');
    }
    //老师主页面
    public function index(){


                // 查询用户基本信息和所在年级和班级
                $teachersBaseinfo = M('teachers_basicinfo')->where('uid='.$this->uid)->find();
                $teachersClass = M('class')->where('id='.$teachersBaseinfo['cid'])->find();
                $teachersGrade = M('class')->where('id='.$teachersClass['pid'])->find();

                // 教师基本信息存入session
                $_SESSION['userinfo']['name'] = $teachersBaseinfo['name'];
                $_SESSION['userinfo']['class'] = $teachersClass['cname'];  //班级
                $_SESSION['userinfo']['Pclass'] = $teachersGrade['cname'];  //年级

                // display
                //查询当前老师所在班级所有历史周期  综合评价表
                $cid=$teachersBaseinfo['cid'];//教师所在班级id
                $classInfo=M("class")->where("id=$cid")->find();//学生所在班级信息
                $pcid=$classInfo['pid']; //班级父id

                if($_GET['perid']){
                    $perid=$_GET['perid'];
                    $period=M("period")->where("id=$perid")->find();//查询要求查询的周期
                }else{
                    $period=M("period")->where("status=2")->find();//查询正在进行的周期
                    if(!$period){
                        $period=M("period")->order("id desc")->limit(1)->find();
                    }
                    $perid=$period['id'];
                }
                $this->assign("period",$period);


                $estimate = M('synthesize_estimate'); // 实例化对象

                // 查询满足要求的总记录数
                $count=M('synthesize_estimate')->where("cid=$cid and pcid=$pcid and perid=$perid")->count();

                $Page       = new selfPage($count,5);// 实例化分页类 传入总记录数和每页显示的记录数(25)

                $show       = $Page->pages();// 分页显示输出
                // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
                $history = $estimate->where("s.cid=$cid and s.pcid=$pcid and s.perid=$perid")
                    ->field("s.*,b.name as studentname")
                    ->join("join students_basicinfo as b on s.uid=b.uid")
                    ->alias("s")
                    ->order("s.id")
                    ->limit($Page->getStart(),5)
                    ->select();
                /*var_dump($estimate->getLastSql());exit;*/

                $supplement=M("replenish_estimate")
                    ->where("cid=$cid and pcid=$pcid and perid=$perid")
                    ->field("uid,type,content")
                    ->select();//补充表
                foreach($history as $key=>$val){
                    foreach($supplement as $Skey=>$Sval){
                        if($supplement[$Skey]['uid']==$history[$key]['uid']){
                            $history[$key]['suppType']=$supplement[$Skey]['type'];
                        }
                    }
                }
                //计算各种类型的评价总分
                $patriarchTotal="";       //家长
                $egoTotal="";             //自我
                $schoolmateTotal="";      //同学
                $evaluateTotal="";      //评价总分
                $Totalhistory = $estimate->where("cid=$cid and pcid=$pcid and perid=$perid")->select();

                foreach ($Totalhistory as $Tkey=>$Tval){
                    $patriarchTotal=bcadd($patriarchTotal,$Tval['patriarch'],1);//家长评价总分
                    $egoTotal=bcadd($egoTotal,$Tval['ego'],1);              //自我评价总分
                    $schoolmateTotal=bcadd($schoolmateTotal,$Tval['schoolmate'],1);//同学评价总分
                    $evaluateTotal=bcadd($evaluateTotal,$Tval['grade'],1);
                }

                //计算每种类型的平均分
                $numTotal=count($Totalhistory);//个数

                $averageScoreEgo=bcdiv($egoTotal,$numTotal,1);
                $averageScorePatriarch=bcdiv($patriarchTotal,$numTotal,1);
                $averageScoreSchoolmate=bcdiv($schoolmateTotal,$numTotal,1);
                $averageScoreEvaluate=bcdiv($evaluateTotal,$numTotal,1);
                $needScore=array();

                $needScore['egoAllScore']=$egoTotal;//自我评价总分
                $needScore['average1']=$averageScoreEgo;//自我评价平均分
                $needScore['patriarchAllScore']=$patriarchTotal;//家长评价总分分
                $needScore['average0']=$averageScorePatriarch;//家人评价总分
                $needScore['schoolmateAllScore']=$schoolmateTotal;//同学评价总分
                $needScore['average2']=$averageScoreSchoolmate;//同学评价平均分
                $needScore['gradeAllScore']=$evaluateTotal;//评价总分
                $needScore['average3']=$averageScoreEvaluate;//评价平均分

                //该班级所有周期评价总分
                $perAllScore=M("synthesize_estimate")->where("cid=$cid and pcid=$pcid")
                    ->field("sum(grade) as gr,avg(grade) as avgr")
                    ->find();//所有周期所有同学的评分总分,平均得分

                $perAllNum=M("synthesize_estimate")->where("cid=$cid and pcid=$pcid")
                    ->field("count(perid)")
                    ->group("perid")
                    ->select();//统计共有多少个周期
                $needPerNum=count($perAllNum);

                $avgScore=bcdiv($perAllScore['gr'],$needPerNum,2);//班级每个周期所有同学总分平均分

                if(bccomp($needScore['gradeAllScore'],$avgScore,2)>0){
                    $needScore['comfire']="高于所有周期平均值";
                }else if(bccomp($needScore['gradeAllScore'],$avgScore,2)==0){
                    $needScore['comfire']="等于所有周期平均值";
                } else{
                    $needScore['comfire']="低于于所有周期平均值";
                }

                if(bccomp($needScore['average3'],$perAllScore['avgr'],2)>0){
                    $needScore['comfireGr']="高于所有周期平均值";
                }else if(bccomp($needScore['average3'],$perAllScore['avgr'],2)==0){
                    $needScore['comfireGr']="等于所有周期平均值";
                }else{
                    $needScore['comfireGr']="低于所有周期平均值";
                }


                $this->assign("needScore",$needScore);

                $this->assign('history',$history);// 赋值数据集
                $this->assign('page',$show);// 赋值分页输出
                $this->display();
    }


    //计算分数
    public function calculate(){
        $ratio=$_POST['Etype'];    //优y  良l  中z  差c
        $type=$_POST['type'];
        $id=$_POST['id'];          //评价表id
        $uid=$_POST['uid'];//学生uid

        $allScrore=bcadd(($_POST['ego']+$_POST['patriarch']),$_POST['schoolmate'],1);

        $arrRatio=M("estimate_config")->field("$ratio")->where("type=$type")->find();
        $needRatio=$arrRatio["$ratio"];//所需计算比例

        $finalScore=bcdiv(bcmul($allScrore,bcdiv($needRatio,100,2),2),10,1);//最终得分取整数部分
        if($ratio=='y'){
            $status=1;
        }else if($ratio=='l'){
            $status=2;
        }else if($ratio=='z'){
            $status=3;
        }else if($ratio=='c'){
            $status=4;
        }

        $data=array('grade'=>$finalScore,'status'=>$status);
        $arr=M("synthesize_estimate")->where("id=$id")->data($data)->save();  //最终分数入综合评价表
        $studentUp=M("students_basicinfo")->where("uid=$uid")->find();//学生信息表查询当前累计分数
        $addup=bcadd($studentUp['addup'],$finalScore,1);     //学生信息累计评分
        $studentUp=M("students_basicinfo")->where("uid=$uid")
            ->data(array('addup'=>$addup))
            ->save();//学生评价累计分数

        if($arr>=0){

            $re=array('info'=>$finalScore,'msg'=>$ratio);
        }
        echo json_encode($re);
    }
}