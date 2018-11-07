<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;
//销售统计
class SalesController extends Controller{

    public function index($book_id, $cp_id, $book_name, $consumption, $vip_num) {
        //准备书序
        $cpmoday = M('CpMoneyday');
        //查询条件
        $where['book_id'] = $book_id;
        $where['time'] = date('Y-m-d', time());
        //更新数据
        $save['consumption'] = array('exp', "consumption+$consumption");
        $save['vip_num'] = array('exp', "vip_num+$vip_num");
        $is = $cpmoday->where($where)->save($save);
        if (!$is) {
            $data['book_id'] = $book_id;
            $data['cp_id'] = $cp_id;
            $data['time'] = $where['time'];
            $data['book_name'] = $book_name;
            $data['consumption'] = $consumption;
            $data['vip_num'] = $vip_num;
            $cpmoday->add($data);
        }
    }

}
