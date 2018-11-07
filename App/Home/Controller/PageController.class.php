<?php
namespace Home\Controller;
use Think\Controller;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10
 * Time: 20:28
 */
class PageController extends Controller{

    public function page(){

        $paixu=$_POST['paixu'];

        $bookid =$_POST['bookid'];
        if($_POST['page']=="next"){
            $current =$_POST['current']+1;
        }
        if($_POST['page']=="pre"){
            $current =$_POST['current']-1;
        }

        if($_POST['page']=="jump"){
            $current =$_POST['current'];
        }

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
        if($current>$totalPage){
            $current =$totalPage;
            $pageSize = 10;//每页显示的记录数
            $totalRow = 0;//总记录数
            $totalPage = 0;//总页数
            $start = ($current-1)*$pageSize;//每页记录的起始值
            $totalRow = $content->where($map)->field('book_id')->count(); // 查询满足要求的总记录数
            $totalPage =ceil($totalRow/$pageSize);
        }
        $content_arr = $content->where($map)->field('content_id,title,num,the_price')->limit($start,$pageSize)->order("num $paixu")->select();

        echo "<div id=\"div\">";
        echo "<ul class=\"content\">";
        foreach ($content_arr as $v){

            echo "<li>";
            if($v[the_price]!=0){
                echo "<img src=\"/Public/Wap/images/vip.png\"/>";
            }

            echo "<a href=\"/chapter//$info[book_id]/$v[num]\">$v[title]</a></li>";

        }

        echo "</ul>";


        echo"<div class=\"pager\">";

        if($current==1){
            echo "<a class=\"pager-item\" href=\"javascript:fenye();\">下一页</a>";

        }else if($current<$totalPage){
            echo "<a class=\"pager-item\" href=\"javascript:fenyee();\">上一页</a>
        <a class=\"pager-item\" href=\"javascript:fenye();\">下一页</a>";
        }else{
            echo "<a class=\"pager-item\" href=\"javascript:fenyee();\">上一页</a>";
        }
        echo "<div class=\"pager-item\">到</div>
                        <div class=\"pager-item page-num-input\"><input type=\"text\" id=\"jump\" value=\"\"/></div>
                        <div class=\"pager-item\">页</div>
                        <a href=\"javascript:jump();\"><div class=\"pager-item goto\">跳转</div></a>
                        <div class=\"pager-item\">$current/$totalPage</div>
                        <input type=\"hidden\" id=\"current\" value=\"$current\">
                        <input type=\"hidden\" id=\"paixu\" value=\"$paixu\">
                    </div>";
        echo "</div>";
    }
}