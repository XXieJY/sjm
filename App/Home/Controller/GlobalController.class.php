<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;
class GlobalController extends Controller {

    protected $to = 0;

    public function _initialize() {
		
        $this->usel_shell($_COOKIE['user_id'], $_COOKIE['shell']);
        $this->assign("to", $this->to);
    }

    //权限验证页
    private function usel_shell($user_id, $shell) {
        $User = M('User');
        $where['user_id'] = $user_id;
        $isUser = $User->field('`user_id`,`uid`,`promoter_id`,`user_name`,`pen_name`,`user_pass`,`mem_vip`,`alance`')->where($where)->find();
        if ($isUser) {
            $shell2 = md5($isUser['user_name'] . $isUser['user_pass'] . C('ALL_ps'));
            if ($shell == $shell2) {
                $this->to = $isUser;
            } else {
				if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { 
					redirect('/Home/Accounts/jingmo');
				}else{
					header("Location: /Home/Login/index.html");
					exit();
				} 
            }
        } else {
				if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { 
					redirect('/Home/Accounts/jingmo');
				}else{
					header("Location: /Home/Login/index.html");
					exit();
				} 
        }
    }

}
