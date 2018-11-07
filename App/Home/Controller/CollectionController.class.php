<?php
namespace Home\Controller;
use Think\Controller;
use Think\Think;
    //收藏
    class CollectionController extends Controller{

        public function index() {
            $collection = M('BookCollection');
            $is = $collection->where(array('book_id' => $_POST[bookid], 'user_id' => $_COOKIE[user_id]))->find();
            if (!$is) {
                $data[book_id] = $_POST[bookid];
                $data[user_id] = $_COOKIE[user_id];
                $data[time] = date('Y-m-d H:i:s', time());
                $isok = $collection->add($data);

              $book=M('Book')->where(array('book_id'=>$_POST['bookid']))->find();

              A('Tuisong')->sendRequest($book['book_name'],$book['book_id']);

                if ($isok) {
                    $datas['collection_day'] = array('exp', "collection_day+1");
                    $datas['collection_weeks'] = array('exp', "collection_weeks+1");
                    $datas['collection_month'] = array('exp', "collection_month+1");
                    $datas['collection_total'] = array('exp', "collection_total+1");
                    M('BookStatistical')->where(array('book_id' => $_POST[bookid]))->save($datas);
                    S('WapBooks' . $_POST[bookid], NULL); //删除缓存
                    echo "1";
                } else {
                    echo "2";
                }
            } else {
                echo "2";
            }
        }

        public function dell() {
            $collection = M('BookCollection');
            $is = $collection->where(array('book_id' => $_POST[bookid], 'user_id' => $_COOKIE[user_id]))->find();
            if ($is) {
                $isok = $collection->where(array('id'=>$is[id]))->delete();
                if ($isok) {
                    $datas['collection_day'] = array('exp', "collection_day-1");
                    $datas['collection_weeks'] = array('exp', "collection_weeks-1");
                    $datas['collection_month'] = array('exp', "collection_month-1");
                    $datas['collection_total'] = array('exp', "collection_total-1");
                    M('BookStatistical')->where(array('book_id' => $_POST[bookid]))->save($datas);
                    S('WapBooks' . $_POST[bookid], NULL); //删除缓存
                    echo "1";
                } else {
                    echo "2";
                }
            } else {
                echo "2";
            }
        }

    }
    