<?php
namespace Wap\Controller;
use Think\Controller;
class StudentController extends BaseController {
    public $uid;
    // 构造方法
    public function _auto(){
        if($_GET['uid']){   //若获取到用户uid，即为老师查询学生页面
            $this->uid=$_GET['uid'];
        }else{
            $this->uid = $_SESSION['userinfo']['uid'];
        }
    	$this->assign('Student','1');
    }
    // 学生主页面
    public function index(){



      // display
// 查询用户所在年级和班级
        $studentBaseinfo = M('students_basicinfo')->where('uid='.$this->uid)->find();
        //引入底部footer
        if($_GET['TeacherType']==1){
            $this->assign('teacherModel',1);
            $oldperiod=M("period")->where("id={$_GET['perid']}")->find();
            $oldid=$oldperiod['periods'];
            $this->assign('oldid',$oldid);//老师查看学生已做的周期名称
            $this->assign("studentBaseinfo",$studentBaseinfo);
        }

        if($_GET['studentModel']==1){
            $this->assign('studentModel',1);
            $oldperiod=M("period")->where("id={$_GET['perid']}")->find();
            $this->assign("perid",$_GET['perid']);
            $oldid=$oldperiod['periods'];
            $this->assign('oldid',$oldid);//老师查看学生已做的周期名称
            $this->assign("studentBaseinfo",$studentBaseinfo);
        }

        $studentClass = M('class')->where('id='.$studentBaseinfo['cid'])->find();
        $studentGrade = M('class')->where('id='.$studentClass['pid'])->find();
        // 低段 中段 高段
        switch ($studentGrade['id']) {
            case '1':
                $_SESSION['userinfo']['ctype'] = 0;
                break;
            case '2':
                $_SESSION['userinfo']['ctype'] = 0;
                break;
            case '3':
                $_SESSION['userinfo']['ctype'] = 1;
                break;
            case '4':
                $_SESSION['userinfo']['ctype'] = 1;
                break;
            case '5':
                $_SESSION['userinfo']['ctype'] = 2;
                break;
            case '6':
                $_SESSION['userinfo']['ctype'] = 2;
                break;
            default:
                $_SESSION['userinfo']['ctype'] = 0;
                break;
        }
        // 学生基本信息存入session
        if( $studentBaseinfo['name'] == '' ){
            $_SESSION['userinfo']['name'] = 1;
        }else{
            $_SESSION['userinfo']['name'] = $studentBaseinfo['name'];
        }
        $_SESSION['userinfo']['class'] = $studentClass['cname'];  //班级
        $_SESSION['userinfo']['Pclass'] = $studentGrade['cname'];  //年级
        $_SESSION['userinfo']['addup'] = $studentBaseinfo['addup'];

        //年级历史记录
        $pcInfo=M("synthesize_estimate")->where("s.uid=$this->uid")
            ->field("s.pcid,s.uid,s.cid,c.cname")
            ->join("class as c on s.pcid=c.id")
            ->alias("s")
            ->group("s.pcid")
            ->select();
        $this->assign("pcInfo",$pcInfo);

        //获取历史记录id值  查询用户id对应的周期测评

        $getPerid=$_GET['perid'];

        //调出试题
        //正在测评的周期
        $period = M('period')->where('status=2')->find();


        $perid=$period['id'];

        //用户当前班级信息
        $classInfo=M("students_basicinfo")->where("uid=$this->uid")->find();
        $cid=$classInfo['cid'];
        $info=M("class")->where("class")->where("id=$cid")->find();
        $pcid=$info['pid'];


        //没有正在测评的周期
        if($period=="" && !$getPerid){  //没有测试周期，没有要查询的周期
            $this->assign('period',$period);
            $this->display();
            exit;
        }else if($getPerid && $_GET['cid'] && $_GET['pcid']){
            //若是查询历史周期，传入历史年级班级id值，历史周期id值
            $cid=$_GET['cid'];
            $pcid=$_GET['pcid'];
            $period['id']=$getPerid;
            $perid=$getPerid;
        }else if($_GET['uid'] && $_GET['perid']){ //若是有要查询的周期
            $period['id']=$_GET['perid'];
            $perid=$getPerid;
        }
        $this->assign('period',$period);
        //查询已作答试题

            //家长评价
            $answerDetail0=M("estimate")->where("uid=$this->uid and cid=$cid and pcid=$pcid and perid=$perid and type=0")
                                        ->field("qid,keyss,valss,type")
                                        ->select();
            if($answerDetail0){
                $type=0;//家长类型
            foreach($answerDetail0 as $key=>$val){
                //$val['type']试题类型  自评 家长  同学
                $needId[$val['type']]=$val['qid'];

                $needAnswer=unserialize($val['keyss']);
                foreach ($needAnswer as $Nkey=>$Nval){
                    foreach ($Nval as $Lkey=>$Lval){//针对多选题
                        $arrKey[$Nkey][]=$Nval[$Lkey];//所选答案数组
                    }
                }

                $needValss=unserialize($val['valss']);
                foreach ($needValss as $Dkey=>$Dval){
                    foreach ($Dval as $Lkeys=>$Lvals){//针对多选题
                        $arrVal[$Dkey][]=$Dval[$Lkeys];//所选答案数组
                    }
                }
            }
            /*var_dump($arrKey);exit;
            var_dump($needAnswer);exit;*/

            $idStr=$needId[$type];

            //该同学作答该周期该类型所有试题

            $question=M("question_bank")->where("perid=$perid and id in({$idStr})")->select();

            foreach($question as $k=>$v){
                $question[$k]['keyss']=unserialize($v['keyss']);//反序列化试题答案选项
                $question[$k]['valss']=unserialize($v['valss']);//反序列化试题答案选项
                foreach($arrKey as $Aks=>$Avs){
                    if($v['id']==$Aks){
                        $question[$k]['selAnswer']=$Avs; //所选答案
                        $a=$question[$k]['keyss'];//试题答案选项  一维数组
                        $b=$question[$k]['selAnswer'];//所选答案  一维数组
                        foreach($a as $Pkey=>$Pval){
                            foreach($b as $Bkey=>$Bval){
                                if($Pval==$Bval){
                                    $question[$k]['selStatus'][$Pkey][]=1;//若选了该答案，则状态判定为1
                                }
                            }
                        }
                    }
                }

                foreach($arrVal as $Vks=>$Vvs){
                    if($v['id']==$Vks){
                        $question[$k]['selValss']=$Vvs;  //所选答案得分
                    }
                }
                $s=$question[$k]['selValss'];
                foreach($s as $Sval){
                    $question[$k]['finalScore']+=$Sval;
                }

            }
            if($_GET['perid']){
                $Pquestion=$question;
                $this->assign('Pquestion',$Pquestion);//查询历史周期的时候
            }else{
                $this->assign('question',$question);
            }

          }else{                //否则传试题
                //家长评价
                $type0=0;
                $this->assign('type0',$type0);
                $limit0= "0,".(C('QUESTIONS_AMOUNT')+1);
                $data0 = M('question_bank')->where('perid='.$period['id'].' AND type='.$_SESSION['userinfo']['ctype'].' AND status=0')->order('sort ASC')->limit($limit0)->select();
                foreach( $data0 AS $k => $v ){
                    $data0[$k]['keyss'] = unserialize($v['keyss']);
                    $data0[$k]['valss'] = unserialize($v['valss']);
                }
                $this->assign("data0",$data0);
            }

            //自我评价
            $answerDetail1=M("estimate")->where("uid=$this->uid and cid=$cid and pcid=$pcid and perid=$perid and type=1")
                ->field("qid,keyss,valss,type")
                ->select();
            if($answerDetail1){
                $type=1;//家长类型
                foreach($answerDetail1 as $key=>$val){
                    //$val['type']试题类型  自评 家长  同学
                    $needId[$val['type']]=$val['qid'];

                    $needAnswer=unserialize($val['keyss']);
                    foreach ($needAnswer as $Nkey=>$Nval){
                        foreach ($Nval as $Lkey=>$Lval){//针对多选题
                            $arrKey[$Nkey][]=$Nval[$Lkey];//所选答案数组
                        }
                    }

                    $needValss=unserialize($val['valss']);
                    foreach ($needValss as $Dkey=>$Dval){
                        foreach ($Dval as $Lkeys=>$Lvals){//针对多选题
                            $arrVal[$Dkey][]=$Dval[$Lkeys];//所选答案数组
                        }
                    }
                }
                /*var_dump($arrKey);exit;
                var_dump($needAnswer);exit;*/

                $idStr=$needId[$type];

                //该同学作答该周期该类型所有试题

                $question1=M("question_bank")->where("perid=$perid and id in({$idStr})")->select();

                foreach($question1 as $k=>$v){
                    $question1[$k]['keyss']=unserialize($v['keyss']);//反序列化试题答案选项
                    $question1[$k]['valss']=unserialize($v['valss']);//反序列化试题答案选项
                    foreach($arrKey as $Aks=>$Avs){
                        if($v['id']==$Aks){
                            $question1[$k]['selAnswer']=$Avs; //所选答案
                            $a=$question1[$k]['keyss'];//试题答案选项  一维数组
                            $b=$question1[$k]['selAnswer'];//所选答案  一维数组
                            foreach($a as $Pkey=>$Pval){
                                foreach($b as $Bkey=>$Bval){
                                    if($Pval==$Bval){
                                        $question1[$k]['selStatus'][$Pkey][]=1;//若选了该答案，则状态判定为1
                                    }
                                }
                            }
                        }
                    }

                    foreach($arrVal as $Vks=>$Vvs){
                        if($v['id']==$Vks){
                            $question1[$k]['selValss']=$Vvs;  //所选答案得分
                        }
                    }
                    $s=$question1[$k]['selValss'];
                    foreach($s as $Sval){
                        $question1[$k]['finalScore']+=$Sval;
                    }

                }
                if($_GET['perid']){
                    $Pquestion1=$question1;
                    $this->assign('Pquestion1',$Pquestion1);//查询历史周期的时候
                }else{
                    $this->assign('question1',$question1);
                }

            }else{          //否则传试题
                //自我评价
                $type1=1;
                $this->assign('type1',$type1);
                $limit1=  (C('QUESTIONS_AMOUNT')+1) .','. C('QUESTIONS_AMOUNT');
                $data1 = M('question_bank')->where('perid='.$period['id'].' AND type='.$_SESSION['userinfo']['ctype'].' AND status=0')->order('sort ASC')->limit($limit1)->select();
                foreach( $data1 AS $k => $v ){
                    $data1[$k]['keyss'] = unserialize($v['keyss']);
                    $data1[$k]['valss'] = unserialize($v['valss']);
                }
                $this->assign("data1",$data1);
            }

            //同学评价
            $answerDetail2=M("estimate")->where("uid=$this->uid and cid=$cid and pcid=$pcid and perid=$perid and type=2")
                ->field("qid,keyss,valss,type")
                ->select();
            if($answerDetail2){
                $type=2;//家长类型
                foreach($answerDetail2 as $key=>$val){
                    //$val['type']试题类型  自评 家长  同学
                    $needId[$val['type']]=$val['qid'];

                    $needAnswer=unserialize($val['keyss']);
                    foreach ($needAnswer as $Nkey=>$Nval){
                        foreach ($Nval as $Lkey=>$Lval){//针对多选题
                            $arrKey[$Nkey][]=$Nval[$Lkey];//所选答案数组
                        }
                    }

                    $needValss=unserialize($val['valss']);
                    foreach ($needValss as $Dkey=>$Dval){
                        foreach ($Dval as $Lkeys=>$Lvals){//针对多选题
                            $arrVal[$Dkey][]=$Dval[$Lkeys];//所选答案数组
                        }
                    }
                }
                /*var_dump($arrKey);exit;
                var_dump($needAnswer);exit;*/

                $idStr=$needId[$type];

                //该同学作答该周期该类型所有试题

                $question2=M("question_bank")->where("perid=$perid and id in({$idStr})")->select();

                foreach($question2 as $k=>$v){
                    $question2[$k]['keyss']=unserialize($v['keyss']);//反序列化试题答案选项
                    $question2[$k]['valss']=unserialize($v['valss']);//反序列化试题答案选项
                    foreach($arrKey as $Aks=>$Avs){
                        if($v['id']==$Aks){
                            $question2[$k]['selAnswer']=$Avs; //所选答案
                            $a=$question2[$k]['keyss'];//试题答案选项  一维数组
                            $b=$question2[$k]['selAnswer'];//所选答案  一维数组
                            foreach($a as $Pkey=>$Pval){
                                foreach($b as $Bkey=>$Bval){
                                    if($Pval==$Bval){
                                        $question2[$k]['selStatus'][$Pkey][]=1;//若选了该答案，则状态判定为1
                                    }
                                }
                            }
                        }
                    }
                    foreach($arrVal as $Vks=>$Vvs){
                        if($v['id']==$Vks){
                            $question2[$k]['selValss']=$Vvs;  //所选答案得分
                        }
                    }
                    $s=$question2[$k]['selValss'];
                    foreach($s as $Sval){
                        $question2[$k]['finalScore']+=$Sval;
                    }

                }
                if($_GET['perid']){
                    $Pquestion2=$question2;
                    $this->assign('Pquestion2',$Pquestion2);//查询历史周期的时候
                }else{
                    $this->assign('question2',$question2);
                }
            }else{   //否则传试题
                //同学评价
                $type2=2;
                $this->assign('type2',$type2);
                $limit2= (C('QUESTIONS_AMOUNT')+1)+C('QUESTIONS_AMOUNT') .','.C('QUESTIONS_AMOUNT');
                $data2 = M('question_bank')->where('perid='.$period['id'].' AND type='.$_SESSION['userinfo']['ctype'].' AND status=0')->order('sort ASC')->limit($limit2)->select();
                foreach( $data2 AS $k => $v ){
                    $data2[$k]['keyss'] = unserialize($v['keyss']);
                    $data2[$k]['valss'] = unserialize($v['valss']);
                }
                $this->assign("data2",$data2);
            }

            $this->display();
    }


