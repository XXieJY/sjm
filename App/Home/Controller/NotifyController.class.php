<?php
namespace Home\Controller;
use Think\Controller;
//支付反馈接收接口
class NotifyController extends Controller {

    public function index() {
        Vendor('Xianzai.conf.Config'); //配置方案
        Vendor('Xianzai.services.Core'); //服务
        Vendor('Xianzai.services.Net'); //服务
        Vendor('Xianzai.services.Services'); //服务
        $request = file_get_contents('php://input');
        parse_str($request, $request_form);
        if ( \Services::verifySignature($request_form) ) {
            $tradeStatus = $request_form['tradeStatus'];
            if ($tradeStatus != "" && $tradeStatus == "A001") {
                A('Payacc')->chuli($request_form['mhtOrderNo'], $request_form['mhtOrderName']);
                echo "success=Y";
            }
            //支付失败
        }
        //验证签名失败
    }
    public function saoma() {
        Vendor('Newxianzai.conf.Config'); //配置方案
        Vendor('Newxianzai.services.Core'); //服务
        Vendor('Newxianzai.services.Net'); //服务
        Vendor('Newxianzai.services.Services'); //服务
        $request = file_get_contents('php://input');
        parse_str($request, $request_form);
        if (Services::verifySignature($request_form)) {
            $tradeStatus = $request_form['tradeStatus'];
            if ($tradeStatus != "" && $tradeStatus == "A001") {
                A('Payacc')->chuli($request_form['mhtOrderNo'], $request_form['mhtOrderName']);
                echo "success=Y";
            }
            //支付失败
        }
        //验证签名失败
    }    
	
	//微信公众号支付返回
	public function wxnotify(){
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
        $issc = $systempay->where('trade="'.$out_trade_no.'"')->find();

		$user = M('User');
		$zhanghu = $user->where(array('user_id' => $issc['user_id']))->field('integral,money,alance,channel_id')->find(); //账户充值额
		if ($issc) {
			$datas['transaction'] = $transaction_id;
			$datas['state'] = 2;
			//$datas['channel_id'] = $zhanghu['channel_id'];
			$dd_up = $systempay->where('trade="'.$out_trade_no.'"')->save($datas);			
			if($dd_up){
				$days = intval($issc['days']);
				/* if($days=30){
					$data['days'] = array('exp', "days+$days");
					//$data['deadline'] = date('Y-m-d H:i:s',strtotime('+'.$data["days"].' day'));
				}elseif($days=100){
					$data['days'] = array('exp', "days+$days");
					//$data['deadline'] = date('Y-m-d H:i:s',strtotime('+'.$data["days"].' day'));
				} */
				
				$data['days'] = array('exp', "days+$days");
				$data['money']=$zhanghu[money]+$issc[money];
				$data['integral']=($issc[money] * 10)+$zhanghu[integral];
				$data['alance']=$zhanghu[alance]+$issc[readmoney];
				$oks = $user->where(array('user_id' => $issc[user_id]))->save($data);
				//更新等级
				if ($oks) {    
					//更新等级
					$level['type'] = 1;
					$integral=$issc[money] * 10;
					$level['score'] = array('ELT', $zhanghu[integral] + $integral);
					$les = M('UserLevel')->where($level)->field('level')->order('id DESC')->find();
					$datas['mem_vip'] = $les[level];
					$user->where(array('user_id' => $issc[user_id]))->save($datas);
				}
				//更新编辑
				if ($issc[promoter_id]) {
					$network[alance] = array('exp', "alance+$issc[commission]");
					$network[money_num] = array('exp', "money_num+1");
					$network[money] = array('exp', "money+$issc[money]");
					$user->where(array('user_id' => $issc[promoter_id]))->save($network);
				}
				//更新统计表
				$caoni['day'] = array('exp', "day+$issc[money]");
				$caoni['weeks'] = array('exp', "weeks+$issc[money]");
				$caoni['month'] = array('exp', "month+$issc[money]");
				$caoni['total'] = array('exp', "total+$issc[money]");
				M('SystemTongji')->where(array('id' => 1))->save($caoni);
				
				$data['return_code'] = $return_code;
				$wxpays = new WeiXinPayController();
				$res = $wxpays->verifyNotify($data);				
				
			}				
		}
	}			
	
}
