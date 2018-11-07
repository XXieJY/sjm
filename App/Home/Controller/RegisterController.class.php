<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/29
 * Time: 16:51
 */
namespace Home\Controller;
use Think\Controller;
use Think\Think;
class RegisterController extends Controller{

    public function index(){

        $this->display();
    }

    //用户注册
    public function register() {

        $user_name =$_POST['username'];
        $pen_name =$_POST['pen_name'];
        $email="";
        $password=$_POST['password'];

        $user = M('User');
        //验证账户
        if (!empty($user_name)) {   // 如果有信息传送过来  即调用该脚本
            $count = $user->where(array('user_name' => $user_name))->sum('user_id'); // 获取数据条数
            if ($count > 0) {             //  如果数据库中 含有该用户
                $this->error("账号已存在请重新注册",U("/Home/Register/index"),3);
            }
        }
        //判断是否存在推广id
        if ($_COOKIE[prid]) {
            $prid = $user->where(array('user_id' => $_COOKIE[prid]))->sum('user_id');
            if ($prid > 0) {             //  如果数据库中 含有该用户
                $promoter_id = $_COOKIE[prid];
                //含有该用户后，编辑推广用户增加一个
                $data['promoter_num'] = array('exp', 'promoter_num+1');
                $user->where(array('user_id' => $promoter_id))->save($data);
                //   file_put_contents('1.txt', $user->getLastSql());
            } else {
                $promoter_id = 0;
            }
        } else {
            $promoter_id = 0;
        }
        //获取注册的推广ID
        $password = md5($password . C('ALL_ps'));
        $data['uid'] = 2;
        $data['promoter_id'] = $promoter_id;
        $data['user_name'] = $user_name;
        $data['pen_name'] = $pen_name;
        $data['user_pass'] = $password;
        $data['email'] = $email;
        $data['login_ip'] = get_client_ip();
        $data['login_time'] = date('y-m-d H:i:s', time());
        $data['sign_time'] = date('Y-m-d', strtotime($data['login_time'] . "-1 day"));
        $data['vip_time'] = $data['login_time'];
        $data['registration_time'] = $data['login_time'];
        if(!empty($_COOKIE['channel'])){
            $data['channel_id'] = $_COOKIE['channel'];//获取渠道id
        }
        $uid = $user->add($data);





       if($uid){

          A("Login")->login($user_name,$password);
           header("Location:$_COOKIE[urls]");

       }


        //统计参数增加
        $tongji['day'] = array('exp', "day+1");
        $tongji['weeks'] = array('exp', "weeks+1");
        $tongji['month'] = array('exp', "month+1");
        $tongji['total'] = array('exp', "total+1");
        M('SystemTongji')->where(array('id' => 4))->save($tongji);
        if ($uid > 0) {
            return 1; //OK
        } else {
            return "系统错误"; //NO
        }
    }

}