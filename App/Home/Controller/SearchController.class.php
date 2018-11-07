<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/4
 * Time: 16:08
 */
class SearchController extends Controller{

    public function index($current,$keyword){


        if($keyword==""){

           header("location:/Home/Searchset/index");
            exit();
        }
        //分页变量
        $pageSize = 10;//每页显示的记录数
        $totalRow = 0;//总记录数
        $totalPage = 0;//总页数
        $start = ($current-1)*$pageSize;//每页记录的起始值


        $con['book_name'] = array('like', "%$keyword%");
        $book = D('ListView');
        $con['is_show'] = 1;
        $totalRow =$book->where($con)->count();
        $totalPage =ceil($totalRow/$pageSize);
        if($totalPage==0){

            header("location:/Home/Searchset/index");
            exit();
        }



        $list = $book->where($con)->limit($start,$pageSize)->select();
        $this->assign("count",$totalRow);
        $this->assign('current',$current);
        $this->assign('totalPage',$totalPage);
        $this->assign('list', $list);
        $this->assign('keyword',$keyword);

        $this->display();


    }

    public function search($current=1,$keyword){

        $searchid =$_POST['searchby'];

        if($searchid==1){
            //按书籍找

            if($keyword==""){

                header("location:/Home/Searchset/index");
                exit();
            }
            //分页变量
            $pageSize = 10;//每页显示的记录数
            $totalRow = 0;//总记录数
            $totalPage = 0;//总页数
            $start = ($current-1)*$pageSize;//每页记录的起始值


            $con['book_name'] = array('like', "%$keyword%");
            $book = D('ListView');
            $con['is_show'] = 1;
            $totalRow =$book->where($con)->count();
            $totalPage =ceil($totalRow/$pageSize);
            if($totalPage==0){

                header("location:/Home/Searchset/index");
                exit();
            }



            $list = $book->where($con)->limit($start,$pageSize)->select();
            $this->assign("count",$totalRow);
            $this->assign('current',$current);
            $this->assign('totalPage',$totalPage);
            $this->assign('list', $list);
            $this->assign('keyword',$keyword);

            $this->display("index");

        }
        if($searchid==2){

            //按作者找
            if($keyword==""){

                header("location:/Home/Searchset/index");
                exit();
            }
            //分页变量
            $pageSize = 10;//每页显示的记录数
            $totalRow = 0;//总记录数
            $totalPage = 0;//总页数
            $start = ($current-1)*$pageSize;//每页记录的起始值


            $con['author_name'] = array('like', "%$keyword%");
            $book = D('ListView');
            $con['is_show'] = 1;
            $totalRow =$book->where($con)->count();
            $totalPage =ceil($totalRow/$pageSize);
            if($totalPage==0){

                header("location:/Home/Searchset/index");
                exit();
            }



            $list = $book->where($con)->limit($start,$pageSize)->select();
            $this->assign("count",$totalRow);
            $this->assign('current',$current);
            $this->assign('totalPage',$totalPage);
            $this->assign('list', $list);
            $this->assign('keyword',$keyword);

            $this->display("index");

        }



    }




    //按书籍查找
    function page(){


        $keyword =$_POST['keyword'];
        $page =$_POST['page'];
        if($page=="next"){
            $current =$_POST['current']+1;

        }
        if($page=="pre"){
            $current =$_POST['current']-1;

        }

        if($page=="jump"){

            $current =$_POST['current'];
        }


        $pageSize = 10;//每页显示的记录数
        $totalRow = 0;//总记录数
        $totalPage = 0;//总页数
        $start = ($current-1)*$pageSize;//每页记录的起始值
        $con['book_name'] = array('like', "%$keyword%");
        $book = D('ListView');
        $con['is_show'] = 1;
        $totalRow =$book->where($con)->count();
        $totalPage =ceil($totalRow/$pageSize);
        if($current>$totalPage){
            $current =$totalPage;
            $pageSize = 10;//每页显示的记录数
            $totalRow = 0;//总记录数
            $totalPage = 0;//总页数
            $start = ($current-1)*$pageSize;//每页记录的起始值
            $con['book_name'] = array('like', "%$keyword%");
            $book = D('ListView');
            $con['is_show'] = 1;
            $totalRow =$book->where($con)->count();
            $totalPage =ceil($totalRow/$pageSize);

        }

        $list = $book->where($con)->limit($start,$pageSize)->select();
        echo "<div class=\"wrap-content\">
    <div class=\"wrap-content-title\">
        <div>共有 $totalRow 条记录</div>
    </div>";

      echo "<ul class=\"result-set\">";

      foreach ($list as $vo){
          echo "<li class=\"result-set-item\">
                <div class=\"result-set-item-top\">
                    <a href='/books/$vo[book_id].html'><img src=\"/Upload/Book/zhong/$vo[upload_img]\" class=\"searchset-img\"/></a>
                    <a href='/books/$vo[book_id].html'><dl>
                        <dd class=\"result-set-title\">$vo[book_name]</dd>
                        <dd class=\"result-set-auther\">作者：<span>$vo[author_name]</span></dd>
                        <dd class=\"result-set-date\">最后更新时间：$vo[time]</dd>
                    </dl></a>
                </div>
                <div class=\"result-set-item-content\">
                    $vo[book_brief]...
                </div>
            </li>";
      }
    echo "</ul>";
      echo "</div>";

      echo "<div class=\"pager\">";

   if($current==1){
       echo "<a class=\"pager-item\" href=\"javascript:pages();\">下一页</a>";

   }else if($current<$totalPage){
       echo "<a class=\"pager-item\" href=\"javascript:pagess();\">上一页</a>
        <a class=\"pager-item\" href=\"javascript:pages();\">下一页</a>";
   }else{
       echo "<a class=\"pager-item\" href=\"javascript:pagess();\">上一页</a>";
   }
    echo "<div class=\"pager-item\">到</div>
    <div class=\"pager-item page-num-input\"><input type=\"text\" id=\"jump\" value=\"\"/></div>
    <div class=\"pager-item\">页</div>
    <a href=\"javascript:jump();\"><div class=\"pager-item goto\" style=\"margin-left: 2px;\">跳转</div></a>
    <div class=\"pager-item\">$current/$totalPage</div>
    <input type=\"hidden\" id=\"current\" value=\"$current\">
    <input type=\"hidden\" id=\"keyword\" value=\"$keyword\">
</div>";



    }



}