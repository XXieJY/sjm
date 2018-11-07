<?php
namespace Home\Controller;
use Think\Controller;
    //粉丝类
    class FansController extends Controller{

        //书籍ID 增加的粉丝的书量
        public function index($book, $num) {
            //粉丝职
            $fans = M('BookFans');
            $con[book_id] = $book;
            $con[user_id] = cookie('user_id');
            $fansvalue = $fans->where($con)->field('fan_value')->find();
            //查看粉丝表在不在
            if (!is_array($fansvalue)) {
                $data['book_id'] = $book;
                $data['user_id'] = cookie('user_id');
                $data['fan_value'] = $num;
                $data['fans_level'] = $this->level($num);
                $data['time'] = date('Y-m-d H:i:s', time());
                $fans->add($data);
            } else {
                $data['fan_value'] = array('exp', "fan_value+$num");
                $data['fans_level'] = $this->level($fansvalue[fan_value] + $num);
                $fans->where($con)->save($data);
            }
        }

        //获取等级
        public function level($fas) {
            //更新等级
            $level['type'] = 2;
            $level['score'] = array('ELT', $fas);
            $les = M('UserLevel')->where($level)->field('level')->order('id DESC')->find();
            return $les[level];
        }

    }
    