var app = getApp();
var http_url = app.globalData.http_api + "s=news&c=comment&page=1";
http_url += '&api_call_function=module_comment_list';

var member_url = app.globalData.http_api + "s=news&m=post&c=comment&api_auth_code=" + wx.getStorageSync('member_auth') + "&api_auth_uid=" + wx.getStorageSync('member_uid');


Page({
  data: {
    id: '',
    content: '',
    supports: 0,
    upsImg: "../../icons/ups.png",
    collectImg: "../../icons/collect.png",
    listData: [],
    hidden: true,
    page: 1,
    hasMore: "false"
  },
  onLoad: function (options) {

    app.showModel();
    var self = this;
    wx.request({
      url: http_url,
      data: {
        id: options.id
      },
      header: {
        'content-type': 'application/json'
      },
      dataType: 'json',
      method: 'GET',
      success: function (res) {


        if (res.data.code == 1) {
          
          self.setData({
            content: res.data.data,
            listData: res.data.data.list,
            supports: res.data.data.support,
            id: options.id,
            page: 1
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



  getText: function (e) {
    this.setData({ text: e.detail.value });

  },

  save: function () {
    //发布评论
      
    var self = this;
    var text = self.data.text;//评论内容
    
    wx.request({
      url: member_url + "&id=" + self.data.id,
      data: {
        content: text,
      },
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      dataType: 'json',
      method: 'POST',

      success: function (res) {
        console.log(res.data);
        if (res.data.code == 1) {
          wx.showToast({
            title: res.data.msg,
            icon: 'success',
            duration: 10000
          });
          setTimeout(function () {
            wx.hideToast();
            wx.redirectTo({
              url: 'comment?id=' + self.data.id
            })
          }, 700)


        }
        else {
          wx.showToast({
            title: res.data.msg,
            icon: 'loading',
            duration: 500
          });
        }

      }
    });





  },
  
onReachBottom: function () {

    this.setData({ hidden: false });
    var self = this;
    var pageid = self.data.page + 1;

    wx.request({
      url: http_url + "&id=" + self.data.id,
      method: 'GET',
      data: {
        page: pageid
      },

      success: function (res) {

        if (res.data.code == 1) {
          if (res.data.data.list.length == 0) {
            self.setData({
              hasMore: "true",
              hidden: false
            });
            setTimeout(function () {
              self.setData({
                hasMore: "false",
                hidden: true
              });
            }, 900)
          } else {
            self.setData({
              listData: res.data.data.list,
              hidden: true,
              page: pageid
            });
          }
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