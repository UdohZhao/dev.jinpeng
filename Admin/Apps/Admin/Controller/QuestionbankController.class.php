<?php
namespace Admin\Controller;
use Think\Controller;
class QuestionbankController extends BaseController {
    public $id;
    public $db;
    // 构造方法
    public function _auto(){
      $this->id = intval($_GET['id']);
      $this->db = M('question_bank');
    }
    // 配置题库界面
    public function index(){
      // GET
      if( IS_GET === true ){
        // 最新周期
        $data = M('period')->where('status=0')->find();

        $this->assign('data',$data);

          //查询备选答案表的答案和对应分数
          $hasAnswer=M('answer')->select();
          foreach ($hasAnswer as $key=>$val){
              $hasAnswer[$key]['answer_str'] = implode(',', unserialize($val['answer_str']));
              $hasAnswer[$key]['val_str'] = implode(',', unserialize($val['val_str']));
          }

          $this->assign('hasAnswer',$hasAnswer);
        // id
        if( $this->id ){
          $dataQb = $this->db->where('id='.$this->id)->find();
          $dataQb['keyss'] = $dataQb['keyss'];
          $dataQb['valss'] = implode(',', unserialize($dataQb['valss']));
            $needInfo=M('answer')->where('answer_str='."'{$dataQb['keyss']}'")->field('id')->find();

            $this->assign('selId',$needInfo['id']);

          $this->assign('dataQb',$dataQb);
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
          echo alert('提交成功！',__APP__.'/Questionbank/listss',6);
          die;
        }else{
          echo alert('提交失败',$_SERVER['HTTP_REFERER'],5);
          die;
        }
      }
      if( $this->db->add($data) ){
        echo alert('提交成功！',__APP__.'/Questionbank/listss',6);
        die;
      }else{
        echo alert('提交失败',$_SERVER['HTTP_REFERER'],5);
        die;
      }
    }
    // 初始化数据
    private function getData(){
        $answerId=I('post.answerId','','intval');
        //查询得出该id的答案和对应分数
        $answerInfo=M('answer')->where('id='.$answerId)->field('id,answer_str,val_str')->find();
      $data = array();
      $data['perid'] = I('post.perid','','intval');
      $data['test_questions'] = $_POST['test_questions'];
      $data['keyss'] = $answerInfo['answer_str'];
      $data['valss'] = $answerInfo['val_str'];
      $data['nature'] = I('post.nature','','intval');
      $data['sort'] = I('post.sort','','intval');
      $data['type'] = I('post.type','','intval');
      $data['status'] = I('post.status','','intval');
      $data['ctime'] = time();
      return $data;
    }
    // 试题列表
    public function listss(){
      // id
      if( $this->id ){
        $pWhere = 'id='.$this->id;
      }else{
        $pWhere = 'status=0';
      }
      // 最新周期
      $period = M('period')->where($pWhere)->find();
      $this->assign('period',$period);
      if( $period ){
        // type
        if( isset($_GET['type']) ){
          $type = intval($_GET['type']);
        }else{
          $type = 0;
        }
        $this->assign('type',$type);
        // search
        if( isset($_POST['search']) ){
          $sWhere = " AND test_questions like '%".$_POST['search']."%'";
        }
        // 数据分页
        $count      = $this->db->where('perid='.$period['id'].' AND type='.$type)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        // 最新周期试题
        $data = $this->db->where('perid='.$period['id'].' AND type='.$type.$sWhere)->order('sort ASC')->limit($Page->firstRow.','.$Page->listRows)->select();
        // foreach
        foreach( $data AS $k => $v ){
          $data[$k]['keyss'] = unserialize($v['keyss']);
          $data[$k]['valss'] = unserialize($v['valss']);
        }
        $this->assign('data',$data);
        $this->assign('page',$show);// 赋值分页输出
      }
      // display
      $this->display();
    }
    // 删除
    public function del(){
      // GET
      if( IS_GET === true ){
        if( $this->db->where('id='.$this->id)->delete() ){
          echo alert('提交成功！',$_SERVER['HTTP_REFERER'],6);
          die;
        }else{
          echo alert('提交失败',$_SERVER['HTTP_REFERER'],5);
          die;
        }
      }
    }


    //保存备选答案
    public function answerSave(){
        $data=array();
        $data['answer_str'] = serialize(explode(',',I('post.answer_str','','strip_tags,htmlspecialchars')));
        $data['val_str'] = serialize(explode(',',I('post.val_str','','strip_tags,htmlspecialchars')));
        $re=M('answer')->data($data)->add();
        if($re){
            echo alert('添加成功',__APP__.'/Questionbank/anwser',6);
        }
    }

    public function answerlist(){

        $count      = M('answer')->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        // 最新周期试题
        $arr=M('answer')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($arr as $key=>$val){
            $arr[$key]['answer_str'] = implode(',', unserialize($val['answer_str']));
            $arr[$key]['val_str'] = implode(',', unserialize($val['val_str']));

        }
        $this->assign('arr',$arr);
        $this->assign('page',$show);
        $this->display();
    }

    public function delAnswer(){
        $id=I('post.id');
        $re=M('answer')->where('id='.$id)->delete();
        if($re){
            $this->ajaxReturn(true);
        }
    }
}