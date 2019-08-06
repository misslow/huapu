var app = getApp();
var http_url = app.globalData.http_api + "s=httpapi&id=4";
// http_url += '&api_call_function=module_search_news_list';


Page({
  /**
   * 页面的初始数据
   */
  data: {
    subtwo: "-1",  // 打开的二级目录
    subthree: "-1",  // 打开的三级目录
    majorList:[],
    // majorList: [
    //   {
    //     "subject": "农学",
    //     "submajor": [
    //       {
    //         "subname": "自然",
    //         "major": ["植物学", "花草学"]
    //       },
    //       {
    //         "subname": "社会学",
    //         "major": ["人文", "历史"]
    //       }
    //     ]
    //   },
    //   {
    //     "subject": "医学",
    //     "submajor": [
    //       {
    //         "subname": "临床医学",
    //         "major": ["动物临床医学", "人体临床医学"]
    //       }
    //     ]
    //   }
    // ]
  },
  // 点击一级目录
  openTwo(e) {
    var indexone = e.currentTarget.dataset.indexone
    if (this.data.subtwo == indexone) {
      this.setData({
        subtwo: "-1",
        subthree: "-1"
      })
    } else {
      this.setData({
        subtwo: indexone,
        subthree: "-1"
      })
    }
  },
  // 点击二级目录
  openThree(e) {
    var indextwo = e.currentTarget.dataset.indextwo
    if (this.data.subthree == indextwo) {
      this.setData({
        subthree: "-1"
      })
    } else {
      this.setData({
        subthree: indextwo
      })
    }
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
        console.log(res.data.data);
        wx.hideLoading();
        if (res.data.code == 1) {
          self.setData({
            majorList: res.data.data,
          });

        }

      }

    });

  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})