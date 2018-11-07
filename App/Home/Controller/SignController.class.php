<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;
    //签到送礼
    class SignController extends GlobalController {
        public function index(){
            $this->display();
        }

        public function add() {
            $user = M('User');
            $where['user_id'] = $this->to[user_id];
            $myuser = $user->where($where)->field('sign_time')->find();
            //判断是否签到过了
            if ($myuser[sign_time] != date("Y-m-d", time())) {
                $sing = M('SystemSing');
                $mysing = $sing->select();
                foreach ($mysing as $key => $val) {
                    $arr[++$a] = $val['chance'];
                }
                $rid = $this->get_rand($arr) - 1; //得到奖品在数组中那一条
                //结果
                $jieguo = rand(1, $mysing[$rid][num]);
                //添加数据
                $data['user_id'] = $this->to[user_id];
                $data['prize'] = $mysing[$rid][prize];
                $data['num'] = $jieguo;
                $data['time'] = date('y-m-d H:i:s', time());
                M('SystemSingRecord')->add($data);
                //赠送东西
                $save['sign_time'] = date("Y-m-d", time());
                switch ($mysing[$rid][type]) {
                    case 1:
                        $save['alance'] = array('exp', "alance+$jieguo");
                        break;
                    case 2:
                        $save['vote'] = array('exp', "vote+$jieguo");
                        break;
                    case 3:
                        $save['vipvote'] = array('exp', "vipvote+$jieguo");
                        break;
                }
                $user->where(array('user_id' => $this->to[user_id]))->save($save);
                echo "签到成功得到:".$mysing[$rid][prize]."+".$jieguo;
            } else {
                echo "您已经签到过了";
            }
        }

        //几率函数
        private function get_rand($proArr) {
            $result = '';      //概率数组的总概率精度    
            $proSum = array_sum($proArr);      //概率数组循环  
            foreach ($proArr as $key => $proCur) {
                $randNum = mt_rand(1, $proSum);
                if ($randNum <= $proCur) {
                    $result = $key;
                    break;
                } else {
                    $proSum -= $proCur;
                }
            }
            unset($proArr);
            return $result;
        }

    }
    