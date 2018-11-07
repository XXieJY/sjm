$(function () {
    $("#vote").click(function () {
        if ($.cookie("user_id") != null) {
            $.ajax({
                url: "/Vote/vote.html",
                type: "post",
                data: 'bookid=' + $("#bookid").val(),
                success: function (data) {
                    if (data != 999) {
                        $("#vote span").html(parseInt($("#vote span").html()) + 1);
                        alert("鲜花赠送成功,剩余" + data + "票。");
                    } else {
                          alert("鲜花不足");
                    }
                }
            });
        } else {
            alert("请先登录");
        }


    });
    $("#vipvote").click(function () {

        if ($.cookie("user_id") != null) {
            $.ajax({
                url: "/Vote/vipvote.html",
                type: "post",
                data: 'bookid=' + $("#bookid").val(),
                success: function (data) {
                    if (data != 999) {
                        $("#vipvote span").html(parseInt($("#vipvote span").html()) + 1);
                        alert("钻石赠送成功,剩余" + data + "票。");
                    } else {
                        alert("钻石不足充值赠送");
                    }
                }
            });
        } else {
            alert("请先登录");
        }

    });
});
//收藏方法
function shou(is) {
    if (is == 1) {
        if ($.cookie("user_id") != null) {
            $.ajax({
                url: "/Home/Collection/index.html",
                type: "post",
                data: 'bookid=' + $("#bookid").val(),
                success: function (data) {
                    if (data == 1) {
                        $("#genduid").html(parseInt($("#genduid").html()) + 1);
                        $(".fui-flex-cell").html("<a class=\"nov-button\" href=\"javascript:void(0)\" onClick=\"shou(2)\" >取消收藏</a>");
                    } else {
                        $("#errors").html("该书您已收藏");
                    }
                }
            });
        } else {
             alert("请先登录");
        }

    } else if (is == 2) {
        if ($.cookie("user_id") != null) {
            $.ajax({
                url: "/Home/Collection/dell.html",
                type: "post",
                data: 'bookid=' + $("#bookid").val(),
                success: function (data) {
                    if (data == 1) {
                        $("#genduid").html(parseInt($("#genduid").html()) - 1);
                        $(".fui-flex-cell").html("<a href=\"javascript:void(0)\" onClick=\"shou(1)\" class=\" nov-button\">点击收藏</a>");
                    } else {
                        $("#errors").html("系统错误");
                    }
                }
            });
        } else {
             alert("请先登录");
        }
    }

}

