<?php
namespace Home\Model;
use Think\Model\ViewModel;
    //收藏类
    class CollectionViewModel extends ViewModel {

        public $viewFields = array(
            'BookCollection' => array('id', 'chapter' => 'chapter_id','time'),
            'Book' => array('book_id', 'book_name','upload_img', '_on' => 'BookCollection.book_id=Book.book_id'),
            'Content' => array('content_id', 'num', 'title', '_on' => 'Book.chapter=Content.num  and  Book.book_id=Content.book_id'),
        );

    }
    