<?php
namespace Home\Controller;
use Think\Controller;

//排行榜
class RankController extends Controller {

    public function _empty() {
        //该方法即为空操作
        echo '当前操作不存在';
    }

    public function index() {
        $this->vipvote();
        $this->dingyue();
        $this->dianji();
        $this->exceptional();
        $this->vote();
        $this->collection();
        $this->display();
    }

    //VIP
    public function vipvote() {
        //模型
        $list = D('RankinglistView');
        $con['is_show'] = 1;
        $con['book_id'] = array('not in','78,61');
        //日
        $arr = $list->where($con)->field('book_id,book_name,upload_img')->order('vipvote_total DESC')->limit(10)->select();
        $this->assign('vipvote', $arr);
    }

    //订阅
    public function dingyue() {
        //模型
        $list = D('RankinglistView');
        $con['is_show'] = 1;
        $con['book_id'] = array('not in','78,61');
        //日
        $arr = $list->where($con)->field('book_id,book_name,upload_img')->order('buy_total DESC')->limit(10)->select();
        $this->assign('dingyue', $arr);
    }

    //点击
    public function dianji() {
        //模型
        $list = D('RankinglistView');
        $con['is_show'] = 1;
        //日
        $con['book_id'] = array('not in','78,61');
        $arr = $list->where($con)->field('book_id,book_name,upload_img')->order('click_total DESC')->limit(10)->select();
        $this->assign('dianji', $arr);
    }

    //作品打赏ok
    public function exceptional() {
        //模型
        $list = D('RankinglistView');
        $con['is_show'] = 1;
        $con['book_id'] = array('not in','78,61');
        //日
        $arr = $list->where($con)->field('book_id,book_name,upload_img')->order('exceptional_total DESC')->limit(10)->select();
        $this->assign('exceptional', $arr);
    }

    //普通票
    public function vote() {
        //模型
        $list = D('RankinglistView');
        $con['is_show'] = 1;
        $con['book_id'] = array('not in','78,61');
        //日
        $arr = $list->where($con)->field('book_id,book_name,upload_img')->order('vote_total DESC')->limit(10)->select();
        $this->assign('vote', $arr);
    }

    //收藏
    public function collection() {
        //模型
        $list = D('RankinglistView');
        $con['is_show'] = 1;
        $con['book_id'] = array('not in','78,61');
        //日
        $arr = $list->where($con)->field('book_id,book_name,upload_img')->order('collection_total DESC')->limit(10)->select();
        $this->assign('collection', $arr);
    }

}
