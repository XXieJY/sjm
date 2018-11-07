<?php
namespace Home\Controller;
use Think\Controller;
//微信回调接口
    class WeiController extends Controller {

        private $fromUsername; //发送方帐号（一个OpenID）
        private $toUsername; //开发者微信号
        private $times; //当前时间
        private $Content; //发来的文本信息
        private $MsgType; //消息累心

        public function index() {
            $this->responseMsg();
        }

        //获取post数据
        public function responseMsg() {

            $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
            if (!empty($postStr)) {
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $this->fromUsername = $postObj->FromUserName; //发送方帐号（一个OpenID）
                $this->toUsername = $postObj->ToUserName; //获取开发者微信号
                $this->Content = trim($postObj->Content); //传送来的信号（代码含义：去除前后空格）
                $this->MsgType = trim($postObj->MsgType); //消息类型
                $this->times = time(); //获取时间 
                //消息类型判断
                switch ($this->MsgType) {
                    case "text":
                        $this->receiveText();
                        break;
                    case "event":
                        $this->receiveEvent($postObj);
                        break;
                    default:
                        $this->noknow();
                        break;
                }
            }
        }

        //txt消息判断
        public function receiveText() {

            switch ($this->Content) {
                case "客服":
                    $this->kefu();
                    break;
                case "充值代码":
                    $this->chongzhi();
                    break;             
                default:
                    $this->zhaoshu();
                    break;
            }
        }

        //事件判断            
        private function receiveEvent($object) {
            switch ($object->Event) {
                case "subscribe":
                    $this->guanzhu($object); //关注推送
                    break;
                case "CLICK":
                    $this->clicks($object->EventKey); //菜单点击事件
                    break;
                default:
                    $this->transmitText("谢谢");
                    break;
            }
        }

        // 餐单点击推送
        private function clicks($keys) {
            switch ($keys) {
                case 'kefus':
                    $contentStr = "我们的联系方式为！ \r\nQQ：3404797373";
                    $this->transmitText($contentStr);
                    break;
                case 'jingpinnvpin':
                    $this->tuwen(10);
                    break;
                case 'rexiaoguyan':
                    $this->tuwen(11);
                    break;   
                case 'fuli':
                    $contentStr = "每日签到送礼<a href=\"http://m.sjnovel.com/Home/Sign/index.html\">【点击签到】</a>";
                    $this->transmitText($contentStr);
                    break;                  
                default:
                    $this->transmitText("否");
                    break;
            }
        }

        //不知道什么消息           
        private function noknow() {
            $this->transmitText($this->buxiangguan());
        }

        //信息转接到客服
        public function kefu() {
            $textTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[%s]]></MsgType>
			</xml>";

            $msgType = "transfer_customer_service"; //回复类型
            $resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $this->times, $msgType);
            echo $resultStr;
        }

        //找书
        private function zhaoshu() {
            $books = M('Book')->where(array('book_name' => array('like', "%$this->Content%")))->field('book_id,book_name')->select();
            if (is_array($books)) {
                for ($i = 0; $i < count($books); $i++) {
                    $xinxi.=$books[$i][book_name] . ":http://m.sjnovel.com/books/" . $books[$i][book_id] . ".html\r\n\r\n";
                }
                $this->transmitText($xinxi);
                exit();
            }
            $con = M('WeixinAutomatic')->where(array('title' => $this->Content))->find();
            if ($con) {
                $this->transmitText($con[content]);
            } else {
                $this->transmitText($this->buxiangguan());
            }
        }

        //关注推送信息       
        private function guanzhu($object) {
            $contentStr = "亲爱的读者，欢迎关注水晶书城，我们为您提供言情小说、青春小说、悬疑小说等各种小说类型，海量原创作品供您选择！\r\n如果网站有什么问题请联系我们！我们将给予奖励！";
            $this->transmitText($contentStr);
        }

        //充值金额
        private function chongzhi() {
            $chongzhi = M('SystemTongji')->where(array('id' => 1))->field('day')->find();
            $this->transmitText($chongzhi[day]);
        }

        //回复信息
        private function transmitText($contentStr) {
            $textTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[%s]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			<FuncFlag>0</FuncFlag>
			</xml>";

            $msgType = "text"; //回复类型
            $resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $this->times, $msgType, $contentStr);
            echo $resultStr;
        }

        //没有找到东西        
        public function buxiangguan() {
            $str = "亲，你输入不正确，请输入正确关键字:\r\n
【1】输入  登陆  ；http://m.sjnovel.com/Home/Login/index.html\r\n
【2】输入  充值  ；http://m.sjnovel.com/Home/Pay/index.html\r\n
【3】输入  首页  ；http://m.sjnovel.com/\r\n
【4】输入  阅读记录；http://m.sjnovel.com/Home/Bookcase/index.html！";
            return $str;
        }

        //图文消息模板
        public function tuwen($id) {

            //准备数据                
            $bookpromote = M('BookPromote');
            $arr = $bookpromote->where(array('promote_id' => $id))->field('book_id,upload_img,book_brief')->limit(5)->order('xu asc')->select();
            $num = count($arr);
            //准备模板    
            $textTpl = "<xml>
                        <ToUserName><![CDATA[$this->fromUsername]]></ToUserName>
                        <FromUserName><![CDATA[$this->toUsername]]></FromUserName>
                        <CreateTime>$this->times</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <ArticleCount>$num</ArticleCount>
                        <Articles>";
            foreach ($arr as $key => $value) {


                $textTpl.= "<item>
                        <Title><![CDATA[$value[book_brief]]]></Title> 
                        <Description><![CDATA[$value[book_brief]]]></Description>
                        <PicUrl><![CDATA[http://www.sjnovel.com/Upload/Book/zhong/$value[upload_img]]]></PicUrl>
                        <Url><![CDATA[http://m.sjnovel.com/books/$value[book_id].html]]></Url>
                        </item>";
            }
            $textTpl.= "
                        </Articles>
                        </xml>  ";
            echo $textTpl;
        }

        public function createmenu() {
            $appid = "wx68c0a8ca531f2780"; //微信appid
            $secret = "62a57dfe79b2423fab051d452a9b822f";
            //得到access_token
            $access_token = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret");
            $access_obj = json_decode($access_token);
            $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_obj->access_token";
            $arr = array(
                'button' => array(
                 array(
                    'name' => urlencode("阅读记录"),
                    'type' => 'view',
                 "url" => "http://m.sjnovel.com/Home/Bookcase/index.html",
                    ),
                array(
                    'name' => urlencode("好书推荐"),
                    'sub_button' => array(
                     array(
                    'name' => urlencode("首页搜书"),
                        'type' => 'view',
                         "url" => "http://m.sjnovel.com/",
                            ),
                 array(
                 'name' => urlencode("往期精彩"),
                'type' => 'view',
                 "url" => "http://mp.weixin.qq.com/mp/getmasssendmsg?__biz=MzI4NDQ3NzQxNg==#wechat_webview_type=1&wechat_redirect",
                            ),
                 array(
                'name' => urlencode("小说排行"),
                'type' => 'view',
                "url" => "http://m.sjnovel.com/Home/Rank/index.html",
                            ),
                 array(
                'name' => urlencode("精品女频"),
                'type' => 'click',
                 "key" => "jingpinnvpin",
                            ),
                 array(
                'name' => urlencode("热销古言"),
                'type' => 'click',
                "key" => "rexiaoguyan",
                            ),
                        )
                    ),
            array(
            'name' => urlencode("粉丝导航"),
        'sub_button' => array(
            array(
            'name' => urlencode("用户登录"),
            'type' => 'view',
        "url" => "http://m.sjnovel.com/Home/Login/index.html",
                         ),
         array(
            'name' => urlencode("我要充值"),
            'type' => 'view',
         "url" => "http://m.sjnovel.com/Home/Pay/index.html",
                            ),
             array(
            'name' => urlencode("个人中心"),
            'type' => 'view',
        "url" => "http://m.sjnovel.com/Home/Mybook/index.html",
                            ),
        array(
            'name' => urlencode("联系客服"),
            'type' => 'click',
            "key" => "kefus",
                            ),
            array(
            'name' => urlencode("粉丝福利"),
            'type' => 'click',
             "key" => "fuli",
                            )                            
                        )
                    )
                )
            );
            $jsondata = urldecode(json_encode($arr));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsondata);
            echo $ch;
            $r = curl_exec($ch);
            print_r($r);
            curl_close($ch);
            echo "收工";
        }
    }
    
