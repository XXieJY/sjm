<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends LoadController {
    public function index(){

        $cache = A('Cache');
        $cache->chushi("/Html_Cache/", 'index');
        $cache->read_cache(); //读取缓存
        
        $this->sousuo();
        $this->slide();
        $this->hots();
        $this->xinzuo();
        $this->rexiao();
        $this->jingpin();
        $this->bar();
        $this->display();

    }
    //搜索
    public function sousuo() {
        $bookpromote = M('BookPromote');
        $arr = $bookpromote->where(array('promote_id' => 1))->field('book_id,book_name')->limit(3)->order('xu asc')->select();
        $this->assign('sousuo', $arr);

    }

    //3个幻灯片
    public function slide() {
        $bookpromote = M('BookPromote');
        $arr = $bookpromote->where(array('promote_id' => 2))->field('book_id,book_name,upload_img')->limit(3)->order('xu asc')->select();
        $this->assign('slide', $arr);
    }

    //热门推荐
    public function hots() {
        $bookpromote = D('BooktuiView');
        $arr = $bookpromote->where(array('promote_id' => 3))->field('book_id,book_name,upload_img,book_brief,type_id')->limit(6)->order('xu asc')->select();
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i][book_type] = $this->booktype($arr[$i][type_id]);
        }
        $this->assign('hots', $arr);
    }

    //潜力新作
    public function xinzuo() {
        $bookpromote = M('BookPromote');
        $arr = $bookpromote->where(array('promote_id' => 4))->field('book_id,book_name,upload_img')->limit(8)->order('xu asc')->select();
        $this->assign('xinzuo', $arr);
    }

    //热销专区
    public function rexiao() {
        $bookpromote = D('BooktuiView');
        $arr = $bookpromote->where(array('promote_id' => 5))->field('book_id,book_name,upload_img,book_brief,type_id')->limit(6)->order('xu asc')->select();
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i][book_type] = $this->booktype($arr[$i][type_id]);
        }
        $this->assign('rexiao', $arr);
    }

    //精品推荐
    public function jingpin() {
        $bookpromote = D('BooktuiView');
        $arr = $bookpromote->where(array('promote_id' => 6))->field('book_id,book_name,upload_img,book_brief,type_id')->limit(6)->order('xu asc')->select();
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i][book_type] = $this->booktype($arr[$i][type_id]);
        }
        $this->assign('jingpin', $arr);
    }

//广告
    public function bar() {
        $ban = M('SystemBan')->select();
        $this->assign('ban', $ban);
    }

    //小说类型
    private function booktype($booktype) {
        $tytes = array(
            '1' => "现代言情",
            '2' => "古代言情",
            '3' => "幻想言情",
            '4' => "悬疑灵异",
            '5' => "游戏竞技",
        );
        return $tytes[$booktype];


    }


}