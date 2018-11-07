<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;
//推荐章节
class ContentController extends Controller {

    public function index($bookid, $num,$id) {
        $weiChat=M('Weichat')->where(['id'=>$id])->find();
        $this->assign('weiChat',$weiChat);
        //寻找书籍
        $books = M('Book')->where(array('book_id' => $bookid))->field('book_name,book_id,chapter')->find();
        if (!is_array($books)) {
            $this->error("没有该章节");
            exit();
        }
        $this->assign('books', $books);
        //内容信息
        $neirong = M('Content');
        $content = $neirong->where(array('book_id' => $bookid, 'num' => $num))->field('content_id,book_id,num,title')->find();
        //显示内容
        $neirong->where(array('content_id' => $content[content_id]))->save(array('clicknum' => array('exp', "clicknum+1")));
        if ($num >= 10) {
            $this->assign('content', $content);
            $this->assign('weiChat',$weiChat);
            $this->display('promote');
           
            exit();
        }
        if (is_array($content)) {
            //章节信息
            $cache = A('Cache');
            $cache->chushi("/Upfile/t$bookid", $content[content_id]);
            $cache->read_cache(); //读取缓存 
            $contents = M('Contents')->where(array('content_id' => $content[content_id]))->find();
            //组合内容
            $content[content] = $contents[content];
            $content[content] = str_replace("\n", "</p><p>", str_replace(" ", "", $content[content]));
            $chapterinfo['preid'] = $num <= 1 ? '1' : $num - 1;
            $chapterinfo['nextid'] = $num <= $books['chapter'] ? $num + 1 : '';
            $this->assign('chapterinfo', $chapterinfo);
            $this->assign('content', $content);
            $this->display();
            $cache->create_cache(); //生成缓存
        }
    }

}
