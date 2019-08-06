var WxParse = require('../../wxParse/wxParse.js');

var app = getApp();
var http_url = app.globalData.http_api + "s=httpapi&id=5";
// http_url += '&api_call_function=';
// var member_url = app.globalData.http_api + "s=api&app=news&c=module&api_auth_code=" + wx.getStorageSync('member_auth') + "&api_auth_uid=" + wx.getStorageSync('member_uid');


Page({
  data: {
    id: '',
    content: '',
    upsImg: "../../icons/ups.png",
    collectImg: "../../icons/collect.png",
  },
  onLoad: function (options) {

    app.showModel();
    var self = this;
    wx.request({
      url: http_url,
      header: {
        'content-type': 'application/json'
      },
      dataType: 'json',
      method: 'GET',
      success: function (res) {


        if (res.data.code == 1) {
          // 是否收藏
          

          // 格式化文章内容
          var article = res.data.data.content;

          WxParse.wxParse('data', 'html', article, self);

          self.setData({
            content: res.data.data,
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

})