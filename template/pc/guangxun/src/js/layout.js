

$(function () {

    var removeCssAttr = function (elem, attr) {
        var s = elem.style;
        if (s.removeProperty) {
            s.removeProperty(attr);
        } else {
            s.removeAttribute(attr);
        }
    };
    minwidth();
    $(window).load(function () {
        $(window).resize(function () {
            minwidth();
        })
    });
    function minwidth() {
        if ($(window).width() < 1200) {
            $("body").css({ "width": "1200px" });
        } else {
            removeCssAttr(document.body, 'width');
        }
    }
})


// function windowresize() {

//     //-------- 头部banner一屏 --------
//     var dubbleHeight = 0;
//     var winHeight = $(window).height() - dubbleHeight;
//     var winwidth = $(window).width();

//     var autoResizeImage = function (mix, width, height, callback) {
//         var iw, ih, w, h,
//             skew, url, prop, x = 0, y = 0,
//             obj = {}, image = new Image,
//             callback = callback || function () { };

//         if (typeof mix == 'string') {
//             url = mix
//         } else if (mix && mix.src) {
//             url = mix.src;
//             obj = mix;
//         } else {
//             return {};
//         }
//         image.onload = function () {
//             iw = this.width, ih = this.height;
//             if (iw / ih < width / height) {
//                 w = width;
//                 h = w * ih / iw;
//                 y = (height - h) / 2;
//             } else {
//                 h = height;
//                 w = h * iw / ih;
//                 x = (width - w) / 2;
//             }
//             prop = { width: w, height: h, x: x, y: y };
//             callback.call(obj, prop);
//         }
//         image.src = url;
//     };

//     var setProp = function (prop) {
//         this.style.width = prop.width + 'px';
//         this.style.height = prop.height + 'px';
//         this.style.marginLeft = prop.x + 'px';
//         this.style.marginTop = prop.y + 'px';
//     };


//     //图片满屏截取
//     if (winwidth > 1200) {
//         $('.indexbannerlist img').each(function () {
//             autoResizeImage(this, winwidth, winHeight, setProp);
//         });
//         $(".indexslideS ul li a img").each(function () {
//             autoResizeImage(this, winwidth / 4, (winHeight - 166) / 3, setProp);
//         })
//         $(".productmain1 .img img").each(function () {
//             autoResizeImage(this, winwidth, winHeight - 144, setProp);
//         })
//     } else {
//         $('.indexbannerlist img').each(function () {
//             autoResizeImage(this, 1200, winHeight, setProp);
//         });
//         $(".indexslideS ul li a img").each(function () {
//             autoResizeImage(this, 1200 / 4, (winHeight - 166) / 3, setProp);
//         })
//         $(".productmain1 .img img").each(function () {
//             autoResizeImage(this, 1200, winHeight - 144, setProp);
//         })
//     }
//     if (winwidth > 1200) {
//         $(".indexslidebox .bg").css({ width: winwidth, height: winHeight });
//         $(".indexslide").css({ "width": Math.floor(winwidth / 4), "overflow": "initial" });
//         $(".indexbannerlist li").css({ "height": $(window).height(), "width": $(window).width() });
//     } else {
//         $(".indexbannerlist li").css({ "height": winHeight, "width": 1200 });
//         $(".indexslidebox .bg").css({ width: 1200, height: winHeight });
//         $(".indexslide").css({ "width": 1200 / 4, "overflow": "initial" });
//     }
//     $(".indexslideS ul li a").css({ height: (winHeight - 166) / 3 });
//     $(".indexslideS ul li").css({ top: (winHeight - 166) / 3 });
//     $(".indexbanner").css({ "height": $(window).height() });
//     $(".indexbannerlist").css({ "height": $(window).height() });
//     $(".indexslidebox").css({ "top": winHeight - 166 });
//     $(".recruitbody").height($(window).height());
//     $(".recruitbody .bgimg img").height($(window).height());
//     $(".indexshortcutnav").css({ "top": $(window).height() - 230 });
// }

