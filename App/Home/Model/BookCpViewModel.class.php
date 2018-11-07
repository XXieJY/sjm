<?php
namespace Home\Model;
use Think\Model\ViewModel;
    //作品公司类
    class BookCpViewModel extends ViewModel {

        public $viewFields = array(
            'Book' => array('book_id', 'cp_id', 'book_name', 'author_name', 'type_id', 'gender', 'upload_img', 'state', 'vip', 'money', 'is_show', 'audit', 'chapter', 'keywords', 'book_brief', 'words', 'time'),
            'Cp' => array('pen_name', '_on' => 'Book.cp_id=Cp.cp_id'),
            'BookType' => array('book_type', '_on' => 'Book.type_id=BookType.type_id'),
        );

    }