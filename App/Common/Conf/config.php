<?php
return array(
	//'配置项'=>'配置值'
    'DEFAULT_MODULE'     => 'Home', //默认模块
	'TMPL_FILE_DEPR' => '_', //模板文件MODULE_NAME与ACTION_NAME之间的分割符
    'URL_MODEL'          => '2', //URL模式
    'SESSION_AUTO_START' => true, //是否开启session	
	'MODULE_DENY_LIST'      =>  array('Common','Runtime','Api'),// 设置禁止访问的模块列表


	'WebName' => '水晶书城', //网站title
	'WebKeyword' => '水晶小说,原创小说网', //网站keywords
	 'DEFAULT_FILTER' => 'strip_tags,htmlspecialchars',//变量过滤机制
	 'ALL_ps' => 'shuijing', //自定义全局变量
	//数据库配置信息
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => '139.196.32.27', // 服务器地址
	'DB_NAME'   => 'shuijing', // 数据库名
	'DB_USER'   => 'shuijing', // 用户名
	'DB_PWD'    => 'AjZMWxBSUVdZLv5c', // 密码
	'DB_PORT'   => 3306, // 端口
	//'DB_PARAMS' =>  array(), // 数据库连接参数
	'DB_PREFIX' => 'hezuo_', // 数据库表前缀 
	'DB_CHARSET'=> 'utf8', // 字符集
	'DB_DEBUG'  =>  true, // 数据库调试模式 开启后可以记录SQL日志
	'LOG_RECORD' => true,
	'URL_ROUTER_ON' => true, //开启路由重写功能

   'URL_ROUTE_RULES'=>array(
        'books/:bookid'=> 'Home/Books/index',
        'chapter/:bookid/:num'=> 'Home/Chapter/index',
        'categories/:bookid'=> 'Home/Categories/index',
       'commend/:bookid/:num'=> 'Home/Commend/index',
	   'Bookcase/index'=> 'Home/Mybook/index',
	   'Login/index'=> 'Home/Login/index',
	   'Pay/index'=> 'Home/Pay/index',
	   'Myinfo/index'=> 'Home/Center/index',
	   'Rankinglist/index'=> 'Home/Rank/index',
	   'promoter/:bookid/:num'=> 'Home/Promoter/index',
    ),

    'HTML_CACHE_ON' => true, //开启静态缓存
    'HTML_CACHE_RULES' => array(//静态缓存规则
        'Rank:index' => array('WapRankinglist', 6000), //排行榜
        'Book:index' => array('Wap{:action}_{gender}_{type}_{state}_{vip}_{click}_{word}_{p}', 600), //小说书库
    ),

);