$(function () {
    windowresize();
    $(".content").css({ "min-height": $(window).height() });
    $(window).resize(function () {
        windowresize();
        $(".content").css({ "min-height": $(window).height() });
    })

    $(".footattention span a img").hover(function () {
        $(this).parents("span").find(".ewm").show();
    }, function () {
        $(this).parents("span").find(".ewm").hide();
    })

    //导航
    var oldindex1 = 0;
    $(".headernav li.oneJli").hover(function () {
        $(".headernav").addClass("showtwo");
        var nowindex = $(this).index();
        $(this).addClass("hover");
        $(this).parents(".header").addClass("Hheader");
        if (nowindex == 4) {
            return false;
        }
        if ($(".navtworank").height() == 0) {
            $(".navtworank").find(".headnavtwo").eq(nowindex).stop(false, true).slideDown(150);
        } else {
            $(".navtworank").find(".headnavtwo").eq(oldindex1).hide();
            $(".navtworank").find(".headnavtwo").eq(nowindex).show();
        }
        oldindex1 = nowindex;

    }, function () {
        $(".headernav").removeClass("showtwo");
        $(this).removeClass("hover");
        var nowindex = $(this).index();
        var c = setTimeout(function () {
            if (!$(".headernav").hasClass("showtwo")) {
                $(".navtworank").find(".headnavtwo").eq(nowindex).stop().slideUp(150);
            }
        }, 150)
    })
    $(".navtworank .headnavtwo").hover(function () {
        $(".headernav").addClass("showtwo");
        $(this).show();
    }, function () {
        $(".headernav").removeClass("showtwo");
        var _this = $(this);
        var d = setTimeout(function () {
            if (!$(".headernav").hasClass("showtwo")) {
                _this.stop().slideUp(150);
            }
        }, 150)
    })
    $(".header").mouseleave(function () {
        $(this).removeClass("Hheader");
    })
    $(".navproduct ul li:first").addClass("firstli");

    //foot
    $(".footattention dl dd").hover(function () {
        $(this).addClass("hover");
    }, function () {
        $(this).removeClass("hover");
    })
    $(".slidelinkbtn").click(function () {
        if ($(this).hasClass("show")) {
            $(this).removeClass("show");
            $(".friendlinkbox").stop().slideUp();
        } else {
            $(this).addClass("show");
            $(".friendlinkbox").stop().slideDown();
        }
    })

    //index
    $(function () {
        $(".indexslide").each(function () {
            $(this).attr("lii" + $(this).index(), 0);
            var lii;
            $(this).hover(function () {
                var nowattr = "lii" + $(this).index()
                lii = $(this).attr(nowattr);
                //$(this).parents(".indexslidebox").find(".bg").show();
                $(this).find(".indexslideS").show();
                _this = $(this).find(".indexslideS").find("ul li");
                var nowli = _this.length;
                aaa(nowli, _this);
                function aaa(nowli, _this) {
                    _this.eq(nowli - 1 - lii).stop().animate({ "opacity": 1, "top": 0 }, 300, function () {
                        lii++;
                        if (lii >= nowli) { return false } else {
                            $(this).parents(".indexslide").attr(nowattr, lii);
                            aaa(nowli, _this);
                        }
                    });
                    return lii;
                }
            }, function () {
                var nowattr = "lii" + $(this).index()
                lii = $(this).attr(nowattr);
                $(this).parents(".indexslidebox").find(".bg").hide();
                _this = $(this).find(".indexslideS").find("ul li");
                var nowli = _this.length;
                bbb(nowli, _this);
                function bbb(nowli, _this) {
                    _this.eq(nowli - 1 - lii).stop().animate({ "opacity": 0, "top": ($(window).height() - 166) / 3 }, 100, function () {
                        lii--;
                        if (lii < 0) { $(this).parents(".indexslide").find(".indexslideS").hide(); return false } else {
                            $(this).parents(".indexslide").attr(nowattr, lii);
                            bbb(nowli, _this);
                        }
                    });
                    return lii;
                }

                $(this).find(".indexslideS").hide();
            })
        })
        $(".indexshortcutnavM").click(function () {
            if ($(this).hasClass("show")) {
                $(this).find(".indexshortcutnavbox").stop().slideUp();
                $(this).removeClass("show");
            } else {
                $(this).find(".indexshortcutnavbox").stop().slideDown();
                $(this).addClass("show");
            }
        })
    })

    //文字滚动
    function textroll() {
        $(".indexpublicity ul").animate({ "margin-top": "-24px" }, 400, function () {
            $(".indexpublicity ul li:first").appendTo($(".indexpublicity ul"));
            $(".indexpublicity ul").css({ "margin-top": 0 });
        })
    }
    if ($(".indexpublicity ul li").length > 1) {
        var indexT = setInterval(function () {
            textroll()
        }, 3000);

        $(".indexpublicity ul,.indexgdnews .leftbtn, .indexgdnews .rightbtn").hover(function () {
            clearInterval(indexT)
        }, function () {
            indexT = setInterval(function () {
                textroll()
            }, 3000);
        })
        $(".indexgdnews .rightbtn").click(function () {
            textroll();
        })
        $(".indexgdnews .leftbtn").click(function () {
            $(".indexpublicity ul li:last").prependTo($(".indexpublicity ul"));
            $(".indexpublicity ul").css({ "margin-top": "-24px" });
            $(".indexpublicity ul").animate({ "margin-top": "0" }, 400)
        })
    }

    $(".scrlldownbtn").click(function () {
        $(".footer").css({ "display": "none" });
        $("html,body").animate({ scrollTop: $(window).height() }, 500);
    })
    $(window).scroll(function () {
        $(".footer").css({ "display": "block" });
    })

    //产品对比页
    $(".productcompareM table tr").find("td:first").addClass("firsttd");
    var nowtdnum = $(".productcompareM table tr:first").find("td").length;
    $(".productcompareM table tr").find("th").attr("colspan", nowtdnum);
    $(window).scroll(function () {
        if ($(".proSname").length > 0) {
            if ($(window).scrollTop() > $(".proSname").offset().top - 47) {
                $(".proSnameM").css({ "position": "fixed", "top": 47, "left": 0 });
            } else if ($(window).scrollTop() <= $(".proSname").offset().top) {
                $(".proSnameM").css({ "position": "initial" });
            }
        }
    })
    //风琴效果
    $(function () {
        $(".productmain14M ul li").eq(0).addClass("cur").css({ width: 340 });
        $(".productmain14M ul li").eq(0).find(".textbox").css({ "height": 188, "padding-top": 19, "padding-bottom": 40 });
        var oldli = 0;
        var t;
        $(".productmain14M ul li").hover(function () {
            var _this = $(this);
            var _thisI = $(this).index();
            clearTimeout(t);
            t = setTimeout(function () {
                if (_thisI == oldli) { return false } else {
                    $(".productmain14M ul li").eq(oldli).removeClass("cur").stop(true).animate({ width: 171 }, 300);
                    $(".productmain14M ul li").eq(oldli).find(".textbox").stop(true).animate({ "height": 56, "padding-top": 0, "padding-bottom": 0 }, 300);
                    _this.addClass("cur").stop(true).animate({ width: 340 }, 300);
                    _this.find(".textbox").stop(true).animate({ "height": 188, "padding-top": 19, "padding-bottom": 40 }, 300)
                    oldli = _this.index();
                };
            }, 200);
        }, function () {
            clearTimeout(t);
        })
    })

    //招聘
    $(function () {
        $(document).on("click", ".jobslist dl", function () {
            if ($(this).parents(".jobmlist").find(".RecruitmentContent").length > 0) {
                if ($(this).hasClass("cur")) {
                    $(this).removeClass("cur");
                    $(this).parents(".jobmlist").find(".RecruitmentContent").stop().slideUp();
                } else {
                    $(this).parents(".jobslist").find(".jobmlist dl").removeClass("cur");
                    $(this).addClass("cur");
                    $(this).parents(".jobslist").find(".jobmlist .RecruitmentContent").stop().slideUp();
                    $(this).parents(".jobmlist").find(".RecruitmentContent").stop().slideDown();
                }
            }
        })
    })
    if ($(".indexbody").length == 0 && $(".proSname").length == 0 && $(".productmainT").length == 0) {
        //$(".header").css({ "position": "fixed" });
        var oldscrollT = 0;
        oldscrollT = $(window).scrollTop();
        $(window).scroll(function () {
            var nowscrollT = $(window).scrollTop();
            if (nowscrollT - oldscrollT > 0) { $(".header").css({ "position": "absolute" }); $(".header").removeClass("pofix"); } else {
                if (!$(".header").hasClass("pofix")) {
                    $(".header").addClass("pofix")
                    $(".header").css({ "position": "fixed", "top": "-93px" }).stop().animate({ "top": 0 }, 300);
                }

            }
            oldscrollT = nowscrollT;
        })
    }
    //产品页导航
    $(function () {
        if ($(".proSname").length > 0) {
            $(".productmainT").wrap('<div class="lingshidiv clearfix" style="height:47px;position:relative;"></div>');
        }
        if ($(".productmainT").length > 0) {
            var nowpro = $(".productmainT").offset().top;
            function scrollpro() {
                var nowscroll = $(window).scrollTop();
                if (nowscroll - nowpro > 0) {
                    $(".productmainT").css({ "position": "fixed" });
                } else {
                    $(".productmainT").css({ "position": "absolute" });
                }
            }
            scrollpro();
            $(window).scroll(function () { scrollpro(); })
        }
    })

    $(".insidechangebtn dl.btns1 dd .btns1M").find("p:last").addClass("last");

    //了解信锐
    $(".companycourse ul li:first").append('<span class="firstshadebox"></span>');
    $(".companycourse ul li:even").addClass("left");
    $(".companycourse ul li:odd").addClass("right");
    var eightli = $(".companycourse ul li:nth-child(1),.companycourse ul li:nth-child(2),.companycourse ul li:nth-child(3),.companycourse ul li:nth-child(4),.companycourse ul li:nth-child(5),.companycourse ul li:nth-child(6),.companycourse ul li:nth-child(7),.companycourse ul li:nth-child(8),.companycourse ul li:last-child");
    eightli.show();
    $(".companycourse ul li .lasticonbox").click(function () {
        if ($(this).hasClass("show")) {
            $(this).removeClass("show");
            $(this).parents("ul").find("li").hide();
            eightli.show();
        } else {
            $(this).addClass("show");
            $(this).parents("ul").find("li").show();
        }
    })

    $(document).on("mouseover", ".succeedaselist ul li", function () {
        $(this).addClass("cur");
        $(this).find(".text1").stop().animate({ "bottom": 60 }, 300);
    })
    $(document).on("mouseout", ".succeedaselist ul li", function () {
        $(this).removeClass("cur");
        $(this).find(".text1").stop().animate({ "bottom": 12 }, 300);
    })
    $(".insidevideolist ul li,.Exhibition dl").hover(function () {
        $(this).addClass("cur");
    }, function () {
        $(this).removeClass("cur");
    })
    $(".companycourse ul li .lasticonbox").hover(function () {
        $(this).addClass("hover");
    }, function () {
        $(this).removeClass("hover");
    })

    //产品试用
    $(".Formfillimg1M .addressSelect label").click(function () {
        $(this).find("select").click();
    })
    $(".MultipleChoice span").click(function () {
        if ($(this).hasClass("cur")) { $(this).removeClass("cur") } else { $(this).addClass("cur"); }
    })
    $(".seekclause .inputbox").click(function () {
        if ($(this).hasClass("cur")) { $(this).removeClass("cur"); } else { $(this).addClass("cur"); }
    })

    //弹窗
    $(".subback").click(function () {
        $(".subback").fadeOut(300);
        $(".popupbody").hide();
    })
    $(".closepopup").click(function () {
        $(".subback").hide();
        $(".popupbody").hide();
    });
    $(".noticepupopbtn").click(function () {
        $(".subback").fadeIn(300);
        $(".popupbody").show();
    })

    //软件更新下载
    $(document).on("click", ".downloadlist dl dd.md5 a", function () {
        var nowtop = $(this).offset().top;
        var nowleft = $(this).offset().left;
        $(".popupbody").css({ "margin-left": 0, "left": nowleft, "top": nowtop - $(document).scrollTop() });
        $(".subback").fadeIn(300);
        $(".popupbody").show();
    })

    //常见问题
    function HWheight() {
        var winH = $(window).height();
        $(".FAQmainleft,.FAQmainright").css({ height: winH - 135 });
        if ($(".FAQmain").length > 0) {
            $("body").css({ "height": winH, "overflow": "hidden" });
        }
    }
    HWheight();
    $(window).resize(function () {
        HWheight();
    })

    $(".Qtwolist li").each(function () {
        if ($(this).hasClass("cur")) {
            $(this).parents("dl").find(".Qtwolist").show();
            $(this).parents("dl").addClass("slideshow");
        }
    })

    $(".FAQmainleft dl dt,.FAQmainleft dl dd .Qonename").click(function () {
        if ($(this).parents("dl").hasClass("slideshow")) {
            $(this).parents("dl").find(".Qtwolist").stop().slideUp(300);
            $(this).parents("dl").removeClass("slideshow");

        } else {
            $(this).parents(".FAQmainleft").find("dl .Qtwolist").stop().slideUp(300);
            $(this).parents(".FAQmainleft").find("dl").removeClass("slideshow");
            $(this).parents("dl").find(".Qtwolist").slideDown(300);
            $(this).parents("dl").addClass("slideshow");

        }
    })

    //联系我们
    $(".insidecontactB ul").each(function () {
        var nowlinum = $(this).find("li").length;
        if (nowlinum % 2 == 0) {
            $(this).find("li").eq(nowlinum - 1).css({ "border-bottom": "none" });
            $(this).find("li").eq(nowlinum - 2).css({ "border-bottom": "none" });
        } else {
            $(this).find("li").eq(nowlinum - 1).css({ "border-bottom": "none" });
        }
    })
})

