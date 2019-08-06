var app=getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
      regData: '',
  },

    formSubmit:function(e){
        console.log(e.detail.value)
        this.setData({regData:e.detail.value});
    },


    add: function () {

        var self=this;

        var oauth = wx.getStorageSync('oauth');
        if (oauth == "") {
            wx.navigateTo({ url: "../login/login" });
            return;
        }



        var self = this;
        var postParams = "is_ajax=1&oid="+oauth['id']
            +"&data[username]="  + self.data.regData.username
            +"&data[email]="  + self.data.regData.email
            +"&data[phone]="  + self.data.regData.phone
            +"&data[password]="  + self.data.regData.pwd1
            +"&data[password2]=" + self.data.regData.pwd2;
        wx.request({
            url: app.globalData.http_api + "s=weixin&c=member&m=xcx_reg",
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

                    wx.showToast({
                        title: "注册成功",
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