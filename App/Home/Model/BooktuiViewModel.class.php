<?php
namespace Home\Model;
use Think\Model\ViewModel;
    class BooktuiViewModel extends ViewModel {

        public $viewFields = array(
            'BookPromote' => array('id', 'promote_id', 'xu', 'book_id','book_name','upload_img','book_brief'),
            'Book' => array('author_name','type_id', '_on' => 'BookPromote.book_id=Book.book_id'),
        );

    }
    