
var app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {

    userName: "",
    userPwd: "",
  },



  getUserName: function (e) {
    this.setData({
      userName: e.detail.value
    });

  },
  getUserPwd: function (e) {
    this.setData({ userPwd: e.detail.value });
  },

  login:function(){

      app.showModel();
    var self = this;
    var postParams = "is_ajax=1&data[username]=" + this.data.userName + "&data[password]=" + this.data.userPwd;
    wx.request({//登录
      url: app.globalData.http_api + "s=member&c=login",
      data: postParams,
      method: 'post',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      success: function (res) {
        console.log(res.data);
        if (res.data.code == 1) {
          // 登录成功储存会员信息
          wx.clearStorageSync();
          wx.setStorageSync('member_uid', res.data.data.member.id);
          wx.setStorageSync('member_auth', res.data.data.auth);
          wx.setStorageSync('member', res.data.data.member);
          // 跳转到会员页面
          wx.showToast({
            title: "登录成功",
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

  

  weixinLogin: function () {
    //发起微信登陆
    var self = this;
      app.showModel();
    wx.login({
      success: function (res) {
        if (res.code) {
          wx.getUserInfo({
            success: function (userRes) {


                app.showModel();
              //发起网络请求D
              wx.request({
                url: app.globalData.http_api + "s=weixin&c=member&m=xcx",
                data: {
                  json: userRes.rawData,
                  js_code: res.code
                },
                method: 'post',
                header: {
                  'Content-Type': 'application/x-www-form-urlencoded',
                },
                success: function (res) {
                  console.log(res.data);
                  if (res.data.code) {
                    if (res.data.msg == 'login') {
                      // 登录成功
                      console.log("登录成功了");
                      wx.setStorageSync('member_uid', res.data.data.member.id);
                      wx.setStorageSync('member_auth', res.data.data.auth);
                      wx.setStorageSync('member', res.data.data.member);
                 
                      wx.showToast({
                        title: "登录成功",
                        icon: 'success',
                        success: function () {
                          wx.reLaunch({ url: "../member/index" });
                        }
                      })

                    } else {
                      // 绑定账号注册
                      wx.setStorageSync('oauth', res.data.data);
                      wx.showActionSheet({
                        itemList: ['绑定已有账号', '注册新账号'],
                        success: function (res) {
                          if (res.tapIndex == 1) {
                            wx.navigateTo({ url: "../login/register" });
                          } else {
                            wx.navigateTo({ url: "../login/bang" });
                          }
                        },
                        fail: function (res) {
                          console.log(res.errMsg)
                        }
                      })  
                      
                      //
                    }
                  } else {
                    // 失败了
                      wx.showModal({
                          showCancel: false,
                          content: res.data.msg
                      })
                  }
                }
              });
            }
          });
        } else {
          console.log('登录失败：' + res.errMsg)
        }
      }
    });
  }


})