<?php
namespace Home\Controller;
use Think\Controller;
//充值公式
class PayaccController extends Controller {

    public function index($user_id, $promoter_id, $types) {
        $data['user_id'] = $user_id;
		$money = intval($_GET['money']);
        //判断编辑
        if ($promoter_id) {
            $data['promoter_id'] = $promoter_id; //编辑号
            $data['commission'] = ($money * 0.15) * 100; //编辑提成
        }
		
        //充值类型
        switch ($_GET[payChannelType]) {
            case 12:
                $data['type'] = "支付宝充值";
                $data['readmoney'] = ($money * 100) + $this->jiacheng($money * 100) + $this->duosong($money); //实际到账阅读币
                break;
            case 13:
                $data['type'] = "微信充值";
                $data['readmoney'] = ($money * 95) + $this->jiacheng($money * 100) + $this->duosong($money); //实际到账阅读币
                break;
            case 11:
                $data['type'] = "银联充值";
                $data['readmoney'] = ($money * 90) + $this->jiacheng($money * 90) + $this->duosong($money); //实际到账阅读币
                break;
            case 10:
                $data['type'] = "微信公众号支付";
                $data['readmoney'] = ($money * 100) + $this->jiacheng($money * 100) + $this->duosong($money); //实际到账阅读币
                break;
            case 18:
                $data['type'] = "银联充值";
                $data['readmoney'] = ($money * 90) + $this->jiacheng($money * 90) + $this->duosong($money); //实际到账阅读币
                break;
            case 100:
                $data['type'] = "微信扫描充值";
                $data['readmoney'] = ($money * 95) + $this->jiacheng($money * 100) + $this->duosong($money); //实际到账阅读币
                break;        
        }
       // $data['readmoney']=$data['readmoney']*2;//双倍
        $data['trade'] = $types.date('YmdHis',time()).rand(1000,9999); //订单号
        $data['money'] = $money;
        $data['state'] = 1;
        $data['time'] = date('Y-m-d H:i:s', time());
        $data['ctime'] = date('Y-m-d', time());
		$User = M('User');
		$where['user_id'] = $user_id;
		$isUser = $User->field("user_id,channel_id")->where($where)->find();
		if(!empty($_COOKIE['channel'])){
			$data['channel_id'] = $_COOKIE['channel'];
		}else{
			$data['channel_id'] = $isUser['channel_id'];
		}
        $isok = M('SystemPay')->add($data);
        if ($isok) {
            return $data; //数据处理进行数组返回
        } else {
            $this->error("系统错误");
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

    //结果处理
    public function chuli($mhtOrderNo, $mhtOrderName) {
        //处理订单
        $systempay = M('SystemPay');
        $user = M('User');
        $issc = $systempay->where(array('trade' => $mhtOrderNo, 'state' => 1))->find();
        $zhanghu = $user->where(array('user_id' => $issc[user_id]))->field('integral,money,alance,channel_id')->find(); //账户充值额
        if (is_array($issc)) {
            $systempay->where('trade="'.$mhtOrderNo.'"')->save(array('transaction' => $mhtOrderName, 'channel_id'=>$zhanghu[channel_id], 'state' => 2));
            $data['money']=$zhanghu[money]+$issc[money];
            $data['integral']=($issc[money] * 10)+$zhanghu[integral];
            $data['alance']=$zhanghu[alance]+$issc[readmoney];
//                               
//            $data['money'] = array('exp', "money+$issc[money]"); //总充值
//            $integral = $issc[money] * 10;
//            $data['integral'] = array('exp', "integral+$integral"); //积分
//            $data['alance'] = array('exp', "alance+$issc[readmoney]"); //余额
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
        }
    }

}
