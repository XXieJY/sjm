<?php
namespace Home\Controller;
use Think\Controller;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10
 * Time: 22:11
 */
class PaixuController extends Controller{

   public function paixu(){

        $bookid=$_POST['bookid'];
        $paixu =$_POST['paixu'];

        if($paixu=="desc"){
            cookie('paixu',null);
            cookie('paixu','desc',time() + 2 * 7 * 24 * 3600);
        }
        if($paixu=="asc"){
            cookie('paixu',null);
            cookie('paixu','asc',time() + 2 * 7 * 24 * 3600);

        }


       $content = M('Content');
       $map['book_id'] = $bookid;
       $map['type'] = 0;



        $listArr =  $this->dirlist($bookid,$paixu);

        $current=1;
       $totalrow = $content->where($map)->field('book_id')->count(); // 查询满足要求的总记录数

       $totalPage =ceil($totalrow/10);

        echo "<ul class=\"content\">";
        foreach ($listArr as $v){

            echo "<li>";
            if($v[the_price]!=0){
                echo "<img src=\"/Public/Wap/images/vip.png\"/>";
            }

            echo "<a href=\"/chapter/$bookid/$v[num]\">$v[title]</a></li>";

        }

        echo "</ul>";

       echo"<div class=\"pager\">";


           echo "<a class=\"pager-item\" href=\"javascript:fenye();\">下一页</a>";

       echo "<div class=\"pager-item\">到</div>
                        <div class=\"pager-item page-num-input\"><input type=\"text\" id=\"jump\" value=\"\"/></div>
                        <div class=\"pager-item\">页</div>
                        <a href=\"javascript:jump();\"><div class=\"pager-item goto\">跳转</div></a>
                        <div class=\"pager-item\">$current/$totalPage</div>
                        <input type=\"hidden\" id=\"current\" value=\"$current\">
                        <input type=\"hidden\" id=\"paixu\" value=\"$paixu\">
                    </div>";


    }

    //目录
    public function dirlist($bookid,$paixu ,$current=1){

        $book = D('Book');
        $content = M('Content');
        $info = $book->where(array('book_id' => $bookid))->field('book_id,book_name')->find();
        $map['book_id'] = $bookid;
        $map['type'] = 0;

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
}