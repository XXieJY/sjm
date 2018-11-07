<?php
namespace Home\Model;
use Think\Model\ViewModel;
    class ListViewModel extends ViewModel {

        public $viewFields = array(
            'Book' => array( 'book_id','book_name','author_name', 'upload_img', 'is_show', 'chapter', 'book_brief', 'words'),
            'BookStatistical' => array('click_day', 'click_month', 'click_weeks', 'click_total', '_on' => 'Book.book_id=BookStatistical.book_id'),
            'BookType' => array('book_type', '_on' => 'Book.type_id=BookType.type_id'),
            'Content' => array('content_id','title','num','time', '_on' => 'Book.chapter=Content.num  and  Book.book_id=Content.book_id')
        );

    }
    