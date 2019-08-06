// pages/member/index.js

var app = getApp();
var member = wx.getStorageSync('member');
var member_auth = wx.getStorageSync('member_auth');
var member_uid = wx.getStorageSync('member_uid');
var avatar = '../../icons/user.png';
if (member['avatar']) {
  avatar = member['avatar'];
}

Page({

  /**
   * 页面的初始数据
   */
  data: {
    member: member,
    avatar: avatar,
    columnList: [
      {
        "url": "../member/my",
        "iconPath": "../../icons/star.png",
        "columnName": "账号信息"
      },

      {
        "url": "../member/account",
        "iconPath": "../../icons/star.png",
        "columnName": "修改资料"
      },
      {
        "url": "../member/password",
        "iconPath": "../../icons/star.png",
        "columnName": "修改密码"
      },
      {
        "url": "../member/recharge",
        "iconPath": "../../icons/star.png",
        "columnName": "账户充值"
      },
      {
        "url": "../member/paylog",
        "iconPath": "../../icons/star.png",
        "columnName": "我的交易"
      },
      {
        "url": "../member/scorelog",
        "iconPath": "../../icons/star.png",
        "columnName": "虚拟金币"
      },
      {
        "url": "../member/comment",
        "iconPath": "../../icons/star.png",
        "columnName": "文章评论"
      },
      {
        "url": "../member/favorite",
        "iconPath": "../../icons/star.png",
        "columnName": "文章收藏"
      }
    ],
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    console.log(member);
    console.log("member_uid:" + member_uid);
    console.log("member_auth:" + member_auth);
    if (member == "") {
      // 未登录跳转登录界面
      wx.reLaunch({ url: "../login/login" });
    }
  },
  downLogin: function () {
    wx.showModal({
      title: "退出",
      content: "是否退出？",
      success: function (res) {
        if (res.confirm) {
          wx.clearStorageSync('member');
          wx.clearStorageSync('member_uid');
          wx.clearStorageSync('member_auth');
          
          wx.reLaunch({ url: "../login/login" });
        }
      }
    })
  },
    userInfo:function(){
      if (wx.getStorageSync('member')) {
        wx.navigateTo({ url: "../member/my" });
      } else {
        wx.reLaunch({ url: "../login/login" });
      }
        

    },

})