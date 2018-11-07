<?php
namespace Home\Controller;
use Think\Controller;
use Think\Page;
use Think\Think;

class BooksController extends Controller{

    public function _empty() {
        //该方法即为空操作
        echo '页面不存在';
    }
    public function index($bookid){


        //缓存是否存在
        $bookfile = S('WapBooks' . $bookid);
        if (!$bookfile) {
            $Databook = $this->books($bookid);
            $Databook['rewardrecord'] = $this->exceptional($bookid);
            $Databook['newbook'] = $this->newbook();
            $Databook['count']=$this->count($bookid);
            S('WapBooks' . $bookid, $Databook, 3600); //生成数据缓存
            $this->assign('books', $Databook);

        } else {
            $this->assign('books', $bookfile);
        }

        //用户最后阅读记录
        if(cookie('user_id')){
            $user_id = cookie('user_id');
            $buy_m = M('BookBuy');
            $chapter_info = $buy_m->field('book_id,chapter_id')->where('user_id='.$user_id.' and book_id='.$bookid)->find();
            $chapter_id = substr($chapter_info['chapter_id'],(strrpos($chapter_info['chapter_id'],',')+1));
            if($chapter_id){
                $content_m = M('Content');
                $content_info = $content_m->field('book_id,num')->where('content_id='.$chapter_id.' and book_id='.$bookid)->find();
                if($content_info){
                    $this->assign('content_info', $content_info);
                }
            }
        }

        $paixu="desc";
        $this->dirlist($bookid,$paixu);

        cookie('paixu',$paixu,time() + 2 * 7 * 24 * 3600);//将排序存在cookie中




        $this->comment($bookid);

        $this->collection($bookid);
        $this->statistical($bookid);

        $this->display();
    }


    //书籍信息
    protected function books($bookid) {
        if($_GET['key']=='xgbest'){
            $where['book_id'] = $bookid;
        }else{
            $where['book_id'] = $bookid;
            $where['is_show'] = 1;
        }
        //书籍信息
        $books = M('book')->where($where)->field('book_id,book_name,author_name,type_id,upload_img,state,is_show,chapter,book_brief,words')->find();
        if (!is_array($books)) {
            $this->error("没有找到该书");
            exit();
        }
        //修改作品简介样式
        $books['book_brief'] = str_replace("\n", "</p><p>", str_replace(" ", "", $books[book_brief]));
        //书籍附表
        $bookinfo = M('BookStatistical')->where($where)->field('click_total,collection_total,exceptional_total,vote_total,vipvote_total')->find();
        $books['bookinfo'] = $bookinfo;
        //最新更新
        $newcontnet = M('Content')->where(array('type'=>0,'book_id' => $bookid, 'num' => $books[chapter]))->field('content_id,title,num,time')->find();
        $books['newcontnet'] = $newcontnet;
        return $books;
    }

    //打赏总数
    protected function count($bookid){
        $book_exception =M('BookExceptional');
        $con['book_id']=$bookid;
        $count=$book_exception->where($con)->count();

        return $count;

    }

    //打赏
    protected function exceptional($bookid) {
        $book_exceptional = M('BookExceptional');
        $con['book_id'] = $bookid;
        $rewardrecord = $book_exceptional->where($con)->field('pen_name,num,gift')->limit(5)->order('time desc')->select();
        return $rewardrecord;
    }

    //新书推荐
    protected function newbook() {
        $bookpromote = M('BookPromote');
        $newbook = $bookpromote->where(array('promote_id' => 7))->field('book_id,book_name,upload_img')->limit(4)->order('xu asc')->select();
        return $newbook;
    }

    //判断用户是否收藏
    protected function collection($bookid) {
        if ($_COOKIE[user_id]) {
            $where['book_id'] = $bookid;
            $where['user_id'] = $_COOKIE[user_id];
            $isok = M('BookCollection')->where($where)->field('id')->find();
            if (is_array($isok)) {
                $this->assign('collection', 2);
            } else {
                $this->assign('collection', 1);
            }
        } else {
            $this->assign('collection', 1);
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

    //目录
    public function dirlist($bookid,$paixu ,$current=1){

        $book = D('Book');
        $content = M('Content');
        $info = $book->where(array('book_id' => $bookid))->field('book_id,book_name')->find();
        $map['book_id'] = $bookid;
        $map['type'] = 0;
        $map['status']=0;

        //分页变量
        $pageSize = 10;//每页显示的记录数
        $totalRow = 0;//总记录数
        $totalPage = 0;//总页数
        $start = ($current-1)*$pageSize;//每页记录的起始值

        $totalRow = $content->where($map)->field('book_id')->count(); // 查询满足要求的总记录数

        $totalPage =ceil($totalRow/$pageSize);

        $content_arr = $content->where($map)->field('content_id,title,num,the_price')->limit($start,$pageSize)->order("num $paixu")->select();


        $this->assign('chapterlist', $content_arr);
        $this->assign('totalPage', $totalPage);
        $this->assign('current', $current);

        $this->assign('bookinfo', $info);


        return $content_arr;

    }
    //查看评论
    public function comment($bookId){
        $mesg = D('MessageView');
        $where['z_id'] = 0;
        $where['book_id'] = $bookId;

        $count=$mesg->where($where)->count();//查询总记录数
        $page =new \Think\Page($count,5);//传入总记录数和每页显示的个数

        //内容显示
        $showArr = $mesg->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('top desc,time desc')->select();
        $shuliang = count($showArr);
        for ($i = 0; $i < $shuliang; $i++) {
            $zmesg = $mesg->where(array('z_id' => $showArr[$i]['f_id'], 'book_id' => $bookId))->order('time asc')->select();
            $showArr[$i]['zmesg'] = $zmesg;
        }

        $this->assign("count",$count);
        $this->assign("showArr",$showArr);



    }



}