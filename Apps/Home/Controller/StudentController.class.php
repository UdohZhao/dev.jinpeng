<?php
namespace Home\Controller;
use Think\Controller;
class StudentController extends BaseController {
    public $uid;
    // 构造方法
    public function _auto(){
      $this->uid = $_SESSION['userinfo']['uid'];
        if(!$_GET['userT']){//老师查看学生答题详情时判断
            if($_SESSION['userinfo']['type']==1){
                header("Location:".U('Teacher/index'));
                exit;
            }
        }
    }
    // 学生主页面
    public function index(){
      // 查询用户所在年级和班级

      $studentBaseinfo = M('students_basicinfo')->where('uid='.$this->uid)->find();
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
      $_SESSION['userinfo']['class'] = $studentGrade['cname'].$studentClass['cname'];
      $_SESSION['userinfo']['addup'] = $studentBaseinfo['addup'];
      // display
        //年级历史记录
        $pcInfo=M("synthesize_estimate")->where("s.uid=$this->uid")
                                        ->field("s.pcid,s.uid,s.cid,c.cname")
                                        ->join("class as c on s.pcid=c.id")
                                        ->alias("s")
                                        ->group("s.pcid")
                                        ->select();
        $this->assign("pcInfo",$pcInfo);
      $this->display();
    }
    //评价
    public function selfEvaluation(){
        // 获取正在进行中周期
        $period = M('period')->where('status=2')->find();

        $this->assign('period',$period);
        $perid=$period['id'];
        // 家长 自己 同学
        $type = isset($_GET['type']) ? intval($_GET['type']) : (int)0;
        $this->assign('type',$type);
        if($period==""){
            $this->assign('period',$period);
            $this->display();
            exit;
        }
        //判断是否作答过试题，查询评价表
        $classInfo=M("students_basicinfo")->where("uid=$this->uid")->find();
        $cid=$classInfo['cid'];
        $info=M("class")->where("class")->where("id=$cid")->find();
        $pcid=$info['pid'];
        //查询已作答试题
        $answerDetail=M("estimate")->where("uid=$this->uid and cid=$cid and pcid=$pcid and perid=$perid and type=$type")
                                    ->field("qid,keyss,valss,type")
                                    ->select();
        if($answerDetail){
            foreach($answerDetail as $key=>$val){
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
            }
            $this->assign('question',$question);
            $this->display();
            exit;
        }


      if( $period ){
        // type 获取显示条数
        if( $type == 1 ){
          $limit = (C('QUESTIONS_AMOUNT')+1) .','. C('QUESTIONS_AMOUNT');
        }elseif( $type == 2 ){
          $limit = (C('QUESTIONS_AMOUNT')+1)+C('QUESTIONS_AMOUNT') .','.C('QUESTIONS_AMOUNT');
        }else{
          $limit = "0,".(C('QUESTIONS_AMOUNT')+1);
        }
        // 查询正在进行中周期试题
        $data = M('question_bank')->where('perid='.$period['id'].' AND type='.$_SESSION['userinfo']['ctype'].' AND status=0')->order('sort ASC')->limit($limit)->select();
        foreach( $data AS $k => $v ){
          $data[$k]['keyss'] = unserialize($v['keyss']);
          $data[$k]['valss'] = unserialize($v['valss']);
        }
        $this->assign('data',$data);
      }
      // 查询当前年段试题
      //display
      $this->display();
    }


    //保存页面得分
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

        $typeNum=array(0,1,2);//所有评价类型

     $arr=M("estimate")->where("uid=$this->uid and cid=$cid and pcid=$pid and perid=$periodId")->select();//用户、正在侧评测的期数

        if($arr){

            foreach($arr as $Ekey=>$Evals){

                if (in_array($arr[$Ekey]['type'],$typeNum)){
                        $m=array_keys($typeNum,$arr[$Ekey]['type']);
                        if($m){
                            foreach($m as $mKey=>$mVal){

                            unset($typeNum[$mVal]);//剩下未评论的类型试题
                            }
                        }
                }
                $num[]=$arr[$Ekey]['type'];
            }


            $reNum=current($typeNum);//返回数组中当前第一个值
            $count=count($typeNum);
            if(in_array($type,$num) && $count){
                echo alert("本期你已经评价此页，请勿重复评价",__APP__."/Student/selfEvaluation/type/".$reNum,3);
                exit;
            }else if(!$count){
                echo alert("本期你已经评价完毕，请勿重复评价",__APP__."/Student/overallRatings/all/3",5);
                exit;
            }
        }
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
            echo alert("没有选择题目哦",__APP__."/Student/selfEvaluation/type/".$type,5);
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
            echo alert("第".$noSortstr."道题没有作答，请答完试题",__APP__."/Student/selfEvaluation/type/$type",5);
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

