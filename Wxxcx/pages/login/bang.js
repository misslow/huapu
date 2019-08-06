
var app = getApp();
Page({

  getUserName: function (e) {
    this.setData({
      userName: e.detail.value
    });

  },
  getUserPwd: function (e) {
    this.setData({ userPwd: e.detail.value });
  },

  login: function () {


      app.showModel();
    var oauth = wx.getStorageSync('oauth');
    if (oauth == "") {

      wx.navigateTo({ url: "../login/login" });
      return;
    }

    var self = this;
    var postParams = "is_ajax=1&data[username]=" + this.data.userName + "&oid="+oauth['id']+"&data[password]=" + this.data.userPwd;
    wx.request({//登录
      url: app.globalData.http_api + "s=weixin&c=member&m=xcx_bang",
      data: postParams,
      method: 'post',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      success: function (res) {
        console.log(res.data);
        if (res.data.code == 1) {
          // 登录成功储存会员信息
          wx.setStorageSync('member_uid', res.data.data.member.id);
          wx.setStorageSync('member_auth', res.data.data.auth);
          wx.setStorageSync('member', res.data.data.member);
          // 跳转到会员页面
            wx.showToast({
                title: "绑定成功",
                icon: 'success',
                success: function () {
                    wx.reLaunch({ url: "../member/index" });
                },
                duration: 2000
            })
        }
        else {
            wx.showModal({
                showCancel: false,
                content: res.data.msg
            })
        }
      }
    })
  },
})