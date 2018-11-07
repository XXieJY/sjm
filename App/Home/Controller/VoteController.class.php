<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;
//VIP票
class VoteController extends Controller {

    //普通票
    public function vote() {

        $user = M('User');
        $isu = $user->where(array('user_id' => cookie('user_id')))->field('vote')->find();
        if (is_array($isu) && $isu[vote] > 0) {
            //处理封粉丝
            A('Fans')->index($_POST['bookid'], 1);
            //排行榜
            $bookexceptional = M('BookStatistical');
            $data['vote_day'] = array('exp', "vote_day+1");
            $data['vote_weeks'] = array('exp', "vote_weeks+1");
            $data['vote_month'] = array('exp', "vote_month+1");
            $data['vote_total'] = array('exp', "vote_total+1");
            $bookexceptional->where(array('book_id' => $_POST['bookid']))->save($data);
            //扣用户表
            $user->where(array('user_id' => cookie('user_id')))->save(array('vote' => array('exp', "vote-1")));
            //增加投票记录
            $shuju[book_id] = $_POST['bookid'];
            $shuju[user_id] = cookie('user_id');
            $shuju[type] = 1;
            $shuju[pen_name] = cookie('pen_name');
            $shuju[number] = 1;
            $shuju[time] = date('Y-m-d H:i:s', time());
            M('BookVote')->add($shuju);
            S('WapBooks' . $_POST['bookid'], NULL); //删除缓存
            echo $isu['vote'] - 1;
        } else {
            echo 999;
        }
    }

    //VIP票
    public function vipvote() {

        $user = M('User');
        $isu = $user->where(array('user_id' => cookie('user_id')))->field('vipvote')->find();
        if (is_array($isu) && $isu[vipvote] > 0) {
            //处理封粉丝
            A('Fans')->index($_POST['bookid'], 10);
            //排行榜
            $bookexceptional = M('BookStatistical');
            $data['vipvote_day'] = array('exp', "vipvote_day+1");
            $data['vipvote_weeks'] = array('exp', "vipvote_weeks+1");
            $data['vipvote_month'] = array('exp', "vipvote_month+1");
            $data['vipvote_total'] = array('exp', "vipvote_total+1");
            $bookexceptional->where(array('book_id' => $_POST['bookid']))->save($data);
            //扣用户表
            $user->where(array('user_id' => cookie('user_id')))->save(array('vipvote' => array('exp', "vipvote-1")));
            //增加投票记录
            $shuju[book_id] = $_POST['bookid'];
            $shuju[user_id] = cookie('user_id');
            $shuju[type] = 2;
            $shuju[pen_name] = cookie('pen_name');
            $shuju[number] = 1;
            $shuju[time] = date('Y-m-d H:i:s', time());
            M('BookVote')->add($shuju);
            S('WapBooks' . $_POST['bookid'], NULL); //删除缓存
            echo $isu['vipvote'] - 1;
        } else {
            echo 999;
        }
    }

}