        $needType=$type+1;//跳转页面类型
        if($needType>2){
            $needType=0;
        }
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
                    echo alert("所有评价完毕,待老师评价后可查看总分",__APP__."/Student/overallRatings/all/3",6);
                    exit;
                }

            }

            echo alert("本页评价完毕",__APP__."/Student/selfEvaluation/type/".$needType,6);
        }
    }
    //综合得分
    public function overallRatings(){
        //跳转类型
        $webType=isset($_GET['all'])?$_GET['all']:"";

        $this->assign('webType',$webType);
        $period = M('period')->where('status=2')->find();//正在评价中的周期

        $classInfo=M("students_basicinfo")->where("uid=$this->uid")->find();
        $classId=$classInfo['cid'];
        $c=M("class")->where("id=$classId")->find();
        $pcid=$c['pid'];
        $id=$period['id'];//正在进行评价的主周期主键id
        if(!$period){
            $Noperiod="暂无正在评测的周期";
            $this->assign("Noperiod",$Noperiod);
            $this->display();
            exit;
        }
        $scoreAll=M("estimate")->where("perid=$id and cid=$classId and pcid=$pcid and uid=$this->uid")->select();//当前期数、当前用户
        if(!$scoreAll){
            echo alert("未评价,务必测评完三种类型",__APP__."/Student/selfEvaluation",3);
            exit;
        }

        $needScore=M("synthesize_estimate")->where("cid=$classId and pcid=$pcid and uid=$this->uid and perid=$id")->find();
        $totalScore=$needScore['grade'];
        foreach($scoreAll as $key=>$val){
            $typeNum[]=$val['type'];
        }
        if(count($typeNum)!=3){
            echo alert("请先将本期测评评价完,老师最终评价完后再查看得分情况",__APP__."/Student/selfEvaluation",5);
            exit;
        }
        $this->assign("totalScore",$totalScore);//本周期得分

        $Scoreyear=M("synthesize_estimate")->where("cid=$classId and pcid=$pcid and uid=$this->uid")
                                        ->order("perid")
                                        ->sum("grade");//当前用户，当前用户所在班级，无期数限制

        $this->assign("totalYearScore",$Scoreyear);//本学期得分

        $ScoreAll=M("synthesize_estimate")->where("uid=$this->uid")->sum("grade");//当前用户，无班级限制，无期数限制
      //display

        $this->assign("scoreAll",$ScoreAll);
        //display
      $this->display();
    }

    //历史得分年级
    public function firstGrade(){
      //display

      // $count=$estimate->count();
      // $page=new \Think\Page($count,4);
      // $page->setConfig('first','首页');
      // $page->setConfig('last','末页');
      // $page->setConfig('prev','上一页');
      // $page->setConfig('next','下一页');
      // $show=$page->show();
      // $info=$estimate->limit($page->firstRow,$page->listRows)->where()->select();
      $id=I("pcid");
        if(!$id){
            exit;
        }
      $uid=$this->uid;
        $classInfo=M("students_basicinfo")->where("uid=$uid")->find();
        $cid=$classInfo['cid'];
      $estimate=M("synthesize_estimate");


        $count      = M("synthesize_estimate")->where("uid=$uid  and cid=$cid and pcid=$id")
                                                ->count();  // 查询满足要求的总记录数
        $pageSize=5;
        $Page       = new selfPage($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->pages();// 分页显示输出

     $info=$estimate->where("pcid={$id} AND cid={$cid} AND uid={$uid}")
                    ->join("period ON synthesize_estimate.perid=period.id")
                    ->order("end_time desc")
                    ->limit($Page->getStart(),$pageSize)
                    ->select();
      $this->assign('info',$info);
        $this->assign('page',$show);
      // $time = time();
      $this->display();


    }

    //历史得分总得分
    public function totalScore(){
        $ScoreAll=M("synthesize_estimate")->where("uid=$this->uid")->sum("grade");//当前用户，无班级限制，无期数限制
      //display

        $this->assign("scoreAll",$ScoreAll);
      $this->display();
    }

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

    public function supplement(){

        $period = M('period')->where('status=2')->find();

        if(!$period){
           echo alert("没有正在测试的周期",__APP__."/Student/overallRatings/all/3",5);
            exit;
        }
        $perid=$period['id'];//正在测试的周期id

        $content=I("content");

        if(!$content){
            echo alert("不能为空",__APP__."/Student/supplementaryQuestion",5);
            exit;
        }
        $uid=$this->uid;//用户id
        $basicInfo=M("students_basicinfo")->where("uid=$uid")->find();//学生基本信息
        $cid=$basicInfo['cid'];//学生所在班级id
        $classInfo=M("class")->where("id=$cid")->find();//学生所在班级信息
        $pid=$classInfo['pid'];//班级父id
        $comfirm=M("replenish_estimate")->where("uid=$uid and perid=$perid")->find();
        if($comfirm){
            echo alert("已经提价补充,请勿重复提交",__APP__."/Student/overallRatings/all/3",5);
            exit;
        }
        $data=array('uid'=>$uid,'pcid'=>$pid,
                    'cid'=>$cid,'perid'=>$perid,
                    'content'=>$content,'ctime'=>time(),
                    'type'=>0,);
        $res=M("replenish_estimate")->data($data)->add();
        if($res>0){
            echo alert("补充已提交，请等待老师评价后查询最终分数",__APP__."/Student/overallRatings/all/3",6);
        }
    }

    public function supplementaryQuestion(){
        $period = M('period')->where('status=2')->find();
        if(!$period){
            echo alert("没有正在评测的周期",__APP__."/Student/overallRatings/all/3",5);
            exit;
        }
        $perid=$period['id'];
        $data=M("replenish_estimate")->where("uid=$this->uid and perid=$perid")
                                     ->field("content")
                                    ->find();
        if($data){
            $this->assign('data',$data);
        }
        $this->display();
    }
    public function Grade(){

        $perid=I("perid");
        if(!$_GET['uid']){
            $uid=$this->uid;
        }else{
            $uid=$_GET['uid'];
        }
        
        $Info=M("students_basicinfo")->where("uid=$uid")->find();
        $cid=$Info['cid'];
        $classInfo=M("class")->where("id=$cid")->find();
        $pcid=$classInfo['pid'];
        $needArr=M("synthesize_estimate")->where("s.cid=$cid and s.perid=$perid and s.uid=$uid")
                                        ->field("s.*,p.periods,b.name as studentname")
                                        ->join("students_basicinfo as b on s.uid=b.uid")
                                        ->join("period as p on p.id=s.perid")
                                        ->alias("s")
                                        ->find();

        $this->assign('needArr',$needArr);

        if(!$_GET['uid'] && $needArr['status']==0){
            echo alert("这个周期老师还未评价，看看其他周期吧",__APP__."/Student/firstGrade/pcid/$pcid",6);
            exit;
        }
        //该周期该学生答题详情  分自我 家人 同学类型
        $type = isset($_GET['type']) ? intval($_GET['type']) : (int)0;

        $answerDetail=M("estimate")->where("uid=$uid and perid=$perid and type=$type")
                                    ->field("qid,keyss,valss,type")
                                    ->select();
        /*var_dump($answerDetail);exit;*/

        foreach($answerDetail as $key=>$val){
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

        $count      = M("question_bank")->where("perid=$perid and id in({$idStr})")
            ->count();  // 查询满足要求的总记录数
        $pageSize=2;
        $Page       = new selfPage($count,$pageSize);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->pages();// 分页显示输出

        $question=M("question_bank")->where("perid=$perid and id in({$idStr})")
                                    ->limit($Page->getStart(),$pageSize)
                                    ->select();

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


        $this->assign('question',$question);

        $uerT=isset($_GET['userT'])?$_GET['userT']:2;//样式控制
        $this->assign('userT',$uerT);
        $this->assign('page',$show);
        $this->display();
    }

}