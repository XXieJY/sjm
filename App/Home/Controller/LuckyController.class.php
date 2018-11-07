<?php
namespace Home\Controller;
use Think\Controller;
//翻版抽奖
class LuckyController extends GlobalController {

    public function index() {
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        $this->display();
    }

    //抽奖记录
    public function record() {

        $sysch = M('SystemLuckyrecord');
        $count = $sysch->where(array('user_id' => $this->to[user_id]))->count(); // 查询满足要求的总记录数   
        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $Page->setConfig('theme', "<div class=\"pager\">&nbsp;&nbsp;%UP_PAGE%&nbsp;&nbsp;%DOWN_PAGE%
                              <div class=\"pager-item\">到</div>
                              <div class=\"pager-item page-num-input\">
                                <input type=\"text\" id=\"pageText\"/>
                              </div>
                              <div class=\"pager-item\">页</div>
                              <div class=\"pager-item goto\" onClick=\"javascript:window.location.href='/Home/Lucky/record/p/'+document.getElementById('pageText').value;\">跳转</div>
                              <div class=\"pager-item\">%NOW_PAGE%/%TOTAL_PAGE%</div>
                            </div>");
        //内容显示
        $jilu = $sysch->where(array('user_id' => $this->to[user_id]))->field('prize,status,luckytime')->limit($Page->firstRow . ',' . $Page->listRows)->order('luckytime desc')->select();
        $this->assign('jilu', $jilu);
        // 分页显示输出
        $show = $Page->show();
        $this->assign('page', $show);
        $this->display();
    }

    //抽奖
    public function chou() {
        $uu = M('User');
        $alance = $uu->where(array('user_id' => $this->to[user_id]))->getField('alance');
        if ($alance < 100) {
            echo 2;
            exit;
        }
        //扣钱
        $map['alance'] = array('exp', "alance-100");
        $r_id = $uu->where(array('user_id' => cookie('user_id')))->save($map);
        //添加消费表
        $datas['user_id'] = $this->to[user_id];
        $datas['book_id'] = 0;
        $datas['type'] = 4;
        $datas['money'] = 100;
        $datas['dosomething'] = "抽奖一次";
        $datas['date'] = date('Y-m-d H:i:s', time());
        M('UserConsumerecord')->add($datas);
        if ($r_id > 0) {
            $this->chuli();
        }
    }

    //抽奖处理
    private function chuli() {
        //查询有的奖品
        $map['v'] = array('gt', 0);
        $list = M('SystemLucky');
        $jiangpin = $list->where($map)->order('id asc')->select();
        if (is_array($jiangpin)) {
            $zong = $list->order('id asc')->select(); //总奖品
            $arr = NULL;
            foreach ($jiangpin as $key => $val) {
                $arr[++$a] = $val['chance'];
            }
            $rid = $this->get_rand($arr) - 1; //得到奖品在数组中那一条
            $list->where(array('id' => $jiangpin[$rid]['id']))->setDec('v'); //奖品减掉一次
            $this->jieguo($jiangpin[$rid]); //发放奖品
            $res['yes'] = $jiangpin[$rid]['prize']; //中奖项
            //去除总奖品里面的中奖项目
            foreach ($zong as $key => $val) {
                if ($zong[$key][id] == $jiangpin[$rid][id]) {
                    unset($zong[$key]);
                    break;
                }
            }
            shuffle($zong); //打乱数组顺序 
            for ($i = 0; $i < count($zong); $i++) {
                $pr[] = $zong[$i]['prize'];
            }
            $res['no'] = $pr; //未中奖项              
            echo json_encode($res);
        } else {
            echo 3;
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

//发放奖品
    public function jieguo($arr) {
        switch ($arr[id]) {
            case 1:
                $this->yi($arr);
                break;
            case 2:
                $this->er($arr);
                break;
            case 3:
                $this->san($arr);
                break;
            case 4:
                $this->si($arr);
                break;
            case 5:
                $this->wu($arr);
                break;
            case 6:
                $this->liu($arr);
                break;
            case 7:
                $this->qi($arr);
                break;
            case 8:
                $this->ba($arr);
                break;
            case 9:
                $this->jiu($arr);
                break;
            case 10:
                $this->shi($arr);
                break;
            case 11:
                $this->shiyi($arr);
                break;
            case 12:
                $this->shier($arr);
                break;
            default:
                echo 2;
                break;
        }
    }

//==========奖品区域
    private function yi($arr) {
        //苹果手机
        $data['prize'] = $arr[prize]; //奖品名称
        $data['status'] = 2; //奖品状态1已领2未领
        $data['money'] = 5000; //价值
        $data['releasetime'] = NULL; //发放时间
        $data['luckytime'] = date('y-m-d H:i:s', time()); //中奖时间
        $this->shujuku($data);
    }

    private function er($arr) {
        //一台亚马逊阅读器
        $data['prize'] = $arr[prize]; //奖品名称
        $data['status'] = 2; //奖品状态1已领2未领
        $data['money'] = 1000; //价值
        $data['releasetime'] = NULL; //发放时间
        $data['luckytime'] = date('y-m-d H:i:s', time()); //中奖时间
        $this->shujuku($data);
    }

    private function san($arr) {
        //100元话费
        $data['prize'] = $arr[prize]; //奖品名称
        $data['status'] = 2; //奖品状态1已领2未领
        $data['money'] = 100; //价值
        $data['releasetime'] = NULL; //发放时间
        $data['luckytime'] = date('y-m-d H:i:s', time()); //中奖时间
        $this->shujuku($data);
    }

    private function si($arr) {
        //50元话费
        $data['prize'] = $arr[prize]; //奖品名称
        $data['status'] = 2; //奖品状态1已领2未领
        $data['money'] = 50; //价值
        $data['releasetime'] = NULL; //发放时间
        $data['luckytime'] = date('y-m-d H:i:s', time()); //中奖时间
        $this->shujuku($data);
    }

    private function wu($arr) {
        //50元话费
        $data['prize'] = $arr[prize]; //奖品名称
        $data['status'] = 2; //奖品状态1已领2未领
        $data['money'] = 30; //价值
        $data['releasetime'] = NULL; //发放时间
        $data['luckytime'] = date('y-m-d H:i:s', time()); //中奖时间
        $this->shujuku($data);
    }

    private function liu($arr) {
        //200M流量
        $data['prize'] = $arr[prize]; //奖品名称
        $data['status'] = 2; //奖品状态1已领2未领
        $data['money'] = 30; //价值
        $data['releasetime'] = NULL; //发放时间
        $data['luckytime'] = date('y-m-d H:i:s', time()); //中奖时间
        $this->shujuku($data);
    }

    private function qi($arr) {
        //100M流量
        $data['prize'] = $arr[prize]; //奖品名称
        $data['status'] = 2; //奖品状态1已领2未领
        $data['money'] = 20; //价值
        $data['releasetime'] = NULL; //发放时间
        $data['luckytime'] = date('y-m-d H:i:s', time()); //中奖时间
        $this->shujuku($data);
    }

    private function ba($arr) {
        //200阅读币
        $data['prize'] = $arr[prize]; //奖品名称
        $data['status'] = 1; //奖品状态1已领2未领
        $data['money'] = 2; //价值
        $data['releasetime'] = date('y-m-d H:i:s', time()); //发放时间
        $data['luckytime'] = date('y-m-d H:i:s', time()); //中奖时间
        $this->shujuku($data);
        $datas['alance'] = array('exp', "alance+200");
        M('User')->where(array('user_id' => $this->to[user_id]))->save($datas);
    }

    private function jiu($arr) {
        //100阅读币
        $data['prize'] = $arr[prize]; //奖品名称
        $data['status'] = 1; //奖品状态1已领2未领
        $data['money'] = 1; //价值
        $data['releasetime'] = date('y-m-d H:i:s', time()); //发放时间
        $data['luckytime'] = date('y-m-d H:i:s', time()); //中奖时间
        $this->shujuku($data);
        $datas['alance'] = array('exp', "alance+100");
        M('User')->where(array('user_id' => $this->to[user_id]))->save($datas);
    }

    private function shi($arr) {
        //50阅读币
        $data['prize'] = $arr[prize]; //奖品名称
        $data['status'] = 1; //奖品状态1已领2未领
        $data['money'] = 0.5; //价值
        $data['releasetime'] = date('y-m-d H:i:s', time()); //发放时间
        $data['luckytime'] = date('y-m-d H:i:s', time()); //中奖时间
        $this->shujuku($data);
        $datas['alance'] = array('exp', "alance+50");
        M('User')->where(array('user_id' => $this->to[user_id]))->save($datas);
    }

    private function shiyi($arr) {
        //2颗钻石
        $data['prize'] = $arr[prize]; //奖品名称
        $data['status'] = 1; //奖品状态1已领2未领
        $data['money'] = 0; //价值
        $data['releasetime'] = date('y-m-d H:i:s', time()); //发放时间
        $data['luckytime'] = date('y-m-d H:i:s', time()); //中奖时间
        $this->shujuku($data);
        $datas['vipvote'] = array('exp', "vipvote+2");
        M('User')->where(array('user_id' => $this->to[user_id]))->save($datas);
    }

    private function shier($arr) {
        //1朵鲜花
        $data['prize'] = $arr[prize]; //奖品名称
        $data['status'] = 1; //奖品状态1已领2未领
        $data['money'] = 0; //价值
        $data['releasetime'] = date('y-m-d H:i:s', time()); //发放时间
        $data['luckytime'] = date('y-m-d H:i:s', time()); //中奖时间
        $this->shujuku($data);
        $datas['vote'] = array('exp', "vote+1");
        M('User')->where(array('user_id' => $this->to[user_id]))->save($datas);
    }

//添加数据库记录
    private function shujuku($arr) {
        $data['user_id'] = $this->to[user_id];
        $data['pen_name'] = $this->to[pen_name];
        $data['prize'] = $arr['prize'];
        $data['status'] = $arr['status'];
        $data['money'] = $arr['money'];
        $data['releasetime'] = $arr['releasetime'];
        $data['luckytime'] = $arr['luckytime'];
        M('SystemLuckyrecord')->add($data);
    }

}