    public function saveAnswer(){

        $questionId=$_POST['id'];//当前页面所有试题的id;

        unset($_POST['id']);
        $periodId=$_POST['periods'];//获取周期数id
        unset($_POST['periods']);
        $type=$_POST['types'];//获取评价类型
        unset($_POST['types']);

        //用户信息
        $uid=$this->uid;//用户id
        $basicInfo=M("students_basicinfo")->where("uid=$uid")->find();//学生基本信息
        $smUid=$basicInfo['id'];//评价同学id
        $cid=$basicInfo['cid'];//学生所在班级id
        $classInfo=M("class")->where("id=$cid")->find();//学生所在班级信息
        $pid=$classInfo['pid'];//班级父id

        $answer=array();
        foreach($_POST as $keyP=>$vP){  //$key: 题目的id  $v:选择的相应答案    若是没选做的题目，就没有数组信息
            $dataId[]=$keyP; //传入的对应题目id
            $answer[$keyP]=$vP;//传入的对应选择的答案数组，相应试题id,二维
        }

        //遍历页面所有试题Id
        foreach($questionId as $Qkey=>$Qval){
            foreach($dataId as $Dkey=>$Dval){
                if(!in_array($Qval,$dataId)){
                    $noAanswer[$Qkey]=$Qval;
                }
            }
        }

        $idStr=implode(",",$dataId);//题目id数组字符串
        if ($idStr==""){
            $msg="没有选择题目哦";
            $re=array('info'=>3,'msg'=>$msg);//未作答试题
            $this->ajaxReturn($re);
            exit;
        }

        $noIdstr=implode(",",$noAanswer);//没有作答的id
        if($noIdstr){
            $Nosort=M("question_bank")->where("id in({$noIdstr})")->select();
        }
        foreach($Nosort as $Nkey=>$Nval){
            $needSort[]=$Nval['sort'];
        }
        $noSortstr=implode(",",$needSort);//没有作答的题目序号
        if($noIdstr){

            $msg="第".$noSortstr."道题没有作答，请答完试题";
            $re=array('info'=>2,'msg'=>$msg);//有未作答的试题
            $this->ajaxReturn($re);
            exit;

        }

        $data = M('question_bank')->where("id in({$idStr})")->select();

        $score="";
        foreach( $data AS $k => $v ){
            $data[$k]['keyss'] = unserialize($v['keyss']);//反序列多有备选答案
            $id=$data[$k]['id'];
            foreach($data[$k]['keyss'] as $key=>$value){//遍历备选答案

                foreach($answer[$id] as $Skey=>$Sval){
                    if($Sval==$value){//与传入的备选答案对比
                        //得到$key
                        $data[$k]['valss'] = unserialize($v['valss']);//反序列化得分情况

                        $arrScroe[$id][]=$data[$k]['valss'][$key];//相应试题id对应答案对应得分，二维
                        $score[$type]+=$data[$k]['valss'][$key];//得到一个评价类型的题目的总得分情况
                    }
                }

            }

        }

        $a=serialize($answer);  //传入所选答案序列化字符串
        $b=serialize($arrScroe);//答案相应得分序列化字符串


        //获取数据   qid试题id字符串

        $needData=array('uid'=>$uid,'pcid'=>$pid,'cid'=>$cid,
            'perid'=>$periodId,'qid'=>$idStr,
            'keyss'=>$a,'valss'=>$b,
            'type'=>$type,'sm_uid'=>$smUid);
        $result=M("estimate")->data($needData)->add();
        //加入综合评价表
        /*  if($type==1){//自我评价
              $ego=$score[$type];
          }else if($type==0){//家人评价、
              $patriarch=$score[$type];
          }else if($type==2){//同学评价、
              $schoolmate=$score[$type];
          }*/

        if($result>0){
            $Hasevalu=M("estimate")->where("uid=$this->uid and cid=$cid and pcid=$pid and perid=$periodId")->select();
            foreach ($Hasevalu as $Hkey=>$Hval){
                $Hnum[]=$Hasevalu[$Hkey]['type'];
            }

            if(count($Hnum)===3){
                $id=$periodId;//正在进行评价的主周期主键id

                $uid=$this->uid;
                $info=M("students_basicinfo")->where("uid=$uid")->find();
                $cid=$info['cid'];
                $classInfo=M("class")->where("id=$cid")->find();
                $pcid=$classInfo['pid'];
                $scoreAll0=M("estimate")->where("perid=$id and cid=$cid and pcid=$pcid and uid=$this->uid and type=0")->find();//当前期数、当前用户、当前类型


                //家人评价
                $T0score="";
                $scoreAll0['valss']=unserialize($scoreAll0['valss']);//返序列化
                foreach($scoreAll0['valss'] as $k=>$v){       //获取得分数组
                    foreach($v as $keyAll=>$valAll){
                        $T0score=bcadd($T0score,$valAll,2);    //类型为的0评价总分
                    }
                }
                //自我评价
                $scoreAll1=M("estimate")->where("perid=$id and cid=$cid and pcid=$pcid and uid=$this->uid and type=1")->find();//当前期数、当前用户、当前类型
                $T1score="";
                $scoreAll1['valss']=unserialize($scoreAll1['valss']);//返序列话化
                foreach($scoreAll1['valss'] as $k=>$v){       //获取得分数组
                    foreach($v as $keyAll=>$valAll){
                        $T1score=bcadd($T1score,$valAll,2);    //类型为的1评价总分
                    }
                }
                //同学评价
                $scoreAll2=M("estimate")->where("perid=$id and cid=$cid and pcid=$pcid and uid=$this->uid and type=2")->find();//当前期数、当前用户、当前类型
                $T2score="";
                $scoreAll2['valss']=unserialize($scoreAll2['valss']);//返序列话化
                foreach($scoreAll2['valss'] as $k=>$v){       //获取得分数组
                    foreach($v as $keyAll=>$valAll){
                        $T2score=bcadd($T2score,$valAll,2);    //类型为的2评价总分
                    }
                }
                $data=array('uid'=>$uid,'pcid'=>$pcid,'cid'=>$cid,
                    'perid'=>$id,'patriarch'=>$T0score,'ego'=>$T1score,'schoolmate'=>$T2score);
                $Allarr=M("synthesize_estimate")->data($data)->add();
                if($Allarr){
                    $msg="所有评价完毕,待老师评价后可查看总分";
                    $re=array('info'=>1,'msg'=>$msg);//所有试题评价完毕
                    $this->ajaxReturn($re);
                    exit;
                }

            }
            $msg="本页评价完毕,请继续评价其他页面";
            $needType=$type+2;//跳转页面类型
            $old=$type+1;//原来的页面
            if($needType>3){
                $needType=1;
            }
            $re=array('info'=>0,'msg'=>$msg,'new'=>$needType,'old'=>$old);//一页试题作答完毕
            $this->ajaxReturn($re);

        }


    }


