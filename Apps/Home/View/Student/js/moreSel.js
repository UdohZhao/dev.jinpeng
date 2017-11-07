/*
$(function(){
    //打钩多选
    $(".list-inline a").click(function(){

        //$(this).parents("li").siblings("li").children("a").children("img").css("display","none");
        $(this).children("img").toggle();
    })
})
*/


$(function(){
    //打钩单选
    $(".imagesDob").click(function(){
        console.log($(this).children("img"));
        for(var i=0;i<$(this).children("img").length;i++){
            alert($(this).children("img").length)
        }
        //$(this).parents("li").siblings("li").children("a").children("img").css("display","none");
        $(this).children("img").toggle();
    })
})
