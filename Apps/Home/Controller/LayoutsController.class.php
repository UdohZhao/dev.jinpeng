<?php
namespace Home\Controller;
use Think\Controller;
class LayoutsController extends Controller {
    // 构造方法
    public function _initialize(){

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