<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;

class BookController extends Controller {

    public function _empty() {
        //该方法即为空操作
        echo '当前操作不存在';
    }

    public function index(){

        $this->typeList();
        $con = array();
        $gender = $_GET['gender']; //男女频分类
        $type = $_GET['type']; //小说类型
        $state = $_GET['state']; //状态
        $signed = $_GET['signed']; //签约否
        $click = $_GET['click']; //点击排序
        $class = $_GET['class']; //分类
        $vip = $_GET['vip']; //是否vip
        //男女频分类
        if ($gender == 2 || $gender == 1) {
            if ($gender == 1) {
                $con['gender'] = 1;
                $this->assign('gender0', 'activite');
            } else {
                $con['gender'] = 2;
                $this->assign('gender1', 'activite');
            }
        } elseif ($gender == 3) {
            $this->assign('gender', 'activite');
        }
        //小说类型
        if ($type != '' && $type != 50) {
            $con['Book.type_id'] = $type;
        } elseif ($type == 50) {
            $this->assign('type', 'activite');
        }
        //状态
        if ($state == 2 || $state == 1) {
            if ($state == 1) {
                $con['state'] = 1;
                $this->assign('state1', 'activite');
            } else {
                $con['state'] = 2;
                $this->assign('state2', 'activite');
            }
        } elseif ($state == 3) {
            $this->assign('state', 'activite');
        }
        //是否vip
        if ($vip == 2 || $vip == 1) {
            if ($vip == '2') {
                $con['vip'] = 0;
                $this->assign('vip1', 'activite');
            } else {
                $con['vip'] = 3;
                $this->assign('vip0', 'activite');
            }
        } elseif ($vip == 3) {
            $this->assign('vip', 'activite');
        }
        //点击榜
        switch ($click) {
            case 0:$click = 'time desc';
                $this->assign('cl0', 'activite');
                break; //正常
            case 1:$click = 'click_total desc';
                $this->assign('cl1', 'activite');
                break; //总榜
            case 2:$click = 'click_month desc';
                $this->assign('cl2', 'activite');
                break; //月榜
            case 3:$click = 'click_weeks desc';
                $this->assign('cl3', 'activite');
                break; //周榜
            case 4:$click = 'click_day desc';
                $this->assign('cl4', 'activite');
                break; //日榜
        }
        $this->assign('click', 'current');


        $this->base($con, $click);

        $this->display();

    }
    //获得小说分类
    public function typeList(){

        $typeArr =M("BookType")->select();

        $this->assign("typeList",$typeArr);

    }

//获取基本信息以及分页类
    public function base($con, $order) {

        $book = D('ListView');
        $con['is_show'] = 1;
       
        $count = $book->where($con)->field('book_id')->count(); // 查询满足要求的总记录数

        $Page = new \Think\Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $m = $book->where($con)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $Page->setConfig('theme', "<div class=\"pager\"><div class=\"pager-item\">%UP_PAGE%&nbsp;&nbsp;%DOWN_PAGE%</div>
                              <div class=\"pager-item\">到</div>
                              <div class=\"pager-item page-num-input\">
                                <input type=\"text\" id=\"pageText\"/>
                              </div>
                              <div class=\"pager-item\">页</div>
                              <div class=\"pager-item goto\" onClick=\"javascript:window.location.href='/Home/Book/index/p/'+document.getElementById('pageText').value;\">跳转</div>
                              <div class=\"pager-item\">%NOW_PAGE%/%TOTAL_PAGE%</div>
                            </div>");
        $show = $Page->show(); // 分页显示输出
        $this->assign('list', $m);
        $this->assign('page', $show);



    }


}