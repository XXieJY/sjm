<?php
namespace Home\Controller;
use Think\Controller;

class LoginController extends Controller{


    public function index(){

        $this->display();
    }
    public function login($user_name="",$user_pass="")
    {

        $user_name = $_POST['username'];
        $user_pass = $_POST['password'];

        if (empty($user_name) || empty($user_pass)) {
           $this->error("登录失败",U("/Home/Login/index"),3);
            exit();
        }
        $User = M('User');
        $where['user_name'] = $user_name;
        $where['user_pass'] = md5($user_pass . C('ALL_ps'));
        $isUser = $User->where($where)->find();



        if ($isUser) {
            cookie('uid', $isUser[uid], time() + 2 * 7 * 24 * 3600);
            cookie('user_id', $isUser[user_id], time() + 2 * 7 * 24 * 3600);
            cookie('user_name', $isUser[user_name], time() + 2 * 7 * 24 * 3600);
            cookie('pen_name', $isUser[pen_name], time() + 2 * 7 * 24 * 3600);
            cookie('portrait', $isUser[portrait], time() + 2 * 7 * 24 * 3600);
            cookie('shell', md5($isUser[user_name] . $isUser[user_pass] . C('ALL_ps')), time() + 2 * 7 * 24 * 3600);
            //记录登录时间
            //更新等级
            $level['type'] = 1;
            $level['score'] = array('ELT', $isUser[integral]);
            $les = M('UserLevel')->where($level)->field('level')->order('id DESC')->find();

            //更新等级
            $data['mem_vip'] = $les['level'];
            $data['login_time'] = date("Y-m-d H:i:s");
           $User->where($where)->save($data);//此处需要一个men_vip
          //$this->success("登录成功",U("/Home/Index/index"),3);
            if ($_COOKIE[urls]) {
                header("Location:$_COOKIE[urls]");
            } else {
				header("Location:/");
            }


        } else {
            $this->error("登录失败",U("Login/index"));
        }
    }
   //退出登录
    public function logout(){

        cookie('uid', null);
        cookie('user_id', null);
        cookie('user_name', null);
        cookie('pen_name', null);
        cookie('portrait', null);
        cookie('shell', null);
        if ($_COOKIE[urls]) {
            header("Location:$_COOKIE[urls]");
        } else {
            header("Location:/");
        }


    }



    }

