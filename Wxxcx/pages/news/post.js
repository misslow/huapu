var app = getApp();

var member_auth_string =  "&api_auth_code=" + wx.getStorageSync('member_auth') + "&api_auth_uid=" + wx.getStorageSync('member_uid');

var post_url = app.globalData.http_api + "s=member&app=news&c=home&m=add";
post_url += member_auth_string;

// fid=19是缩略图的字段id号
var upload_url = app.globalData.http_api + "s=api&c=file&m=upload_file&fid=19";
upload_url += member_auth_string;

var thumb_id = 0;// 储存缩略图id

Page({
  data: {
    postData: [],
  },
   formSubmit:function(e){
        console.log(e.detail.value)
        this.setData({postData:e.detail.value});
    },


    add: function () {

        var self=this;

        var postParams = "is_ajax=1"
            +"&data[title]="  + self.data.postData.title
            +"&data[content]="  + self.data.postData.content
            +"&data[thumb]="  + thumb_id
            +"&catid=11";// 暂时固定栏目11
        wx.request({
            url: post_url,
            data: postParams,
            method: 'post',
            header: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            success: function (res) {
                console.log(res.data);
                if (res.data.code) {
                    wx.showToast({
                        title: res.data.msg,
                        icon: 'success'
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


    uploadFile:function(){
        var that = this;
        wx.chooseImage({
            count: 1, // 最多可以选择的图片张数，默认9
            sizeType: ['compressed'], // original 原图，compressed 压缩图，默认二者都有
            sourceType: ['album', 'camera'], // album 从相册选图，camera 使用相机，默认二者都有
            success: function(res2){
                wx.uploadFile({
                    url: upload_url,
                    filePath:res2.tempFilePaths[0],
                    name:'file_data',
                    formData: {
                      is_ajax:1
                    },
                    header: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    success: function(res){
                      var ret = JSON.parse(res.data);
                      console.log(ret);
                        if (ret.code) {
                          thumb_id = ret.code;
                            wx.showModal({
                                showCancel: false,
                                content: "上传成功："+ret.data.url
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