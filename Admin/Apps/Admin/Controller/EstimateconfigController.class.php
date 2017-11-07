<?php
namespace Admin\Controller;
use Think\Controller;
class EstimateconfigController extends BaseController {
    public $id;
    public $db;
    // 构造方法
    public function _auto(){
      $this->db = M('estimate_config');
    }
    // 评价配置页面
    public function index(){
      // GET
      if( IS_GET === true ){
        $synthesis = $this->db->where('type=0')->find();
        $replenish = $this->db->where('type=1')->find();
        $this->assign('synthesis',$synthesis);
        $this->assign('replenish',$replenish);
        // display
        $this->display();
        die;
      }
      // data
      $data = $this->getData();
      // id
      if( intval($_POST['id']) ){
        if( $this->db->where('id='.intval($_POST['id']))->save($data) ){
          echo alert('提交成功！',$_SERVER['HTTP_REFERER'],6);
          die;
        }else{
          echo alert('提交失败！',$_SERVER['HTTP_REFERER'],5);
          die;
        }
      }
      if( $this->db->add($data) ){
        echo alert('提交成功！',$_SERVER['HTTP_REFERER'],6);
        die;
      }else{
        echo alert('提交失败！',$_SERVER['HTTP_REFERER'],5);
        die;
      }
    }
    // 初始化数据
    private function getData(){
      $data = array();
      $data['y'] = I('post.y','','intval');
      $data['l'] = I('post.l','','intval');
      $data['z'] = I('post.z','','intval');
      $data['c'] = I('post.c','','intval');
      $data['type'] = I('post.type','','intval');
      return $data;
    }
}