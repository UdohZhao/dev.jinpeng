$(function(){
	$(".exitLogin").click(function(){
		$.confirm({
		  title: '您确定要退出登录吗？',
		  onOK: function () {
		    window.location.href='/Wap/Login/logout';//点击确认
		  },
		  onCancel: function () {
		  	$.closeModal();
		  }
		});
	})
})