//右侧浮窗按钮
$(function () {
    $('.return_top').hide();
    function addremovename() {
        if ($(window).scrollTop() > $(window).height() * 0.6) {
            $('.return_top').fadeIn();
            $("#quick_links").parent().addClass("quick_links_allow_gotop")
        } else {
            $('.return_top').fadeOut();
            $("#quick_links").parent().removeClass("quick_links_allow_gotop")
        }
    }
    $(window).scroll(function () {
        addremovename();
    });

    $('.return_top').click(function () {
        $('html,body').animate({ scrollTop: 0 }, 600);
        return false;
    });
    $(".quick_toggle a").click(function () {
        if ($(this).hasClass("show")) {
            $(this).removeClass("show");
            $(this).parents(".quick_links_panel").addClass("quick_links_allow_gotop");
            $(this).parents(".quick_links_wrap").addClass("quick_links_min");
            addremovename();
        } else {
            $(this).addClass("show");
            $(this).parents(".quick_links_panel").removeClass("quick_links_allow_gotop");
            $(this).parents(".quick_links_wrap").removeClass("quick_links_min");
            addremovename();
        }
    })

    function bg1show() {
        $("#ad1animate").animate({ "opacity": 0.2 }, 1000, bg1hide);
    }
    function bg1hide() {
        $("#ad1animate").animate({ "opacity": 1 }, 1000, bg1show);
    }
    $(document).ready(function () {
        bg1hide();

        var _ic = $(".ico_lx");
        _ic.hover(function () {
            var _this = $(this);
            _this.find(".opst").show()
        }, function () {
            var _this = $(this);
            _this.find(".opst").hide()
        })


    });

})

