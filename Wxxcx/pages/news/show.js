var WxParse = require('../../wxParse/wxParse.js');

var app = getApp();
var http_url = app.globalData.http_api + "s=news&c=show";
http_url+= '&api_call_function=';
var member_url = app.globalData.http_api + "s=api&app=news&c=module&api_auth_code=" + wx.getStorageSync('member_auth') + "&api_auth_uid=" + wx.getStorageSync('member_uid');
 

Page({
  data:{
      id:'',
      content:'',
      supports: 0,
      upsImg:"../../icons/ups.png",
      collectImg:"../../icons/collect.png",
  },
  onLoad:function(options){

      app.showModel();
      var self=this;
      wx.request({
        url: http_url,
        data: {
          id:options.id
        },
        header: {
          'content-type': 'application/json'
        },
        dataType:'json',
        method: 'GET', 
        success: function(res){


            if (res.data.code == 1) {
                // 是否收藏
              wx.request({
                url: member_url +'&m=is_favorite',
                data: {
                  id: options.id
                },
                header: {
                  'content-type': 'application/json'
                },
                dataType: 'json',
                method: 'GET',
                success: function (sc) {
                  if (sc.data.code == 1) {
                    self.setData(
                      {
                        collectImg: "../../icons/collect-active.png",
                      })
                  }
                }
              });

                // 格式化文章内容
                var article = res.data.data.content;

                WxParse.wxParse('data', 'html', article, self);

                self.setData({
                    content:res.data.data,
                    supports: res.data.data.support,
                    id: options.id
                })
                wx.hideToast();
            } else {
              wx.showModal({
                showCancel: false,
                content: res.data.msg
              })
            }
          

         
        }
      })
  },
   getCommentList:function(){//评论跳转

      wx.navigateTo({
        url: '../news/comment?id='+this.data.content.id
     })
   },


   up:function(){//点赞

     var self = this;
     wx.request({
       url: member_url + '&m=digg&value=1',
       data: {
         id: self.data.id
       },
       header: {
         'content-type': 'application/json'
       },
       dataType: 'json',
       method: 'GET',
       success: function (sc) {
         if (sc.data.code == 1) {
           wx.showToast({
             icon: 'success',
             title: sc.data.msg,
             duration: 2000
           });
           self.setData(
             {
               supports: sc.data.data,
             })
         } else {
           wx.showModal({
             showCancel: false,
             content: sc.data.msg
           })
         }
       }
     });
   },
   collect: function (){//收藏
     var self =this;
     wx.request({
       url: member_url + '&m=favorite',
       data: {
         id: self.data.id
       },
       header: {
         'content-type': 'application/json'
       },
       dataType: 'json',
       method: 'GET',
       success: function (sc) {
         if (sc.data.code == 1) {
           wx.showToast({
             icon: 'success',
             title: sc.data.msg,
             duration: 2000
           });
           if (sc.data.msg =='收藏成功') {
             self.setData(
               {
                 collectImg: "../../icons/collect-active.png",
               })
           } else {
             self.setData(
               {
                 collectImg: "../../icons/collect.png",
               })
           }
         } else {
           wx.showModal({
             showCancel: false,
             content: sc.data.msg
           })
         }
       }
     });

   }


})