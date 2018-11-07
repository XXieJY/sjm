<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/30
 * Time: 20:07
 */
namespace Home\Controller;
use Think\Controller;
use Think\Think;
class LoadController extends Controller{

    public function _initialize() {
        $_SERVER['HTTP_HOST']=='www.xiangmucangku.com'?header("Location:http://m.sjnovel.com/"):'';
        //路径
        cookie('urls', $_SERVER['REQUEST_URI'], time() + 2 * 7 * 24 * 3600);
        //推广ID
        if ($_GET[prid]) {
            cookie('prid', $_GET[prid], time() + 2 * 7 * 24 * 3600);
        }
    }
}