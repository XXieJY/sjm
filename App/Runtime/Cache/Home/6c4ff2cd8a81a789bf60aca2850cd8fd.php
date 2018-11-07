<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta content="telephone=no" name="format-detection" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
<title><?php echo C('WebName');?>_原创小说网</title>
	<link rel="shortcut icon" href="Favicon.ico" type="image/x-icon" />
<link href="/sjtest/Public/Wap/css/reset.css" rel="stylesheet">
	<link href="/sjtest/Public/Wap/css/cus-css.css" rel="stylesheet">
	<script type="text/javascript" src="/sjtest/Public/Wap/js/jquery.min.js"></script>
	<script type="text/javascript" src="/sjtest/Public/Wap/js/jquery.cookie.js"></script>
</head>
<body class="index-body">

<header>
    <img src="/sjtest/Public/Wap/images/logo1.png" class="logo"/>
    <ul class="nav">
        <li>
            <a href="/Home/Index/index">首页</a>
        </li>
        <li>
            <a href="/Home/Book/index/gender/3/type/50/state/3/vip/3/click/0.html">书库</a>
        </li>
        <li>


            <a href="/Home/Rank/index">排行</a>
        </li>
        <!--<li>
            <a href="#">精品</a>
        </li>-->
        <li>
            <a href="/Home/Pay/index">充值</a>
        </li>
    </ul>
</header>
<script type="text/javascript">

    var user;
    if ($.cookie("user_id")){
     user="<div class=\"wrap-logo-way\">";
     user+="<ul class=\"wrap-logined\">";
     user+="<li class=\"fl\" style=\"margin-left: 32px;\">";
     user+="<a href=\"/Home/Mybook/index\">我的书架</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
     user+="</li>";
     user+="<li class=\"wrap-mesg-tips fl\">";
     user+="<a href=\"/Home/Login/logout\">退出</a>";
     user+="</li>";
     user+="<a href=\"/Home/Center/index\"><li>"+ $.cookie("pen_name") +"</li></a>";
     user+="<a href=\"/Home/Center/index\"><li class=\"wrap-tx\"><img src=\"/Upload/upload_portrait/" + $.cookie("portrait") + "\" style=\"border-radius:50%;\"/></li></a>";
     user+="</ul>";
     user+="</div>";

     document.write(user);

    }else{

        user="<div class=\"wrap-logo-way\" id=\"div2\" style=\"display: block\">";
        user+="<ul class=\"login-way\">";
        user+="<li><a href=\"/Home/Login/index\" >登录</a></li>";
        user+="<li style=\"width: 40px;\"><a href=\"/Home/Register/index\" >注册</a></li>";
        user+="<li><a href=\"/Home/Accounts/qq.html\" ><img src=\"/sjtest/Public/Wap/images/share_icon_4.png\"/></a></li>";
        user+="<li><a href=\"/Home/Accounts/weixin.html\" ><img src=\"/sjtest/Public/Wap/images/share_icon_2.png\"/></a></li>";
        user+="</ul>";
        user+="</div>";
        document.write(user);


    }

</script>










<div class="search-box">
	<form action="/Home/Search/index/current/1" method="post">
		<input type="text" id="searchInput" name="keyword" placeholder="输入关键字"/>
		<input type="submit" id="searchBtn" value="搜索"/>
	</form>
