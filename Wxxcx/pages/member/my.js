var app=getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {

      member: wx.getStorageSync('member'),
  },

    changeAvatar:function(){
        var that = this;
        wx.chooseImage({
            count: 1, // 最多可以选择的图片张数，默认9
            sizeType: ['compressed'], // original 原图，compressed 压缩图，默认二者都有
            sourceType: ['album', 'camera'], // album 从相册选图，camera 使用相机，默认二者都有
            success: function(res2){
                wx.uploadFile({
                    url: app.globalData.http_api + "s=member&c=account&m=avatar&api_auth_code=" + wx.getStorageSync('member_auth') + "&api_auth_uid=" +wx.getStorageSync('member_uid'),
                    filePath:res2.tempFilePaths[0],
                    name:'file',
                    formData: {
                      is_ajax:1
                    },
                    header: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    success: function(res){
                      var ret = JSON.parse(res.data);
                      console.log(ret);
                        if (ret.code == 1) {
                            // 储存会员信息
                            wx.removeStorageSync('member');
                            wx.setStorageSync('member', ret.data);
                            // 修改成功
                            wx.showToast({
                                icon: 'success',
                                duration: 2000
                            });
                            wx.redirectTo({
                              url: '../member/my'
                            })
                        } else {
                            wx.showModal({
                                showCancel: false,
                                content: ret.msg
                            })
                        }

                    }
                })
            },
            fail: function() {
                // fail
            },
            complete: function() {
                // complete
            }
        })
    },
})