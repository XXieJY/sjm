<?php
namespace Home\Controller;
use Think\Controller;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/8
 * Time: 19:41
 */
class MybookController extends Controller{

    public function index(){
        if ($_COOKIE[user_id]) {
            //书架
            $collection = D('CollectionView');
            $user_id = $_COOKIE[user_id];
            $count = $collection->where(array('user_id' => $user_id))->count(); // 查询满足要求的总记录数
            $Page = new \Think\Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数

            $books = $collection->where(array('user_id' => $user_id))->order('time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $this->assign('books', $books);


            $this->assign('count',$count);

        }
        $this->display();
    }

    function more(){
        $page =$_POST['page'];
        $number =5;//每页条数

        $page =($page-1)*$number;

        $collection = D('CollectionView');
        $user_id = $_COOKIE[user_id];
        $books = $collection->where(array('user_id' => $user_id))->order('time desc')->limit($page,$number)->select();
      if($books){

          $this->assign('books', $books);

          $this->display();
      }
      else{
          echo 1;
      }



    }


    //删除收藏
    public function del($bookid) {
        $collection = M('BookCollection');
        $is = $collection->where(array('book_id' => $bookid, 'user_id' => $_COOKIE[user_id]))->find();
        if ($is) {
            $isok = $collection->where(array('id' => $is[id]))->delete();
            if ($isok) {
                $datas['collection_day'] = array('exp', "collection_day-1");
                $datas['collection_weeks'] = array('exp', "collection_weeks-1");
                $datas['collection_month'] = array('exp', "collection_month-1");
                $datas['collection_total'] = array('exp', "collection_total-1");
                M('BookStatistical')->where(array('book_id' => $bookid))->save($datas);
                S('WapBooks' . $_POST[bookid], NULL); //删除缓存
                header("Location: /Home/Mybook/index.html");
            } else {
                $this->error("系统错误");
            }
        } else {
            $this->error("系统错误");
        }
    }

}