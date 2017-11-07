<?php
namespace Admin\Controller;
use Think\Controller;
class PeriodController extends BaseController {
    public $id;
    public $db;
    // 构造方法
    public function _auto(){
      $this->id = intval($_GET['id']);
      $this->db = M('period');
    }
    // 周期配置界面
    public function index(){
      // GET
      if( IS_GET === true ){
        // id
        if( $this->id ){
          $data = $this->db->where('id='.$this->id)->find();
          $this->assign('data',$data);
        }
        // display
        $this->display();
        die;
      }
      // data
      $data = $this->getData();
      // id
      if( intval($_POST['id']) ){
        if( $this->db->where('id='.intval($_POST['id']))->save($data) ){
          echo alert('提交成功！',__APP__.'/Period/listss',6);
          die;
        }else{
          echo alert('提交失败！',$_SERVER["HTTP_REFERER"],5);
          die;
        }
      }
      if( $this->db->add($data) ){
        echo alert('提交成功！',__APP__.'/Period/listss',6);
        die;
      }else{
        echo alert('提交失败！',$_SERVER["HTTP_REFERER"],5);
        die;
      }
    }
    // 初始化数据
    private function getData(){
      $data = array();
      $data['periods'] = I('post.periods','','strip_tags,htmlspecialchars');
      $data['start_time'] = I('post.start_time','','strtotime');
      $data['end_time'] = I('post.end_time','','strtotime');
      $data['status'] = 0;
      return $data;
    }
    // 周期列表页面
    public function listss(){
      // search
      if( isset($_POST['search']) ){
        $sWhere = 'periods like "%'.$_POST['search'].'%"';
      }
      // 数据分页
      $count      = $this->db->count();// 查询满足要求的总记录数
      $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
      $show       = $Page->show();// 分页显示输出
      // data
      $data = $this->db->where($sWhere)->order('end_time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
      $this->assign('data',$data);
      $this->assign('page',$show);// 赋值分页输出
      // display
      $this->display();
    }
    // 检测是否重复添加
    public function checkPeriods(){
      // Ajax
      if( IS_AJAX === true ){
        $periods = I('post.periods');
        if( $this->db->where("periods='$periods'")->count() ){
          $this->ajaxReturn("<span style='color:red;'>该周期已经存在，请勿重复添加！</span>");
        }elseif( $this->db->where("status=0")->count() ){
          $this->ajaxReturn("<span style='color:red;'>检测到当前已有最新周期，请先过期掉！</span>");
        }elseif( $this->db->where("status=2")->count() ){
          $this->ajaxReturn("<span style='color:red;'>检测到当前已有周期正在进行中，请先过期掉！</span>");
        }else{
          $this->ajaxReturn('true');
        }
      }
    }
    // 过期
    public function freeze(){
      // GET
      if( IS_GET === true ){
        if( $this->db->where('id='.$this->id)->save(array('status'=>$_GET['status'])) ){
          echo alert('提交成功！',$_SERVER["HTTP_REFERER"],6);
          die;
        }else{
          echo alert('提交失败！',$_SERVER["HTTP_REFERER"],5);
          die;
        }
      }
    }
    // 删除
    public function del(){
      // GET
      if( IS_GET === true ){
        // 查询是否有儿子
        if( M('question_bank')->where('perid='.$this->id)->count() ){
          echo alert('请先删除该周期下所有试题！',$_SERVER["HTTP_REFERER"],5);
          die;
        }
        if( $this->db->where('id='.$this->id)->delete() ){
          echo alert('提交成功！',$_SERVER["HTTP_REFERER"],6);
          die;
        }else{
          echo alert('提交失败！',$_SERVER["HTTP_REFERER"],5);
          die;
        }
      }
    }
    // 动态评价条数
    public function editQa(){
      // GET
      if( IS_GET === true ){
        $qa = intval($_GET['qa']);
        if( $qa ){
          C('QUESTIONS_AMOUNT',$qa);
          echo alert('设定成功！',$_SERVER["HTTP_REFERER"],6);
          die;
        }else{
          echo alert('设定失败！',$_SERVER["HTTP_REFERER"],5);
          die;
        }
      }
    }


}