</div>
<ul class="wrap-keywords">
	<?php if(is_array($sousuo)): $i = 0; $__LIST__ = $sousuo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="/books/<?php echo ($vo["book_id"]); ?>.html"><?php echo ($vo["book_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul>
<div class="banner diplay-flex">

	<?php if(is_array($slide)): $i = 0; $__LIST__ = $slide;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="banner-item flex-1">
		<a href="/books/<?php echo ($vo["book_id"]); ?>.html"><img src="/Upload/Book/zhong/<?php echo ($vo["upload_img"]); ?>" alt="<?php echo ($vo["book_name"]); ?>" class=""></a>
	</div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<div class="wrap-content">
	<div class="wrap-content-title">
		<div>热门推荐</div>
	</div>
	<ul class="result-set border-less">
		<li class="result-set-item">
			<div class="result-set-item-top">
				<a href="/books/<?php echo ($hots[0]["book_id"]); ?>.html"><img src="/Upload/Book/zhong/<?php echo ($hots[0]["upload_img"]); ?>" class="searchset-img"/>
				<dl>
					<dd class="result-set-title"><?php echo ($hots[0]["book_name"]); ?></dd>
					<dd class="result-set-auther">
						<?php echo ($hots[0]["book_brief"]); ?>
					</dd>
				</dl>
				</a>
			</div>
		</li>

		<?php if(is_array($hots)): $i = 0; $__LIST__ = array_slice($hots,1,null,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="result-set-item">
			<span class="categories-label">[<?php echo ($vo["book_type"]); ?>]</span><a href="/books/<?php echo ($vo["book_id"]); ?>.html"><span class="tj-book-name"><?php echo ($vo["book_name"]); ?></span></a>
		</li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
</div>
<div class="banner border-radius-5">
	<a href="/books/29.html"><img src="/Upload/Ban/<?php echo ($ban[0][pic]); ?>"/></a>
</div>
<div class="wrap-content" style="margin-top:15px;">
	<div class="wrap-content-title">
		<div>潜力新作</div>
	</div>
	<ul class="like-list">

	<?php if(is_array($xinzuo)): $i = 0; $__LIST__ = $xinzuo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
			<a href="/books/<?php echo ($vo["book_id"]); ?>.html"><img src="/Upload/Book/zhong/<?php echo ($vo["upload_img"]); ?>"/></a>
			<p>
				<?php echo ($vo["book_name"]); ?>
			</p>
		</li><?php endforeach; endif; else: echo "" ;endif; ?>

	</ul>
</div>

<div class="banner border-radius-5">
	<a href="/books/190.html"><img src="/Upload/Ban/<?php echo ($ban[1][pic]); ?>"/></a>
</div>

<div class="wrap-content" style="margin-top:20px;">
	<div class="wrap-content-title">
		<div>热销专区</div>
	</div>
	<ul class="result-set border-less">
		<li class="result-set-item">
			<div class="result-set-item-top">
				<a href="/books/<?php echo ($vo["book_id"]); ?>.html"><img src="/Upload/Book/zhong/<?php echo ($rexiao[0]["upload_img"]); ?>" class="searchset-img"/>
				<dl>
					<dd class="result-set-title"><?php echo ($rexiao[0]["book_name"]); ?></dd>
					<dd class="result-set-auther">
						<?php echo ($rexiao[0]["book_brief"]); ?>
					</dd>
				</dl>
				</a>
			</div>
		</li>

      <?php if(is_array($rexiao)): $i = 0; $__LIST__ = array_slice($rexiao,1,null,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="result-set-item">
			<span class="categories-label">[<?php echo ($vo["book_type"]); ?>]</span><a href="/books/<?php echo ($vo["book_id"]); ?>.html"><span class="tj-book-name"><?php echo ($vo["book_name"]); ?></span></a>
		</li><?php endforeach; endif; else: echo "" ;endif; ?>

	</ul>
</div>
<div class="wrap-content">
	<div class="wrap-content-title">
		<div>精品推荐</div>
	</div>
	<ul class="result-set border-less">
		<li class="result-set-item">
			<div class="result-set-item-top">
				<a href="/books/<?php echo ($vo["book_id"]); ?>.html"><img src="/Upload/Book/zhong/<?php echo ($jingpin[0]["upload_img"]); ?>" class="searchset-img"/>
				<dl>
					<dd class="result-set-title"><?php echo ($jingpin[0]["book_name"]); ?></dd>
					<dd class="result-set-auther">
						<?php echo ($jingpin[0]["book_brief"]); ?>
					</dd>
				</dl>
				</a>
			</div>
		</li>

	<?php if(is_array($jingpin)): $i = 0; $__LIST__ = array_slice($jingpin,1,null,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="result-set-item">
			<span class="categories-label">[<?php echo ($vo["book_type"]); ?>]</span><a href="/books/<?php echo ($vo["book_id"]); ?>.html"><span class="tj-book-name"><?php echo ($vo["book_name"]); ?></span></a>
		</li><?php endforeach; endif; else: echo "" ;endif; ?>

	</ul>
</div>

<!--公共底部-->
<div class="read-bottom">
    <p class="readb-nav diplay-flex">
        <a href="/Home/Index/index">首页</a>
        <a href="/Home/Mybook/index">我的书架</a>
        <a href="/Home/Pay/index">充值</a>
        <a href="/Home/Login/logout">退出</a>
        <a href="#">返回顶部</a>
    </p>
    <p class="readb-title">联系客服</p>
    <p class="font-size-12">工作时间：周一到周五  10:00-18:00</p>
    <p class="font-size-12">客服邮箱：3404797373@qq.com</p>
    <p class="font-size-12">客服QQ：3404797373</p>
    <p class="font-size-12">客服电话：0571-85212232</p>
    <img src="/sjtest/Public/Wap/images/qrcode_jiukan.jpg" class="footer-code-img"/>
    <p class="readb-title">浙ICP备16034663号-1</p>
    <p>Copyright @2016 sjnovel.com All rights reserved</p>
</div>
<!--公共底部 结束-->
<script src="/sjtest/Public/Wap/js/zepto.min.js"></script>
</body>
</html>