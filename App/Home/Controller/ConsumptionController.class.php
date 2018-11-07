<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;
//消费记录
    class ConsumptionController extends GlobalController {

        public function index() {
            $cont = M('UserConsumerecord');
            $count = $cont->where(array('user_id' => $_COOKIE[user_id]))->count(); // 查询满足要求的总记录数   
            $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('theme', "<div class=\"pager\"><div class=\"pager-item\">%UP_PAGE%%DOWN_PAGE%</div>
                              <div class=\"pager-item\">到</div>
                              <div class=\"pager-item page-num-input\">
                                <input type=\"text\" id=\"pageText\"/>
                              </div>
                              <div class=\"pager-item\">页</div>
                              <div class=\"pager-item goto\" onClick=\"javascript:window.location.href='/Home/Consumption/index/p/'+document.getElementById('pageText').value;\">跳转</div>
                              <div class=\"pager-item\">%NOW_PAGE%/%TOTAL_PAGE%</div>
                            </div>");
            //内容显示
            $xiaofei = $cont->where(array('user_id' => $_COOKIE[user_id]))->limit($Page->firstRow . ',' . $Page->listRows)->order('date desc')->select();
            $this->assign('xiaofei', $xiaofei);
            // 分页显示输出
            $show = $Page->show();
            $this->assign('page', $show);
            $this->display();
        }

    }
    