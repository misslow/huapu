var app=getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
      member: wx.getStorageSync('member'),
  },
  onLoad: function (options) {
    if(wx.getStorageSync('member') == "") {
      // 未登录跳转登录界面
      wx.reLaunch({ url: "../login/login" });
    }
  },

    /**
     * 表单提交
     */
    formBindsubmit: function (e) {

        app.showModel();
        var self = this;
        var openid = "";

        var money = e.detail.value.money;

        var postParams = "is_ajax=1&pay[mark]=recharge&pay[money]=" + money + "&pay[type]=weixin&pay[is_xcx]=1";
        wx.request({//登录
            url: app.globalData.http_api + "s=member&c=pay&m=index&api_auth_code=" + wx.getStorageSync('member_auth') + "&api_auth_uid=" + wx.getStorageSync('member_uid'),
            data: postParams,
            method: 'post',
            header: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            success: function (res) {
                console.log(res.data);
                wx.hideLoading();
                if (res.data.code) {

                    wx.request({
                        url: app.globalData.http_api + "s=api&c=pay&id="+res.data.code,
                        data: postParams,
                        method: 'get',
                        success: function (res) {
                          console.log(res.data);
                            if (res.data.code) {
                                // 服务端订单创建成功

                                wx.requestPayment({
                                  "timeStamp": res.data.data.timeStamp,
                                  "nonceStr": res.data.data.nonceStr,
                                    "package": res.data.data.package,
                                    "signType": "MD5",
                                    "paySign": res.data.data.paySign,
                                    "success":function(res){
                                        console.log(res);
                                        // 从服务端获取用户信息并储存
                                        wx.request({//提交
                                          url: app.globalData.http_api + "s=member&c=home&m=index&api_auth_code=" + wx.getStorageSync('member_auth') + "&api_auth_uid=" + wx.getStorageSync('member_uid'),
                                          method: 'get',
                                          success: function (res) {
                                            if (res.data.code == 1) {
                                              // 重新获取用户信息
                                              // 储存会员信息
                                              wx.removeStorageSync('member');
                                              wx.setStorageSync('member', res.data.data);
                                              wx.showModal({
                                                showCancel: false,
                                                success: function (res) {
                                                  if (res.confirm) {
                                                    wx.reLaunch({ url: "../member/paylog" });
                                                  }
                                                },
                                                content: "付款成功"
                                              });
                                              
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
                                    "fail":function(res){
                                        console.log(res);
                                        wx.showModal({
                                            showCancel: false,
                                            content: res.err_desc+"："+res.errMsg
                                        })
                                    }
                                })
                            }
                            else {
                                wx.showModal({
                                    showCancel: false,
                                    content: res.data.msg
                                })
                            }
                        }
                    });


                }  else {
                    wx.showModal({
                        showCancel: false,
                        content: res.data.msg
                    })
                }
            }
        })




    },
})