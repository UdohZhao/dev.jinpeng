<?php
namespace Admin\Controller;
use Think\Controller;
class LayoutsController extends BaseController {
    // 构造方法
    public function _auto(){

    }
    // header
    public function header(){
      // display
      $this->display();
    }
    // nav
    public function nav(){
      // display
      $this->display();
    }
    // footer
    public function footer(){
      // display
      $this->display();
    }
}