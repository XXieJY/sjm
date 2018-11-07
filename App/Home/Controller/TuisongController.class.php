<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;

//模板推送
class TuisongController extends Controller{

//获取微信唯一token
  public  function get_token(){

       $appid = "wx68c0a8ca531f2780";
       $appsecret = '62a57dfe79b2423fab051d452a9b822f';

       $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;

       $json=file_get_contents($url);

       $result=json_decode($json,true);

       $token= $result['access_token'];
 //echo $token;

       return $token; 
  }
 //发送模板消息
    public function send_template_message($data){

        $token =$this->get_token(); 


        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$token;


        $res = $this->http_request($url, $data);

      // var_dump($res);

        return json_decode($res, true);
    }
/*
   static function http_request($url, $data) {
            if(!is_array($data)){
         echo "参数必须是数组";
    }
    $httph =curl_init($url);
    curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($httph,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($httph, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
    curl_setopt($httph, CURLOPT_POST, 1);//设置为POST方式 
    curl_setopt($httph, CURLOPT_POSTFIELDS, $data);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($httph, CURLOPT_HEADER,1);
    $rst=curl_exec($httph);
    curl_close($httph);
    return $rst;
        }

        */

    protected function http_request($url, $data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;

    }

  public function sendRequest($book,$bookid){

     $user =M('User')->where(array('user_id'=>$_COOKIE[user_id]))->field('openid,pen_name')->find();

     $openid =$user['openid'];

     $pen_name =$user['pen_name'];

     $time =date("Y-m-d H:i:s");


    $model_id = "L2gAGBzlhINe3TKl5hi9CsmhU_GnKn2nVsyS6qGRGE4";//项目状态模板ID
    $template = array('touser' =>$openid,
     
      'template_id' =>$model_id,
                  
      'url' => "http://m.sjnovel.com/Home/chapter/".$bookid."/1.html",
     'topcolor' => "#FF0000",//顶部颜色，自定义
                  
    'data' => array(

    'first' => array('value' =>"您好，您已订阅成功.",'color' => "#173177", ),

       'keyword1' => array('value'=>$book,'color' => "#173177",),
       'keyword2'=>array('value'=>$time,'color'=>"#173177",),
                                  
    'remark'=>array('value'=>'点击详情开始阅读.','color'=>"#173177",))
                  );
     $this->send_template_message(urldecode(json_encode($template))); 


  }


}