    //综合得分
    public function overallRatings(){
        $period = M('period')->where('status=2')->find();//正在评价中的周期

        $classInfo=M("students_basicinfo")->where("uid=$this->uid")->find();
        $classId=$classInfo['cid'];
        $c=M("class")->where("id=$classId")->find();
        $pcid=$c['pid'];
        $id=$period['id'];//正在进行评价的主周期主键id
        if($_POST['cid'] && $_POST['pcid'] && $_POST['perid']){
            $classId=$_POST['cid'];
            $pcid=$_POST['pcid'];
            $id=$_POST['perid'];
        }
        if(!$period && !$_POST['perid']){
            $Noperiod="暂无正在评测的周期";
            $re=array('info'=>0,'msg'=>$Noperiod);
            return $this->ajaxReturn($re);
            exit;
        }
        $scoreAll=M("estimate")->where("perid=$id and cid=$classId and pcid=$pcid and uid=$this->uid")->select();//当前期数、当前用户
        if(!$scoreAll){
            $msg="未评价,务必测评完三种类型";
            $re=array('info'=>2,'msg'=>$msg);
            return $this->ajaxReturn($re);
            exit;
        }

        $needScore=M("synthesize_estimate")->where("cid=$classId and pcid=$pcid and uid=$this->uid and perid=$id")->find();
        $totalScore=$needScore['grade'];
        foreach($scoreAll as $key=>$val){
            $typeNum[]=$val['type'];
        }
        if(count($typeNum)!=3){
            $msg="请先将本期测评评价完,老师最终评价完后再查看得分情况";
            $re=array('info'=>3,'msg'=>$msg);
            return $this->ajaxReturn($re);
            exit;

        }



        $Scoreyear=M("synthesize_estimate")->where("cid=$classId and pcid=$pcid and uid=$this->uid")
            ->order("perid")
            ->sum("grade");//当前用户，当前用户所在班级，无期数限制

       // $this->assign("totalYearScore",$Scoreyear);//本学期得分

        $ScoreAll=M("synthesize_estimate")->where("uid=$this->uid")->sum("grade");//当前用户，无班级限制，无期数限制
        //display

        $re=array('info'=>1,'totalScore'=>$totalScore,'scoreAll'=>$ScoreAll);
        return $this->ajaxReturn($re);
    }


    //绑定姓名

    // 绑定姓名
    public function bindName(){
        // Ajax
        if( IS_AJAX === true ){
            $name = I('post.name','','strip_tags,htmlspecialchars');
            if( M('students_basicinfo')->where('uid='.$this->uid)->save(array('name'=>$name)) ){
                $this->ajaxReturn(1);
            }else{
                return false;
            }
        }
    }
}