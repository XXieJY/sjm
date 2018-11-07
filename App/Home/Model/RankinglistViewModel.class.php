<?php
namespace Home\Model;
use Think\Model\ViewModel;
    class RankinglistViewModel extends ViewModel {

        public $viewFields = array(
            'Book' => array('book_id', 'book_name','upload_img'),
            'BookStatistical' => array('click_total','vipvote_total','buy_total','exceptional_total','vote_total','collection_total', '_on' => 'Book.book_id=BookStatistical.book_id'),
        );

    }