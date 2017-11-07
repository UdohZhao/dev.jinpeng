<?php
namespace Admin\Controller;
use Think\Controller;
class SiteController extends BaseController{
    public function _auto(){

    }
    //站点配置
    public function add(){
        $this->display();
    }

    public function save(){
        $_POST['status']=0;

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录
        // 上传文件
        $upload   =   $upload->upload();
        $logo='/Admin/Uploads/'.$upload['logo']['savepath'].$upload['logo']['savename'];
        $_POST['logo']=$logo;
        $re=M("site")->data($_POST)->add();
        if($re>0){
            echo alert("添加成功",__APP__."/Site/add",6);
        }else{
            echo alert("添加失败",__APP__."/Site/add",5);
        }
    }
  public function oper(){

      $info=M("site")->order("id desc")->find();
      $this->assign('info',$info);
      $this->display();
  }

  public function usave(){
      $upload = new \Think\Upload();// 实例化上传类
      $upload->maxSize   =     3145728 ;// 设置附件上传大小
      $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
      $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
      $upload->savePath  =     ''; // 设置附件上传（子）目录
      // 上传文件
      $upload   =   $upload->upload();
      if($upload){
          $logo='/Admin/Uploads/'.$upload['logo']['savepath'].$upload['logo']['savename'];
          $_POST['logo']=$logo;
      }
      $re=M("site")->data($_POST)->save();

      if($re>=0){
          echo alert("修改成功",__APP__."/Site/oper",6);
      }
  }
}