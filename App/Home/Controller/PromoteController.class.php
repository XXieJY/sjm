<?php
namespace Home\Controller;
use Think\Controller;
    //推广
    class PromoteController extends Controller {

        public function index() {
            $this->zhangjie();
            $this->display("/Cheapter_promote");
        }

        //好书推荐
        public function zhangjie() {
            $bookpromote = M('BookPromote');
            $arr = $bookpromote->where(array('promote_id' => 9))->field('book_id,book_name,upload_img')->limit(8)->order('xu asc')->select();
            $this->assign('meiyou', $arr);
        }

    }
    