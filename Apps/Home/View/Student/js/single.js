$(function(){
    //打钩单选
    $(".dob").click(function(){
        $(this).parents("li").siblings("li").children("a").children("img").css("display","none");
        $(this).children("img").css("display","block");
    })
})
