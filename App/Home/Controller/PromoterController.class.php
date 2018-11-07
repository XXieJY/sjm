<?php
namespace Home\Controller;
use Think\Controller;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/5
 * Time: 16:03
 */
class PromoterController extends Controller{

    public function index($bookid,$num) {

         $this->statistical($bookid);

        //获取渠道id
        if(!empty($_GET['channel'])){
            setcookie("channel",$_GET['channel'],time()+86400,"/",".sjnovel.com");
            $channel=$_COOKIE['channel'];

        }
        //print_r($_COOKIE);
        $books = M('Book')->where(array('book_id' => $bookid))->field('cp_id,book_name,book_id,money,vip,chapter,words,time')->find();
        if (!is_array($books)) {
            $this->error("没有该章节");
            exit();
        }
        $this->assign('books', $books);
        //内容信息
        $neirong = M('Content');
        $content = $neirong->where(array('book_id' => $bookid, 'num' => $num,'status'=>0))->find();
        if (!is_array($content)) {
            A('Promote')->index(); //没用界面调用推广
            exit();
        }
        $this->assign('content', $content);
        //对收费的章节进行处理
        if ($content[the_price]) {
            if (cookie('user_id')) {
                //阅读记录
                $coll = M('BookCollection');
                $iscoll = $coll->where(array('book_id' => $bookid, 'user_id' => cookie('user_id')))->field('id')->find();

                if (is_array($iscoll)) {
                    $coll->where(array('book_id' => $bookid, 'user_id' => cookie('user_id')))->save(array('chapter' => $num));
                } else {
                    //没有收藏就收藏
                    $data[book_id] = $bookid;
                    $data[user_id] = $_COOKIE[user_id];
                    $data[chapter] = $num;
                    $data[time] = date('Y-m-d H:i:s', time());
                    $coll->add($data);
                    $datas['collection_day'] = array('exp', "collection_day+1");
                    $datas['collection_weeks'] = array('exp', "collection_weeks+1");
                    $datas['collection_month'] = array('exp', "collection_month+1");
                    $datas['collection_total'] = array('exp', "collection_total+1");
                    M('BookStatistical')->where(array('book_id' => $bookid))->save($datas);
                }
            }
			$user_m = M('User');
			$user_info = $user_m->where(array('user_id' => cookie('user_id')))->field('days')->find();
			$days = intval($user_info['days']);
			//判断包月天数
			if($days>0){
				$this->logins();
				//判断是否购买
				$con['user_id'] = cookie('user_id');
				$con['book_id'] = $books[book_id];
				$buyinfo = M('BookBuy')->where($con)->field('chapter_id')->find();
				if (!is_array($buyinfo)) {
					//创建购买信息
					$data['book_id'] = $books[book_id];
					$data['user_id'] = cookie('user_id');
					$data['chapter_id'] = "baoyue:";
					M('BookBuy')->add($data);
				}		
				$this->baoyue($books,$content);
				
			}else{
				//购买
				$this->buy($books, $content);
			}
            
        }

        //显示内容
        $neirong->where(array('content_id' => $content[content_id]))->save(array('clicknum' => array('exp', "clicknum+1")));
        if (is_array($content)) {
            //章节信息
            $cache = A('Cache');
            $cache->chushi("/Upfile/$bookid", $content[content_id]);
            $cache->read_cache(); //读取缓存
            $contents = M('Contents')->where(array('content_id' => $content[content_id]))->find();
            //组合内容
            $content[content] = $contents[content];
            $content[content] = str_replace("\n", "</p><p>", str_replace(" ", "", $content[content]));
            $chapterinfo['preid'] = $num <= 1 ? '1' : $num - 1;
            $chapterinfo['nextid'] = $num <= $books['chapter'] ? $num + 1 : '';

            if($bookid==78&&$num==19){$chapterinfo['nextid']=21;};
            $this->assign('chapterinfo', $chapterinfo);
            $this->assign('content', $content);
            $this->display();
            $cache->create_cache(); //生成缓存
        }
    }

 //更新用户点击
    public function statistical($bookid) {
        $data['click_day'] = array('exp', "click_day+1");
        $data['click_weeks'] = array('exp', "click_weeks+1");
        $data['click_month'] = array('exp', "click_month+1");
        $data['click_total'] = array('exp', "click_total+1");
        M("BookStatistical")->where(array('book_id' => $bookid))->save($data);
    }
    //是否登录
    private function logins() {
        //用户没有登录就登录
        if (!cookie('user_id')) {
            header("Location: /Home/Login/index.html");
            exit();
        }
    }

    //创建购买信息
    private function addbuy($books) {
        //登录后判断章节记录是否存在不存在创建
        $data['book_id'] = $books[book_id];
        $data['user_id'] = cookie('user_id');
        $data['chapter_id'] = ":";
        M('BookBuy')->add($data);
    }

