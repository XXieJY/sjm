<?php
namespace Home\Controller;
use Think\Controller;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/10
 * Time: 15:19
 */
class MessageController extends Controller{

    public function index($id){

        if (!cookie('user_id')) {
            header("Location:/Home/Login/index.html");
            exit();
        }
        $mesg = M('BookMessage');
        $mesfes = $mesg->where(array('f_id' => $id))->find();
        $bookid=$mesfes['book_id'];
        $book =M('Book');
        $bookInfo =$book->where("book_id ={$bookid}")->field('book_name')->find();


        $this->assign('mesfes', $mesfes);
        $this->assign('book',$bookInfo);

        $this->display();
    }

    public function replay(){

        $content=$_POST['content'];
        $id=$_POST['id'];
        $bookid=$_POST['bookid'];
            $mesg = M('BookMessage');
            $data['z_id'] = $id;
            $data['book_id'] = $_POST['bookid'];
            $data['user_id'] = $_COOKIE['user_id'];
            $data['title'] = cookie('pen_name');
            $data['content'] = $content;
            $data['time'] = date("Y-m-d H:i:s");
            //父跟新
            $datas['num'] = array('exp', 'num+1');
            $mesg->where(array('f_id' => $data['z_id']))->save($datas);
            $isok = $mesg->add($data);

            if ($isok) {
                header("Location:/books/$bookid.html");
            } else {
                $this->error("系统错误");
            }
    }

    //追加评论
    public function message(){

        $bookid =$_POST['bookid'];
        $page =$_POST['page'];

        $mesg = D('MessageView');
        $where['z_id'] = 0;
        $where['book_id'] = $bookid;

        $count = $mesg->where($where)->count(); // 查询满足要求的总记录数
        $replay =new \Think\Page($count,5);//传入总记录数和每页显示的个数

        $number = 5; //每页条数
        $page = ($page - 1) * $number;
        //内容显示
        $message = $mesg->where($where)->limit($page . ',' . $number)->order('top desc,time desc')->select();
        $shuliang = count($message);
        for ($i = 0; $i < $shuliang; $i++) {
            $zmesg = $mesg->where(array('z_id' => $message[$i]['f_id'], 'book_id' => $bookid))->limit($replay->firstRow . ',' . $replay->listRows)->order('time asc')->select();
            $message[$i]['zmesg'] = $zmesg;
        }
        if($shuliang){

            $this->assign('showArr',$message);

            $this->display();


        }  else {
            echo 1;
        }
    }
    //发表评论
    public function publish(){


        $title=$_POST['title'];
        $content=$_POST['content'];
        $bookid =$_POST['bookid'];
        if($title==""){
            echo "标题不可以为空";
            exit();
        }
        if($content==""){
            echo "内容不可以为空";
            exit();
        }
        if($title!="" && $content!=""){

            $mesg = M('BookMessage');
            $data['z_id'] = 0;
            $data['book_id'] = $bookid;
            $data['user_id'] = cookie('user_id');
            $data['title'] = $title;
            $data['content'] = $content;
            $data['time'] = date("Y-m-d H:i:s");

            $isok = $mesg->add($data);
            if($isok) {

                //单独封装一个单独的div页面
                $showArr = $this->reload($bookid);
                foreach ($showArr as $v) {
                    echo " <div class=\"mod-content\">";
                    echo "<div class=\"item-comment\">
                      <div class=\"item-comment-title\">";

                    if ($v[top] == 1) {
                        echo "<span class=\"tag-stick\">[置顶]</span>";
                    }
                    if ($v[good] == 1) {
                        echo "<span class=\"tag-stick\">[精华]</span>";
                    }
                    echo "<span>{$v['title']}</span>
                            </div>
                            <div class=\"item-comment-content\">
                                {$v['content']}
                            </div>
                            <div class=\"item-comment-bottom\">
                                <span class=\"comment-user-name\">{$v['pen_name']}</span>
                                <span class=\"comment-times\">{$v['time']}</span>
                                <a href=\"/Home/Message/index/id/$v[f_id].html\"><span class=\"button-comment-reply\"><i class=\"icon-comment\"></i>回复</span></a>
                            </div>";

                    echo "<div class=\"comment-reply\">
                                <ul>";

                    foreach ($v[zmesg] as $vv) {

                        echo "<li class=\"item-reply\">
                                    <span class=\"reply-user-name\">
                                       {$vv[pen_name]}
                                    </span>
                                    <span class=\"reply-txt\">
                                    
                                     {$vv[content]}
                                    </span>
                                    </li>";
                    }


                    echo "</ul>";

                    if (count($v[zmesg])>3) {

                        echo "<div class=\"load-more-button\">查看更多回复</div>";
                    }


                    echo "</div>
                        </div>";
                    echo "</div>";
                }

            }

        }

    }

    //封装一个方法重新加载评论内容
    public function reload($bookId){
        $mesg = D('MessageView');
        $where['z_id'] = 0;
        $where['book_id'] = $bookId;
        $count=$mesg->where($where)->count();//查询总记录数
        $page =new \Think\Page($count,5);//传入总记录数和每页显示的个数

        //内容显示
        $showArr = $mesg->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('top desc,time desc')->select();
        $shuliang = count($showArr);
        for ($i = 0; $i < $shuliang; $i++) {
            $zmesg = $mesg->where(array('z_id' => $showArr[$i]['f_id'], 'book_id' => $bookId))->limit(10)->order('time asc')->select();
            $showArr[$i]['zmesg'] = $zmesg;
        }
        return $showArr;

    }
    //追加评论回复
    function more(){
        $bookid =$_POST['bookid'];
        $zid=$_POST['zid'];

        $page =$_POST['page'];
        $number =5;//每页条数

        $page =($page-1)*$number;

        $mesg = D('MessageView');

        $zmesg = $mesg->where(array('z_id' => $zid, 'book_id' => $bookid))->limit($page . ',' . $number)->order('time asc')->select();


        if($zmesg){

            $this->assign('showArr',$zmesg);

            $this->display();


        }  else {
            echo 1;
        }
    }
}