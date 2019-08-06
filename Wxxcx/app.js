//app.js
App({
 
  showModel:function(){
     wx.showToast({
      title: '正在加载....',
      icon: 'loading',
      duration: 5000
    });
  },

  globalData:{
    http_api:"https://www.fishclock.cn/index.php?appid=1&appsecret=123&",
  }
})