//视频播放
$(function () {

    $(".insidevideolist ul li a,.indexcenterlink ul li a").click(function () {
        $(".recruitmentfloat").height($("body").height());
        var scrolltop = $(window).scrollTop();
        $(".recruitmentfloat").fadeIn(300);

        if ($(window).width() > 1024) {

            var inputval = $(this).attr("videosrc");
            var inputval2 = $(this).attr("videosrc2");
            if (inputval != "") {
                $(".removetvwrap").html('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="100%" height="100%">' +
                          '      <param name="movie" value="/flv_player.swf?flv_url=' + inputval + '&amp;autoplay=1&amp;btn_color=0xcccccc"> ' +
                          '      <param name="BarColor" value="0xffffff" />' +
                          '      <param name="BarTransparent" value="20" />' +
                          '      <param name="quality" value="high"/> ' +
                          '      <param name="allowFullScreen" value="true" /> ' +
                          '      <embed src="/flv_player.swf?flv_url=' + inputval + '&amp;autoplay=1&amp;btn_color=0xcccccc" allowFullScreen="true" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="100%" height="100%"> ' +
                          '      </embed> ' +
                          '    </object>');



                if ($(window).width() > 320 && $(window).width() < 1024) {

                    var inputval = $("#nowvideo").val();
                    $(".removetvwrap").html("<video src=\"" + inputval + "\" controls=\"controls\" width=\"100%\" height=\"100%\"></video>");

                }


                $(".tianjiatvwrap").css("top", (($(window).height() - $(".tianjiatvwrap").height()) / 2) + scrolltop);
            } else {
                if (inputval2 != "") {
                    $(".removetvwrap").html('<iframe height=500 width=1000 src="' + inputval2 + '" frameborder=0 allowfullscreen></iframe>"');

                    $(".tianjiatvwrap").css("top", (($(window).height() - $(".tianjiatvwrap").height()) / 2) + scrolltop);
                }
            }
        };
    });

    $(".recclosebtn").click(function () {
        $(".recruitmentfloat").fadeOut(300, function () {
            $(".removetvwrap").html("");
        });
    });
    $(".scrlldownbtn2").click(function () {
        $("html,body").animate({ scrollTop: $(this).parents(".productmainSbox").outerHeight(true) + $(this).parents(".productmainSbox").offset().top }, 500);
    })

    $(".productmain12M ul.ul2 li").eq(0).addClass("li1");
    $(".insideapproveF ul li:last").addClass("last");


    $(document).on("click", ".insidechangebtn dl.btns1 dd p", function () {
        var nowthis = $(this).index();
        $(this).parents(".insidechangebtn").find("dl:eq(1)").find("dd").hide();
        $(this).parents(".insidechangebtn").find("dl:eq(1)").find("dd").eq(nowthis).show();
    })
    $(document).on("click", ".postselectbtn dl:first dd a", function () {
        $(".postselectbtn dl:eq(1) dd").hide();
        $(".postselectbtn dl:eq(1) dd").eq($(this).index()).show();
    })

    //联系我们
    $(".insidechangebtn dl.btns5 dd a").eq(0).addClass("cur");
    $(".insidecontactB").eq(0).show();
    $(".insidechangebtn dl.btns5 dd a").click(function () {
        $(this).parents("dd").find("a").removeClass("cur");
        $(this).addClass("cur");
        $(".insidecontactB").hide();
        $(".insidecontactB").eq($(this).parent().index()).show();
    })



})
//外部页面添加
$(function () {
    var _a = $(".ca"),
        _b = $(".cb");
    _a.hover(function () {
        $(".box8").find("em").addClass("ba");
        return false;
    }, function () {
        $(".box8").find("em").removeClass("ba");
    })
    _b.hover(function () {
        $(".box8").find("em").addClass("bb");
        return false;
    }, function () {
        $(".box8").find("em").removeClass("bb");
    })
})
//wqp start
$(function () {
    $(".product-series:odd").addClass("odd");

    $(".small-list ul li:eq(0)").addClass("cur");
    var onesrc = $(".small-list ul li:eq(0)").attr("src");
    $(".bigimg img").attr("src", onesrc);
    $(".small-list ul li").each(function (index) {
        $(this).click(function () {
            $(this).addClass("cur").siblings().removeClass("cur");
            var imgsrc = $(this).attr("rel");
            $(".bigimg img").attr("src", imgsrc);

        })
    });

    $(".technical-table table tr:even").addClass("graybg");



    var cars = $(".key-list .key-text");
    for (i = 0; i < cars.length; i++) {
        cars.eq(2 * i + 2).addClass("add");
    }
    //$(".clickparameter").click(function () {
    //    if ($(".technical-table").hasClass("overauto")) {
    //        $(".technical-table").removeClass("overauto");
    //        $(this).html("点击查看完整参数");
    //    }
    //    else {
    //        $(".technical-table").addClass("overauto");
    //        $(this).html("已是完整参数");
    //    }
    //});


    //分享悬浮
    var ShareBottom, shareBtm;
    function ShareScroll() {
        if ($(".detail-con").length > 0) {
            if ($(window).scrollTop() > $(".detail-con").offset().top) {
                ShareBottom = $(window).height() - ($(".load-list").offset().top - $(window).scrollTop()) + 75;
                shareBtm = $(window).height() - $(".Share11").outerHeight();
                if (ShareBottom > shareBtm) {
                    //下拉到一定程度
                    $(".Share11").css({ "position": "fixed", "bottom": ShareBottom, "top": "auto", left: ($(window).width() - $(".detail-con").outerWidth()) / 2 });
                }
                else {
                    //正常情况下

                    $(".Share11").css({ "position": "fixed", "bottom": "auto", "top": 30, left: ($(window).width() - $(".detail-con").outerWidth()) / 2 });

                }

            } else {

                $(".Share11").css({ "position": "absolute", "top": $(".detail-con").offset().top, left: ($(window).width() - $(".detail-con").outerWidth()) / 2 });

            }
        }
    };

    ShareScroll();
    $(window).scroll(function () {
        ShareScroll();
    });

    $(window).resize(function () {
        ShareScroll();
    });


    //悬浮
    /* offHarry = [];
     $(".navigation-item a:eq(0)").addClass("cur");
     $(".scroll").each(function (index, element) {
         var offT = $(this).offset().top;
         offHarry.push(offT);
     });
     $('.navigation-item a').click(function () {
         $(this).addClass('cur').siblings().removeClass('cur');
         var aIn = $(this).index();
         $("body,html").animate({ "scrollTop": offHarry[aIn] });
     });*/


    $(window).load(function () {
        //侧漂导航
        function scroll() {
            var topH1 = $(".Suspendednav").length ? $(".Suspendednav").offset().top : 0;
            var offHarry = [];
            $(".navigation-item a:eq(0)").addClass("cur");
            $(".scroll").each(function (index, element) {
                var offT = $(this).offset().top;
                offHarry.push(offT);
            });

            var prev = $('.navigation-item a.cur').index();
            var next = 0;
            var temp = 0;

            $('.navigation-item a').each(function () {
                var aIn = $(this).index();
                $(this).click(function () {
                    $(this).addClass('cur').siblings().removeClass('cur');
                    prev = $('.navigation-item a.cur').index();
                    next = aIn;
                    if (prev > next) {
                        temp = prev;
                        prev = next;
                        next = temp;
                    }
                    $("body,html").animate({ "scrollTop": offHarry[aIn] - 100 });
                })
            });

            $(window).scroll(function () {
                if ($(window).scrollTop() >= topH1) {
                    $('.Suspendednav').addClass('addfixed');
                } else {
                    $('.Suspendednav').removeClass('addfixed');
                };
                for (var i = 0; i < offHarry.length; i++) {
                    if ($(window).scrollTop() + 100 >= offHarry[i]) {
                        next = i;
                        $('.navigation-item a').eq(i).addClass('cur').siblings().removeClass('cur');
                    };
                };
            })

        }

        scroll();
    })








})