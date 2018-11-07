<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/4
 * Time: 17:06
 */

class CenterController extends GlobalController {

    public function index() {
        $userinfo = M('User')->where(array('user_id' => $_COOKIE[user_id]))->field('user_id,pen_name,mem_vip,vote,vipvote,alance,vip_time')->find();
        if (strtotime($userinfo[vip_time]) > strtotime(date('Y-m-d H:i:s', time()))) {
            $userinfo['baoyue']=1;
        }
        $this->assign('userinfo', $userinfo);


        $this->display();
        //print_r($_COOKIE);
        //echo $_COOKIE['channel'];
    }

}