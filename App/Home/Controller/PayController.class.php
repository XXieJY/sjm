<?php
namespace Home\Controller;
use Think\Controller;
class PayController extends GlobalController {
    public function index(){
		
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { 
			$this->assign( "wx", 1);
			$this->assign( "type", 10);
		}else{
			$this->assign( "type", 13);
		}	

        $this->display();
    }
	
    //充值记录
    public function record() {
        //import('Org.Util.Pagea');
        $sysch = M('SystemPay');
        $where['user_id'] = $this->to[user_id];
        //$where['state'] = 2;
        $count = $sysch->where($where)->count(); // 查询满足要求的总记录数   

		$Page = new \Think\Page($count,10);
        $Page->setConfig('theme', "<div class=\"pager\"><div class=\"pager-item\">%UP_PAGE%%DOWN_PAGE%</div>
                              <div class=\"pager-item\">到</div>
                              <div class=\"pager-item page-num-input\">
                                <input type=\"text\" id=\"pageText\"/>
                              </div>
                              <div class=\"pager-item\">页</div>
                              <div class=\"pager-item goto\" onClick=\"javascript:window.location.href='/Home/Pay/record/p/'+document.getElementById('pageText').value;\">跳转</div>
                              <div class=\"pager-item\">%NOW_PAGE%/%TOTAL_PAGE%</div>
                            </div>");
        //内容显示
        $jilu = $sysch->where($where)->field('type,money,trade,state,time')->limit($Page->firstRow . ',' . $Page->listRows)->order('time desc')->select();
        $this->assign('jilu', $jilu);
        // 分页显示输出
        $show = $Page->show();
        $this->assign('page', $show);
        $this->display();
    }	
    //充值订单
    public function pay() {

        
        if (is_numeric($_GET[money]) && $_GET[money] >= 30 && $_GET[money] < 10000) {
            
            //充值类型
            switch ($_GET[payChannelType]) {
				case 10:
                    //微信支付
					$trade = "WX".date('YmdHis', time()).rand(100000,999999); //订单号
                    $this->wxpay($_GET['money'],$trade); //调用微信公众号支付
                    break;
                case 12:
					$data = A('Payacc')->index($this->to['user_id'], $this->to['promoter_id'], 'AL'.$this->to[user_id]);
                    //支付宝充值
                    $this->xianzai($data['money'], $data['readmoney'], $data['trade'], $_GET[payChannelType]); //调用现在支付充值   
                    break;
                case 13:
					$data = A('Payacc')->index($this->to['user_id'], $this->to['promoter_id'], 'XZ'.$this->to[user_id]);
                    //微信
                    $this->xianzai($data['money'], $data['readmoney'], $data['trade'], $_GET[payChannelType]); //调用现在支付充值   
                    break;
                case 11:
                    //网银(现在支付通道)
                    $this->xianzai($data['money'], $data['readmoney'], $data['trade'], $_GET[payChannelType]); //调用现在支付充值   
                    break;
                case 14:
                    //扫码微信
                    $this->saoma($data['money'], $data['readmoney'], $data['trade']); //调用现在支付充值   
                    break; 
				case 15:
                    $trade = "BY".date('YmdHis', time()).rand(100000,999999); //订单号
                    $this->wxbypay($_GET['money'],$trade,15); //调用微信公众号支付
                    break; 
				case 16:
                    $trade = "BY".date('YmdHis', time()).rand(100000,999999); //订单号
                    $this->wxbypay($_GET['money'],$trade,16); //调用微信公众号支付
                    break;
				case 17:
                    $trade = "BY".date('YmdHis', time()).rand(100000,999999); //订单号
                    $this->wxbypay($_GET['money'],$trade,17); //调用微信公众号支付
                    break;	
            }
        } else {
			//dump($_GET);
            $this->success("充值必须大于30元");
        }
    }
	
	//微信公众号支付接口
	public function wxpay($money,$trade){
		$data['user_id'] = $this->to['user_id'];
		$data['type'] = "微信公众号支付";
        $data['readmoney'] = ($money * 100) + $this->jiacheng($money * 100) + $this->duosong($money); //实际到账阅读币
		$data['trade'] = $trade;
        $data['money'] = $money;
        $data['state'] = 1;
        $data['time'] = date('Y-m-d H:i:s', time());
        $data['ctime'] = date('Y-m-d', time());
		$User = M('User');
		$where['user_id'] = $this->to['user_id'];
		$isUser = $User->field("user_id,channel_id")->where($where)->find();
		if(!empty($_COOKIE['channel'])){
			$data['channel_id'] = $_COOKIE['channel'];
		}else{
			$data['channel_id'] = $isUser['channel_id'];
		}
        if(!empty($_COOKIE['prid'])){
            $data['prid'] = $_COOKIE['prid'];
        }else{
            $data['prid'] = '';
        }
        $isok = M('SystemPay')->add($data);
		 
		$price = $money*100;
		$body = "水晶书城微信支付";
		$total_fee = $price;
		$wxpay = new WeiXinPayController();
		$res = $wxpay->buildRequestForm($trade,$total_fee,$body);
		echo $res;		
		
	}	
	
	//微信公众号支付包月充值
	public function wxbypay($money,$trade,$type){
		
			//$money = intval($_POST['money']);
			//$type = $_POST['payChannelType'];
			//$trade = "BY".date('YmdHis', time()).rand(100000,999999);
			
			$data['user_id'] = $this->to['user_id'];
			$data['type'] = "微信公众号支付";
			$data['readmoney'] = 0;
			$data['trade'] = $trade;
			$data['money'] = $money;
			$data['state'] = 1;
			$data['time'] = date('Y-m-d H:i:s', time());
            $data['ctime'] = date('Y-m-d', time());
			if($type == 15){
				$data['days'] = 30;
			}elseif($type == 16){
				$data['days'] = 100;
			}elseif($type == 17){
				$data['days'] = 365;
			}
			$User = M('User');
			$where['user_id'] = $this->to['user_id'];
			$isUser = $User->field("user_id,channel_id")->where($where)->find();
			if(!empty($_COOKIE['channel'])){
				$data['channel_id'] = $_COOKIE['channel'];
			}else{
				$data['channel_id'] = $isUser['channel_id'];
			}
            if(!empty($_COOKIE['prid'])){
            $data['prid'] = $_COOKIE['prid'];
        }else{
            $data['prid'] = '';
        }
			$Spay = M('SystemPay');
			$isok = $Spay->add($data);
			$trade_info = $Spay->where('id='.$isok)->field('trade')->find();
			//dump($trade_info);exit;
			if($isok){
				$price = $money*100;
				$body = "水晶书城微信支付";
				$total_fee = $price;
				$wxbypay = new WeiXinPayController();
				$res = $wxbypay->buildRequestForm($trade,$total_fee,$body);
				echo $res;		
				
			}else{
				echo "创建订单失败";
			}
			
	}		
	
	
    //加成计算
    private function jiacheng($ymb) {
        if ($this->to[mem_vip] != 0) {
            switch ($this->to[mem_vip]) {
                case 1:
                    $ticheng = $ymb * 0.01;
                    break;
                case 2:
                    $ticheng = $ymb * 0.02;
                    break;
                case 3:
                    $ticheng = $ymb * 0.03;
                    break;
                case 4:
                    $ticheng = $ymb * 0.04;
                    break;
                case 5:
                    $ticheng = $ymb * 0.05;
                    break;
            }
            return $ticheng;
        } else {
            return 0;
        }
    }
    //多送阅读币            
    private function duosong($money) {
        //赠送礼物
        if ($money >= 100) {
            return 600;
        } elseif ($money >= 50) {
            return 400;
        } elseif ($money >= 30) {
            return 200;
        }elseif ($money >= 200) {
            return 800;
        }elseif ($money >= 500) {
            return 1000;
        }elseif ($money >= 1000) {
            return 1200;
        } else {
            return 0;
        }
    }	
	//微信公众号支付返回
	public function wxnotify(){
		//echo 111;
		$xmlObj = array();
		$log_name= $_SERVER['DOCUMENT_ROOT']."/App/Runtime/notify_url.log";//log文件路径
		$ress=file_get_contents('php://input');
		file_put_contents('aaa.log',file_get_contents('php://input')."\n",FILE_APPEND);

         //这里没有去做回调的判断，可以参考手机做一个判断。
        //$xmlObj=simplexml_load_string($GLOBALS['HTTP_RAW_GET_DATA']); //解析回调数据
		$xmlObj=simplexml_load_string($ress, 'SimpleXMLElement', LIBXML_NOCDATA); 
        $appid=$xmlObj->appid;//微信appid
        $mch_id=$xmlObj->mch_id;  //商户号
        $nonce_str=$xmlObj->nonce_str;//随机字符串
        $sign=$xmlObj->sign;//签名
        $result_code=$xmlObj->result_code;//业务结果
        $openid=$xmlObj->openid;//用户标识
        $is_subscribe=$xmlObj->is_subscribe;//是否关注公众帐号
        $trace_type=$xmlObj->trade_type;//交易类型，JSAPI,NATIVE,APP
        $bank_type=$xmlObj->bank_type;//付款银行，银行类型采用字符串类型的银行标识。
        $total_fee=$xmlObj->total_fee;//订单总金额，单位为分
        $fee_type=$xmlObj->fee_type;//货币类型，符合ISO4217的标准三位字母代码，默认为人民币：CNY。
        $transaction_id=$xmlObj->transaction_id;//微信支付订单号
        $out_trade_no=$xmlObj->out_trade_no;//商户订单号
        $attach=$xmlObj->attach;//商家数据包，原样返回
        $time_end=$xmlObj->time_end;//支付完成时间
        $cash_fee=$xmlObj->cash_fee;
        $return_code=$xmlObj->return_code;
        //下面开始你可以把回调的数据存入数据库，或者和你的支付前生成的订单进行对应了。
        //需要记住一点，就是最后在输出一个success.要不然微信会一直发送回调包的，只有需出了succcess微信才确认已接收到信息不会再发包.
		
		file_put_contents($log_name,$xmlObj."\n",FILE_APPEND);
		//$datas = A('Payacc')->chuli($out_trade_no, $transaction_id);
		$systempay = M('SystemPay');
        $issc = $systempay->where(array('trade' => $out_trade_no, 'state' => 1))->find();
		$datas['transaction'] = $transaction_id;
		$datas['state'] = 2;
		$dd_up = $systempay->where('trade="'.$out_trade_no.'"')->save($datas); 
		
		if($dd_up){
			$data['return_code'] = $return_code;
			$wxpays = new WeiXinPayController();
			$res = $wxpays->verifyNotify($data);
		}

	}		
    //调用现在支付接口
    public function xianzai($money, $readmoney, $trade, $payChannelType) {
        Vendor('Xianzai.conf.Config'); //配置方案
        Vendor('Xianzai.services.Core'); //服务
        Vendor('Xianzai.services.Net'); //服务
        Vendor('Xianzai.services.Services'); //服务
        $req = array();
        $req["mhtOrderName"] = "充值 $money 元";
        $req["mhtOrderAmt"] = $money * 100;
        $req["mhtOrderDetail"] = "充值 $money 元=$readmoney 阅读币";
        $req["funcode"] = \Config::TRADE_FUNCODE;
        $req["appId"] = \Config::$appId; //应用ID
        $req["mhtOrderNo"] = $trade;
        $req["mhtOrderType"] = \Config::TRADE_TYPE;
        $req["mhtCurrencyType"] = \Config::TRADE_CURRENCYTYPE;
        $req["mhtOrderTimeOut"] = \Config::$trade_time_out;
        $req["mhtOrderStartTime"] = date("YmdHis");
        $req["notifyUrl"] = \Config::$back_notify_url;
        $req["frontNotifyUrl"] = \Config::$front_notify_url;
        $req["mhtCharset"] = \Config::TRADE_CHARSET;
        $req["deviceType"] = \Config::TRADE_DEVICE_TYPE;
        $req["payChannelType"] = $payChannelType;
        $req["mhtReserved"] = "test";
        $req["mhtSignature"] = \Services::buildSignature($req);
        $req["mhtSignType"] = \Config::TRADE_SIGN_TYPE;
        $req_str = \Services::trade($req);
        header("Location:" . \Config::TRADE_URL . "?" . $req_str);
    }		
}