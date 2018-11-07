<?php
namespace Home\Model;
use Think\Model\ViewModel;
   //书籍消费关联表
    class XiaofeiViewModel extends ViewModel {

        public $viewFields = array(
            'UserConsumerecord' => array('user_id','money','dosomething','date'),
            'User' => array('pen_name', '_on' => 'UserConsumerecord.user_id=User.user_id'),
        );

    }