    //收费章节判断
    private function buy($books, $content) {
        $this->logins();
        //判断是否购买
        $con['user_id'] = cookie('user_id');
        $con['book_id'] = $books[book_id];
        $buyinfo = M('BookBuy')->where($con)->field('chapter_id')->find();
        if (!is_array($buyinfo)) {
            $this->addbuy($books);
            $buyinfo = M('BookBuy')->where($con)->field('chapter_id')->find();
        }
        //判断收费类型
        switch ($books[vip]) {
            case 0:
                if (!strstr($buyinfo['chapter_id'], $content[content_id])) {
                    $this->anzhang($books, $content); //按章收费
                }
                break;
            case 1:
                if (!strstr($buyinfo['chapter_id'], "ben")) {
                    $this->anben($books); //按本收费
                }
                break;
            case 2:
                //判断包月时间是否过期
                $this->panduan();
                if (!strstr($buyinfo['chapter_id'], "bao")) {
                    $this->baoyue($books); //包月
                }
                break;
            case 3:
                return;
        }
    }

    //按章收费
    public function anzhang($books, $content) {
        $is = $this->consumption($books, $content[the_price], $content[content_id]);
        if ($is != 1) {
            $this->display('chongzhi'); //钱不够显示充值
            exit();
        } else {
            //购买成功产生记录
            $this->jilubiao($books[book_id], $content[the_price], "够买：$books[book_name]：$content[title]");
            //添加购买次数
            $this->mycishu($content[content_id]);
        }
    }

    //按本收费
    public function anben($books) {
        $is = $this->consumption($books, $books[money], "ben");
        if ($is != 1) {
            $this->display('anben'); //钱不够显示充值
            exit();
        } else {
            //购买成功产生记录
            $this->jilubiao($books[book_id], $books[money], "整本够买：$books[book_name]");
        }
    }

    //包月
    private function baoyue($books,$content) {
        //增加章节数据记录
        //$dataaa['chapter_id'] = array('exp', "CONCAT(chapter_id,',bao')");
		$shuju = $content[content_id];
		$dataaa['chapter_id'] = array('exp', "CONCAT(chapter_id,',$shuju')");
        M('BookBuy')->where(array('book_id' => $books[book_id], 'user_id' => cookie('user_id')))->save($dataaa);
        //跟新数据排名号
        $saves['payment_day'] = array('exp', "buy_day+1");
        $saves['payment_weeks'] = array('exp', "payment_weeks+1");
        $saves['payment_month'] = array('exp', "payment_month+1");
        $saves['payment_total'] = array('exp', "payment_total+1");
        M('BookStatistical')->where(array('book_id' => $books[book_id]))->save($saves);
        //记录日期
        A('Sales')->index($books[book_id], $books[cp_id], $books[book_name], 0, 1);
    }

//================================================================
    //书籍ID 价格 存什么数据
    public function consumption($books, $money, $shuju) {
        $uuu = M('User');
        $user = $uuu->where(array('user_id' => cookie('user_id')))->field('user_name,user_pass,alance')->find();
        $shell2 = md5($user['user_name'] . $user['user_pass'] . C('ALL_ps'));
        if ($shell2 == cookie('shell')) {
            if ($user[alance] >= $money) {
                //扣钱
                $map['alance'] = array('exp', "alance-$money");
                $uuu->where(array('user_id' => cookie('user_id')))->save($map);
                //记录日期
                A('Sales')->index($books[book_id], $books[cp_id], $books[book_name], $money, 0);
                //增加章节数据记录
                $dataaa['chapter_id'] = array('exp', "CONCAT(chapter_id,',$shuju')");
                M('BookBuy')->where(array('book_id' => $books[book_id], 'user_id' => cookie('user_id')))->save($dataaa);
                //更新粉丝记录
                A('Fans')->index($books[book_id], $money);
                //跟新数据排名号
                $saves['buy_day'] = array('exp', "buy_day+$money");
                $saves['buy_weeks'] = array('exp', "buy_weeks+$money");
                $saves['buy_month'] = array('exp', "buy_month+$money");
                $saves['buy_total'] = array('exp', "buy_total+$money");
                M('BookStatistical')->where(array('book_id' => $books[book_id]))->save($saves);
                //更新统计表
                $caoni['day'] = array('exp', "day+$money");
                $caoni['weeks'] = array('exp', "weeks+$money");
                $caoni['month'] = array('exp', "month+$money");
                $caoni['total'] = array('exp', "total+$money");
                M('SystemTongji')->where(array('id' => 2))->save($caoni);
                return 1;
            } else {
                //没钱
                return 2;
            }
        } else {
            $this->error("系统错误请重新登录！",U('/Home/Login/index'));
        }//z
    }

    //消费记录
    public function jilubiao($book_id, $the_price, $xinxi) {
        $datas['user_id'] = cookie('user_id');
        $datas['book_id'] = $book_id;
        $datas['type'] = 1;
        $datas['money'] = $the_price;
        $datas['dosomething'] = $xinxi;
        $datas['date'] = date('Y-m-d H:i:s', time());
        M('UserConsumerecord')->add($datas);
    }

    //添加购买次数
    public function mycishu($content_id) {
        M('Content')->where(array('content_id' => $content_id))->save(array('dycs' => array('exp', "dycs+1")));
    }

    //包月时间判断
    public function panduan() {
        $user = M('User')->where(array('user_id' => cookie('user_id')))->field('vip_time')->find();
        if (strtotime($user[vip_time]) < strtotime(date('Y-m-d H:i:s', time()))) {
            $this->assign('user', $user);
            $this->display('baoyue');
            exit();
        }
    }
}