<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/29
 * Time: 13:30
 */
header("Content-Type:text/html;charset=utf-8");
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
//开启调试模式
define('APP_DEBUG', TRUE);

//定义项目路径
define('APP_PATH', './App/');
//定义项目名称
define('APP_NAME', 'Home');
define('HTML_PATH', './Html_Cache/');
require './ThinkPHP/ThinkPHP.php';
