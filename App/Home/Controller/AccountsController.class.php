<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;
//第三方账户
class AccountsController extends Controller {

    //qq登录
    public function qq() {
        Vendor('Wapqq.qqConnectAPI');
        $qc = new \QC();
        $qc->qq_login();
    }

    //qq反馈
    public function qqcallback() {
        Vendor('Wapqq.qqConnectAPI');
        $qc = new \QC();
        $access_token = $qc->qq_callback(); //获取授权代码
        $openid = $qc->get_openid(); //获取唯一登录ID
        $qc = new \QC($access_token, $openid);
     //   $uuser = $qc->get_user_info();
        $is = A('Hezuoaccount')->login($openid);
        if ($is == 2) {
            A('Hezuoaccount')->registers($openid);
        }
    }

    //微博登录
    public function weibo() {
        vendor("Xinlang.wapconfig");//pc配置
        vendor("Xinlang.sina");
        $cons = new Configs();
        $sina = new sinaPHP($cons->sina_k, $cons->sina_s);
        $login_url = $sina->login_url($cons->callback_url);
        header("Location: $login_url");
    }

    //微博反馈
    public function weibocallback() {
        vendor("Xinlang.wapconfig");
        vendor("Xinlang.sina");
        $cons = new Configs();
        if (isset($_GET['code']) && $_GET['code'] != "") {
            $o = new sinaPHP($cons->sina_k, $cons->sina_s);
            $result = $o->access_token($cons->callback_url, $_GET['code']);
            if (isset($result['access_token']) && $result['access_token'] != "") {
                $o = new sinaPHP($cons->sina_k, $cons->sina_s, $result['access_token']);
                $neirong = $o->show_user_by_id($result['uid']);
                if ($neirong[screen_name]) {
                    $is = A('Hezuoaccount')->login($result['access_token']);
                    if ($is == 2) {
                        A('Hezuoaccount')->registers($result['access_token']);
                    }
                } else {
                    echo "微信登录审核中！！";
                }
            }
        } else {
            echo "系统错误";
        }
    }
        //微信登录
        public function weixin() {
            header('location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx68c0a8ca531f2780&redirect_uri=http://m.sjnovel.com/Home/Accounts/weixincallback.html&response_type=code&scope=snsapi_userinfo&state=123&connect_redirect=1#wechat_redirect');
        }
		//微信静默登录
		public function jingmo(){
			//配置参数的数组
			$CONF =  array(
				'__APPID__' =>'wx68c0a8ca531f2780',
				'__SERECT__' =>'62a57dfe79b2423fab051d452a9b822f',
				'__CALL_URL__' =>'http://m.sjnovel.com/Home/Accounts/weixincallback' //当前页地址
			);

			//没有传递code的情况下，先登录一下
			if(!isset($_GET['code']) || empty($_GET['code'])){

				$getCodeUrl  =  "https://open.weixin.qq.com/connect/oauth2/authorize".
								"?appid=" . $CONF['__APPID__'] .
								"&redirect_uri=" . $CONF['__CALL_URL__']  . 
								"&response_type=code".
								"&scope=snsapi_base". #!!!scope设置为snsapi_base !!!
								"&state=1";

				//跳转微信获取code值,去登陆   
				header('Location:' . $getCodeUrl);
				exit;
			}

			$code =	trim($_GET['code']);
			//使用code，拼凑链接获取用户openid 
			$getTokenUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$CONF['__APPID__']."&secret=".$CONF['__SERECT__']."&code=".$code."&grant_type=authorization_code";

			//拿到openid,下面就可以继续调起支付啦                 
			$openid =	$token_get_all->openid; 		
		}
		//微信登录授权返回
        public function weixincallback() {
            $code = $_GET['code'];
            $state = $_GET['state'];
            $appid = 'wx68c0a8ca531f2780';
            $appsecret = '62a57dfe79b2423fab051d452a9b822f';
            if (empty($code)) {
                $this->error('授权失败');
            }
            //获取access_token
            $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code';
            $token = json_decode(file_get_contents($token_url));
        
            if (isset($token->errcode)) {
                echo '<h1>错误：</h1>' . $token->errcode;
                echo '<br/><h2>错误信息：</h2>' . $token->errmsg;
                exit;
            }
            $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $appid . '&grant_type=refresh_token&refresh_token=' . $token->refresh_token;
            //认证令牌合法性
            $access_token = json_decode(file_get_contents($access_token_url));
            if (isset($access_token->errcode)) {
                echo '<h1>错误：</h1>' . $access_token->errcode;
                echo '<br/><h2>错误信息：</h2>' . $access_token->errmsg;
                exit;
            }
            $is = A('Hezuoaccount')->login($access_token->openid);
            if ($is == 2) {
                A('Hezuoaccount')->registers($access_token->openid);
            }


//            //获取用户信息
//            $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token->access_token . '&openid=' . $access_token->openid . '&lang=zh_CN';
//            //转成对象
//            $user_info = json_decode(file_get_contents($user_info_url));
//            if (isset($user_info->errcode)) {
//                echo '<h1>错误：</h1>' . $user_info->errcode;
//                echo '<br/><h2>错误信息：</h2>' . $user_info->errmsg;
//                exit;
//            }
//            //打印用户信息
//            echo '<pre>';
//            print_r($access_token);
//            echo '</pre>';
        }
}
