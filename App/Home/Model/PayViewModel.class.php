<?php
namespace Home\Model;
use Think\Model\ViewModel;
    //充值查看
    class PayViewModel extends ViewModel {

        public $viewFields = array(
            'SystemPay' => array('id', 'user_id', 'promoter_id', 'type', 'trade', 'transaction', 'money', 'commission', 'readmoney', 'state', 'time'),
            'User' => array('user_name', 'pen_name', '_on' => 'SystemPay.user_id=User.user_id'),
        );

    }
    