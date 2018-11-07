<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/6
 * Time: 10:56
 */
class CategoriesController extends Controller{

    public function index($bookid,$paixu="desc"){

        $this->paixu($bookid,$paixu);

        if($paixu=="asc"){
            $this->assign('xuxie', 1);
        }else{
            $this->assign('xuxie', 2);
        }

        $this->display();
    }

   public function paixu($bookid,$paixu){

       $book = D('Book');
       $content = M('Content');
       $info = $book->where(array('book_id' => $bookid))->field('book_id,book_name')->find();
       $map['book_id'] = $bookid;
       $map['type'] = 0;
       $map['status']=0;

       $count = $content->where($map)->field('book_id')->count(); // 查询满足要求的总记录数
       $page =new \Think\Page($count,10);

       $content_arr = $content->where($map)->field('content_id,title,num,the_price')->limit($page->firstRow.','.$page->listRows)->order("num $paixu")->select();

       $page->setConfig('theme', "<div class=\"pager\"><div class=\"pager-item\">%UP_PAGE%&nbsp;&nbsp;%DOWN_PAGE%</div>
                              <div class=\"pager-item\">到</div>
                              <div class=\"pager-item page-num-input\">
                                <input type=\"text\" id=\"pageText\"/>
                              </div>
                              <div class=\"pager-item\">页</div>
                              <div class=\"pager-item goto\" onClick=\"javascript:window.location.href='/Categories/".$bookid."/p/'+document.getElementById('pageText').value;\">跳转</div>
                              <div class=\"pager-item\">%NOW_PAGE%/%TOTAL_PAGE%</div>
                            </div>");
       $show = $page->show(); // 分页显示输出

       $this->assign('chapterlist', $content_arr);

       $this->assign('page',$show);
       $this->assign('bookinfo', $info);


   }



}