<?php
namespace Admin\Controller;
use Think\Controller;
class ClassconfigController extends BaseController {
    public $id;
    public $db;
    // 构造方法
    public function _auto(){
      $this->id = intval($_GET['id']);
      $this->db = M('class');
    }
    // 班级配置首页
    public function index(){
      // GET
      if( IS_GET === true ){
        // pdata
        $pdata = $this->db->where('pid=0')->order('sort ASC')->select();
        $this->assign('pdata',$pdata);
        // id
        if( $this->id ){
          $sdata = $this->db->where('id='.$this->id)->find();
        }
        $this->assign('sdata',$sdata);
        // display
        $this->display();
        die;
      }
      // data
      $data = $this->getData();
      // id
      if( intval($_POST['id']) ){
        if( $this->db->where('id='.$_POST['id'])->save($data) ){
          echo alert('修改成功!',__APP__.'/Classconfig/cclist/id/'.$data['pid'],6);
          die;
        }else{
          echo alert('修改失败!',__APP__.'/Classconfig/index/id/'.$_POST['id'],5);
          die;
        }
      }
      // 防止重复添加
      $pid = $data['pid'];
      $cname = $data['cname'];
      if ( $this->db->where("pid='$pid' AND cname='$cname'")->count() ){
        echo alert('请勿重复添加!',__APP__.'/Classconfig/index',5);
        die;
      }else{
        if( $this->db->add($data) ){
          echo alert('提交成功!',__APP__.'/Classconfig/cclist/id/'.$data['pid'],6);
          die;
        }else{
          echo alert('提交失败!',__APP__.'/Classconfig/index',5);
          die;
        }
      }
    }
    // 初始化数据
    private function getData(){
        $data = array();
        $data['pid'] = I('post.cid','','intval');
        $data['cname'] = I('post.cname','','strip_tags,htmlspecialchars');
        $data['sort'] = I('post.sort','','intval');
        return $data;
    }
    // 班级列表
    public function cclist(){
      // pdata
      $pdata = $this->db->where('pid=0')->order('sort ASC')->select();
      $this->assign('pdata',$pdata);
      // id
      if( !$this->id ){
        $this->id = $pdata[0]['id'];
      }
      $this->assign('id',$this->id);
      // sdata
      $sdata = $this->db->where('pid='.$this->id)->order('sort ASC')->select();
      // foreach
      foreach( $sdata AS $k => $v ){
        // 教师
        $teacher = M('teachers_basicinfo')->where('cid='.$v['id'])->getField('name');
        if( $teacher ){
          $sdata[$k]['teacher'] = $teacher;
        }else{
          $sdata[$k]['teacher'] = '暂无';
        }
        // 学生人数
        $sdata[$k]['student'] = M('students_basicinfo')->where('cid='.$v['id'])->count();
        // 学生总评分
        $seaddupSQL = "
              SELECT
                      sum(addup) AS addups
              FROM
                      `students_basicinfo`
              WHERE
                      1 = 1
              AND
                      cid = ".$v['id']."
        ";
        $seaddupRE = M()->query($seaddupSQL);
        if( $seaddupRE[0]['addups'] ){
          $sdata[$k]['addup'] = $seaddupRE[0]['addups'];
        }else{
          $sdata[$k]['addup'] = '0.0';
        }
      }
      $this->assign('sdata',$sdata);
      // display
      $this->display();
    }
    // 删除班级
    public function del(){
      // GET
      if( IS_GET === true ){
        if( $this->db->where('id='.$this->id)->delete() ){
          echo alert('删除成功！',$_SERVER["HTTP_REFERER"],6);
          die;
        }else{
          echo alert('删除失败！',$_SERVER["HTTP_REFERER"],5);
          die;
        }
      }
    }
    // 毕业班级
    public function graduation(){
      // GET
      if( IS_GET === true ){
        // 毕业班级
        $data = $this->db->where('id='.$this->id)->find();
        $data['cid'] = $data['id'];
        $data['pcid'] = $data['pid'];
        $data['graduation'] = $_GET['g'];
        unset($data['id']);
        unset($data['pid']);
        // 加入毕业班级表
        if( M('comeandgo_class')->add($data) ){
          $this->db->where('id='.$this->id)->delete();
          echo alert('毕业成功！',$_SERVER['HTTP_REFERER'],6);
          die;
        }else{
          echo alert('提交失败！',$_SERVER['HTTP_REFERER'],5);
          die;
        }
      }
    }

}