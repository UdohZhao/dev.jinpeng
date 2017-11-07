<?php
namespace Wap\Controller;
use Think\Controller;
class SallgradesController extends BaseController {
    // 构造方法
    public $uid;
    public function _auto(){
        $this->uid = $_SESSION['userinfo']['uid'];
    }
    //总得分页面
    public function index(){
        $ScoreAll=M("synthesize_estimate")->where("uid=$this->uid")->sum("grade");//当前用户，无班级限制，无期数限制
        $this->assign("ScoreAll",$ScoreAll);
    	$this->display();
    }
}