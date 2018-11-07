<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;
//第三方合作登录
class HezuoaccountController extends Controller {

    public function login($openid) {
        //准备工作
        $User = M('User');
        $where[openid] = $openid;
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
            $data['mem_vip'] = $les[level];
            $data['login_time'] = date("Y-m-d H:i:s");
            $User->where($where)->save($data);

             header("Location:/Home/Tuisong/sendRequest");


            if ($_COOKIE[urls]) {
                header("Location:$_COOKIE[urls]");
            } else {
                header("Location: /");
            }
            exit();
        } else {
            return 2; //失败
        }
    }

    //注册账户
    public function registers($openid) {
        $user = M('User');
        //判断是否存在推广id
        if ($_COOKIE[prid]) {
            $prid = $user->where(array('user_id' => $_COOKIE[prid]))->sum('user_id');
            if ($prid > 0) {             //  如果数据库中 含有该用户
                $promoter_id = $_COOKIE[prid];
                $data['promoter_num'] = array('exp','promoter_num+1');
                $user->where(array('user_id'=>$promoter_id))->save($data);
              //  file_put_contents('1.txt', $user->getLastSql());
            } else {
                $promoter_id = 0;
            }
        } else {
            $promoter_id = 0;
        }
        //获取注册的推广ID
        $password = md5("xg123456" . C('ALL_ps'));
        $data['uid'] = 2;
        $data['promoter_id'] = $promoter_id;
        $data['user_name'] = "dz" . date('YmdHis', time()) . $this->getRandChar(3);
        $data['pen_name'] = "读者" . $this->getRandChar(6);
        $data['user_pass'] = $password;
        $data['openid'] = $openid; //绑定唯一ID
        $data['login_ip'] = get_client_ip();
        $data['login_time'] = date('y-m-d H:i:s', time());
        $data['sign_time'] = date('Y-m-d', strtotime($data['login_time'] . "-1 day"));
        $data['vip_time'] = $data['login_time'];
        $data['registration_time'] = $data['login_time'];
        //统计参数增加
        $tongji['day'] = array('exp', "day+1");
        $tongji['weeks'] = array('exp', "weeks+1");
        $tongji['month'] = array('exp', "month+1");
        $tongji['total'] = array('exp', "total+1");
        M('SystemTongji')->where(array('id' => 4))->save($tongji);
		if($_COOKIE['channel']){
			$data['channel_id'] = $_COOKIE['channel'];//获取渠道id
		}
        $uid = $user->add($data);
        if ($uid > 0) {
            $this->login($openid);
            exit();
        } else {
            echo "系统错误";
        }
    }

    //生成随机码
    function getRandChar($length) {
        $str = null;
        $strPol = "0123456789";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str.=$strPol[rand(0, $max)]; //rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        return $str;
    }

}
