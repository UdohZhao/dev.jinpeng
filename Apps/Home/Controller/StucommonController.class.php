<?php
namespace Home\Controller;
use Think\Controller;
class StucommonController extends Controller {
    // 构造方法
    public function _initialize(){

    }
    // 循环体以外
    public function evaluationheader(){
      // display
      $this->display();
    }
     public function paging(){
      // display
      $this->display();
    }
}