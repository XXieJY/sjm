$(function(){
	
	var replyBtn = $(".reply-btn");
	var replyShade = $(".reply-shade");
	var replyScontent = $(".reply-scontent");
	var replyTextarea = $(".reply-textarea");
	var commentInput = $(".comment-input");
	replyBtn.on("touchend",function(){
		if(replyTextarea.val() == "" || commentInput.val()==""){
			replyShade.show();
			replyScontent.show();
		}
		setTimeout(function(){
			replyShade.hide();
			replyScontent.hide();
		},1000)
	});
	
	var $content = $('.remind-main');
    var $navLis = $content.find('.remind-nav li');
    var $mainItems = $content.find('.boosting-conent');
    $navLis.on('touchend', function(event){
        var $target = $(event.currentTarget);
        var index = $navLis.index($target);
        //�Ƴ�����li active classname;
        $navLis.removeClass('active');
        $target.addClass('active');
        $mainItems.hide();
        $mainItems.eq(index).show();
    });
	
	
	var personRead = $(".person-wrap .person-read");
	var pursuitDel = $(".pursuit-del");
	var affirmBtn = $(".js-affirm");
	var replyScontent = $(".reply-scontent");
	affirmBtn.on("touchend",function(){
		replyShade.hide();
		pursuitDel.hide();
		replyScontent.hide();
	});
	
	
	/*����*/
	$(".person-read").on("longTap", function(){
		$("#pursuit-layer").show();
	});
	/*ȡ��*/
	$("#pursuit-cancle").on("click", function(){
		$("#pursuit-layer").hide();
	});

	$(".fui-flex-tab-item").on("click", function(){
		var _index = $(".fui-flex-tab-item").index($(this));
		$(".fui-flex-tab-item").removeClass("active");
		$(this).addClass("active");
		var _index = $(".fui-flex-tab-item").index($(this));
		$(".tab-item").addClass("hidden");
		switch (_index){
			case(0):
				$(".tab-item").eq(0).removeClass("hidden");
				break;
			case(1):
				$(".tab-item").eq(1).removeClass("hidden");
				break;
			case(2):
				$(".tab-item").eq(2).removeClass("hidden");
				break;
		}
	});
	$(".nov-button").on("click", function(){
		var _index = $(".nov-button").index($(this));
		$(".nov-button").removeClass("nov-button-primary");
		$(this).addClass("nov-button-primary");
		console.log("*********_index: ", _index);
		if(_index > 1){
			var $img = $(this).find("img").eq(0);
			if($img.attr("src").indexOf("-active") != -1){
				$img.attr("src", _index==2?"/Public/Wap/images/flower.png":"/Public/Wap/images/diamond.png");
			}else{
				$img.attr("src", _index==2?"/Public/Wap/images/flower-active.png":"/Public/Wap/images/diamond-active.png");
			}
			if(_index == 2){
				$(".icon-in-nov-button").eq(1).attr("src", $(".icon-in-nov-button").eq(1).attr("src").replace(/-active.png/,".png"));
			}else{
				$(".icon-in-nov-button").eq(0).attr("src", $(".icon-in-nov-button").eq(0).attr("src").replace(/-active.png/,".png"));
			}
		}else{
			$(".icon-in-nov-button").eq(0).attr("src", $(".icon-in-nov-button").eq(0).attr("src").replace(/-active.png/,".png"));
			$(".icon-in-nov-button").eq(1).attr("src", $(".icon-in-nov-button").eq(1).attr("src").replace(/-active.png/,".png"));
		}
	});

	var $navLis = $('.tabs').find("div");
	$navLis.on('touchend', function(event){
		var $target = $(event.currentTarget);
		var index = $navLis.index($target);
		$navLis.removeClass('activite');
		$target.addClass('activite');
	});
});