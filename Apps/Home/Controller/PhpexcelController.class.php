<?php
namespace Home\Controller;
use Think\Controller;
class PhpexcelController extends BaseController{
    public $uid;
    public function _initialize(){
        $this->uid=$_SESSION['userinfo']['uid']; //记录登陆教师用户的uid

    }


    public function index(){
        vendor('PHPExcel');//引入excel类
        $excel=new \PHPExcel();

        //Excel表格式,这里简略写了8列
        $letter = array('A','B','C','D','E','F','F','G');

        //表头数组

        $tableheader = array('学生姓名','自我评价','家长评价','同学评价','评价得分');
        for($i = 0;$i < count($tableheader);$i++) {

            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");

        }

        //表格数组


        //查询当前老师所在班级所有历史周期  综合评价表
        $teachersBaseinfo=M("teachers_basicinfo")->where("uid=$this->uid")->find();
        $cid=$teachersBaseinfo['cid'];//教师所在班级id
        $classInfo=M("class")->where("id=$cid")->find();//学生所在班级信息
        $pcid=$classInfo['pid']; //班级父id

        $perid = I('get.perid','','intval'); //获取要excel的周期数

        $estimate = M('synthesize_estimate'); // 实例化对象




        $history = $estimate->where("s.cid=$cid and s.pcid=$pcid and s.perid=$perid")
            ->field("b.name as studentname,s.ego,s.patriarch,s.schoolmate,s.grade")
            ->join("join students_basicinfo as b on s.uid=b.uid")
            ->alias("s")
            ->order("s.id")
            ->select();

        foreach($history as $key=>$val){
            $history[$key]['ego']="\t".$history[$key]['ego']."\t";
            $history[$key]['patriarch']="\t".$history[$key]['patriarch']."\t";
            $history[$key]['schoolmate']="\t".$history[$key]['schoolmate']."\t";
            $history[$key]['grade']="\t".$history[$key]['grade']."\t";
        }
        /*var_dump($estimate->getLastSql());exit;*/


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


        //总分数组
        $history[]=array(
        'EvalName'=>"评价总分",
        'egoAllScore'=>"\t".$egoTotal."\t",//自我评价总分
        'patriarchAllScore'=>"\t".$patriarchTotal."\t",//家长评价总分
        'schoolmateAllScore'=>"\t".$schoolmateTotal."\t",//同学评价总分
        'gradeAllScore'=>"\t".$evaluateTotal."\t",//评价总分
        );
        //平均分
        $history[]=array(
            'avergName'=>"平均得分",
            'average1'=>"\t".$averageScoreEgo."\t",//自我评价平均分
            'average0'=>"\t".$averageScorePatriarch."\t",//家人评价平均分
            'average2'=>"\t".$averageScoreSchoolmate."\t",//同学评价平均分
            'average3'=>"\t".$averageScoreEvaluate."\t",//评价平均分
        );


        $data=$history;
        /*$data = array(

            array('1','小王','男','20','100'),

            array('2','小李','男','20','101'),

            array('3','小张','女','20','102'),

            array('4','小赵','女','20','103'),

        );*/

//填充表格信息

        for ($i = 2;$i <= count($data) + 1;$i++) {

            $j = 0;

            foreach ($data[$i - 2] as $key=>$value) {

                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");

                $j++;

            }

        }

        //创建Excel输入对象

        $write = new \PHPExcel_Writer_Excel5($excel);

        header("Pragma: public");

        header("Expires: 0");

        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");

        header("Content-Type:application/force-download");

        header("Content-Type:application/vnd.ms-execl");

        header("Content-Type:application/octet-stream");

        header("Content-Type:application/download");;

        header('Content-Disposition:attachment;filename="金鹏实验小学评测.xls"');

        header("Content-Transfer-Encoding:binary");

        $write->save('php://output');


    }
}