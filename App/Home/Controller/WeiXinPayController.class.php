<?php
namespace Home\Controller;
use Think\Controller;
class WeiXinPayController extends Controller {
    protected $gateway    = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    protected $orderquery = 'https://api.mch.weixin.qq.com/pay/orderquery';
    protected $config     = array(
        'appid'     => 'wxf108bf13f99ac9e4',
        'appsecret' => '5f0cf6f63503bd6637c7deebc62556a7',
        'mchid'     => '1453761702',
        'key'       => '435f4f4eb327e8afa2fd460ff5a893ed',
		'notify_url'	=> 'http://m.sjnovel.com/Home/Notify/wxnotify',
    );
    public function check()
    {
        if (!$this->config['appid'] || !$this->config['appsecret'] || !$this->config['mchid'] || !$this->config['key']) {
            E("微信支付设置有误！");
        }
        return true;
    }
    public function buildRequestForm($trade,$total_fee,$body)
    {
        // APP支付
        if (C('IS_API')) {
            $param = array(
                'appid'            => $this->config['appid'],
                'mch_id'           => $this->config['mchid'],
                'nonce_str'        => $this->getNonceStr(),
                'body'             => $body,
                'out_trade_no'     => $trade,
                'total_fee'        => $total_fee,
                'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
                'notify_url'       => $this->config['notify_url'],
                'trade_type'       => 'APP',
            );
            // 签名
            $param['sign'] = $this->MakeSign($param);
            $xml_param     = $this->ToXml($param);
            $result        = $this->FromXml($this->postXmlCurl($xml_param, $this->gateway));
			
            if ($result['return_code'] === 'SUCCESS') {
                if ($this->CheckSign($result)) {
                    // 统一下单接口返回正常的prepay_id，再按签名规范重新生成签名后，将数据传输给APP。
                    // 参与签名的字段名为appId，partnerId，prepayId，nonceStr，timeStamp，package。注意：package的值格式为Sign=WXPay
                    $prepayparams              = array();
                    $prepayparams['appid']     = $result['appid'];
                    $prepayparams['partnerid'] = $result['mch_id'];
                    $prepayparams['prepayid']  = $result['prepay_id'];
                    $prepayparams['noncestr']  = $result['nonce_str'];
                    $prepayparams['package']   = 'Sign=WXPay';
                    $prepayparams['timestamp'] = time();
                    $prepayparams['sign']      = $this->MakeSign($prepayparams); // 签名
                    $return['json']            = json_encode($prepayparams);
                    return $return;
                }
            }
        } else {
            // 获取用户openId，微信公众号JSAPI支付必须
            $openId = $this->GetOpenid();
            $param  = array(
                'appid'            => $this->config['appid'],
                'mch_id'           => $this->config['mchid'],
                'nonce_str'        => $this->getNonceStr(),
                'body'             => $body,
                'out_trade_no'     => $trade,
                'total_fee'        => $total_fee,
                'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
                'notify_url'       => $this->config['notify_url'],
                'trade_type'       => 'JSAPI',
                'openid'           => $openId,
            );
			//dump($param);exit;
            // 签名
            $param['sign'] = $this->MakeSign($param);
            $xml_param     = $this->ToXml($param);
            $result        = $this->FromXml($this->postXmlCurl($xml_param, $this->gateway));
			
            if ($result['return_code'] === 'SUCCESS') {
				$total = $total_fee*0.01;
                if ($this->CheckSign($result)) {
                    // 获取JSAPI所需参数
                    $jsApiParameters = $this->GetJsApiParameters($result);
                    $pay_page        = <<<EOF
                        <html>
                            <head>
                                <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
                                <meta name="viewport" content="width=device-width, initial-scale=1"/>
                                <title>微信支付</title>
                                <script type="text/javascript">
                                //调用微信JS api 支付
                                function jsApiCall()
                                {
                                    WeixinJSBridge.invoke(
                                        'getBrandWCPayRequest',
                                        {$jsApiParameters},
                                        function(res){
											//alert(res.err_desc);
                                            //WeixinJSBridge.log(res.err_msg);
                                            //alert(res.err_code+res.err_desc+res.err_msg);
											if(res.err_msg=="get_brand_wcpay_request:ok"){
												window.location.href='/Home/Pay/index';
											}else if(res.err_msg=="get_brand_wcpay_request:cancel"){
												//alert("支付取消");
											}else if(res.err_msg=="get_brand_wcpay_request:fail"){
												alert("支付失败");
											}
                                        }
                                    );
                                }
                                function callpay()
                                {
                                    if (typeof WeixinJSBridge == "undefined"){
                                        if( document.addEventListener ){
                                            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                                        }else if (document.attachEvent){
                                            document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                                            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                                        }
                                    }else{
                                        jsApiCall();
                                    }
                                }
                                </script>
                            </head>
                            <body style="text-align:center">
                                <br/>
                                <div align="center" style="margin:0 auto;text-align:center;margin-top:150px">
									<font color="#9ACD32" style="text-align:center;"><b>订单号为<span style="color:#f00;font-size:20px">{$trade}</span></b></font><br/>
									<font color="#9ACD32" style="text-align:center;"><b>该笔订单支付金额为<span style="color:#f00;font-size:30px">{$total}</span>元</b></font><br/><br/>
                                    <button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>
                                </div>
                            </body>
                            </html>
EOF;
                    return $pay_page;
                }
            } else {
				//dump($result);exit;
                E("微信订单错误！" . $result['return_msg']);
            }
        }
    }
    /**
     * 异步通知验证
     */
    public function verifyNotify($notify)
    {
        //获取通知的数据
        if ($notify['return_code'] === 'SUCCESS') {
            if (!array_key_exists("transaction_id", $notify)) {
                E("输入参数不正确！");
            }
            $param['transaction_id'] = $notify["transaction_id"];
            $param['appid']          = $this->config['appid'];
            $param['mch_id']         = $this->config['mchid'];
            $param['nonce_str']      = $this->getNonceStr();
            $param['sign']           = $this->MakeSign($param);
            $xml_param               = $this->ToXml($param);
            $result                  = $this->FromXml($this->postXmlCurl($xml_param, $this->orderquery));
            if ($this->CheckSign($result)) {
                $result['status'] = ($result['result_code'] == 'SUCCESS') ? true : false;
                $result['money']  = $result['total_fee'] / 100;
                $this->info       = $result;
                return true;
            }
        } else {
            E('通知错误');
        }
    }
    /**
     *
     * 获取jsapi支付的参数
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     * @throws WxPayException
     *
     * @return json数据，可直接填入js函数作为参数
     */
    public function GetJsApiParameters($UnifiedOrderResult)
    {
        if (!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "") {
            E("参数错误");
        }
        $jsapi['appId']     = $UnifiedOrderResult["appid"];
        $jsapi['timeStamp'] = (string) time();
        $jsapi['nonceStr']  = $this->getNonceStr();
        $jsapi['appId']     = $UnifiedOrderResult["appid"];
        $jsapi['package']   = "prepay_id=" . $UnifiedOrderResult['prepay_id'];
        $jsapi['signType']  = 'MD5';
        $jsapi['paySign']   = $this->MakeSign($jsapi);
        $parameters         = json_encode($jsapi);
        return $parameters;
    }
    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml  需要post的xml数据
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     * @throws WxPayException
     */
    private static function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //如果有配置代理这里就设置代理
        // if(WxPayConfig::CURL_PROXY_HOST != "0.0.0.0"
        //             && WxPayConfig::CURL_PROXY_PORT != 0){
        //             curl_setopt($ch,CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
        //             curl_setopt($ch,CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
        //         }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); //严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, false);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($useCert == true) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, WxPayConfig::SSLCERT_PATH);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, WxPayConfig::SSLKEY_PATH);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            E("curl出错，错误码:$error");
        }
    }
    /*
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str   = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function MakeSign($param)
    {
        //签名步骤一：按字典序排序参数
        ksort($param);
        $string = $this->ToUrlParams($param);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $this->config['key'];
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    /**
     *
     * 检测签名
     */
    public function CheckSign($param)
    {
        $sign = $this->MakeSign($param);
        if ($param['sign'] == $sign) {
            return true;
        } else {
            E("签名错误！");
        }
    }
    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    public function ToUrlParams($param)
    {
        $buff = "";
        foreach ($param as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
    /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public function ToXml($param)
    {
        if (!is_array($param)
            || count($param) <= 0) {
            E("数组数据异常！");
        }
        $xml = "<xml>";
        foreach ($param as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public function FromXml($xml)
    {
        if (!$xml) {
            E("xml数据异常！");
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $this->values;
    }
    /**
     *
     * 通过跳转获取用户的openid，跳转流程如下：
     * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
     * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
     *
     * @return 用户的openid
     */
    public function GetOpenid()
    {
        //通过code获得openid
        if (!isset($_GET['code'])) {
            //触发微信返回code码
            $baseUrl = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            $url     = $this->__CreateOauthUrlForCode($baseUrl);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code   = $_GET['code'];
            $openid = $this->getOpenidFromMp($code);
            return $openid;
        }
    }
    /**
     *
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     *
     * @return openid
     */
    public function GetOpenidFromMp($code)
    {
        $url = $this->__CreateOauthUrlForOpenid($code);
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // if(WxPayConfig::CURL_PROXY_HOST != "0.0.0.0"
        //             && WxPayConfig::CURL_PROXY_PORT != 0){
        //             curl_setopt($ch,CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
        //             curl_setopt($ch,CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
        //         }
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data       = json_decode($res, true);
        $this->data = $data;
        $openid     = $data['openid'];
        return $openid;
    }
    /**
     *
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"]         = $this->config['appid'];
        $urlObj["redirect_uri"]  = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"]         = "snsapi_base";
        $urlObj["state"]         = "#wechat_redirect";
        $bizString               = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }
    /**
     *
     * 构造获取open和access_toke的url地址
     * @param string $code，微信跳转带回的code
     *
     * @return 请求的url
     */
    private function __CreateOauthUrlForOpenid($code)
    {
        $urlObj["appid"]      = $this->config['appid'];
        $urlObj["secret"]     = $this->config['appsecret'];
        $urlObj["code"]       = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString            = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }
 
}