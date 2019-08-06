<!DOCTYPE html>
<!-- <script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?90e83417a76f20e258caeedb02234422";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script> -->

<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?8ce541202d33f84ba5a5ed94499cf558";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>


<head>

    <meta http-equiv="Content-Type" content="text/html; Charset=utf-8">
    <title><?php echo SITE_NAME; ?></title>
    <!-- <title><?php echo $meta_title;  echo SITE_NAME; ?></title> -->
    <meta name="keywords" content="<?php echo $meta_keywords; ?>" />
    <meta name="description" content="<?php echo $meta_description; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />

    <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/base.css" />
    <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/index.css" />
    <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/global.css" />
    <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/layout.css" />
    <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/resetcommon.css" />
    <!-- <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/style1.css" /> -->
    <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/style2.css" />
    <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/bootstrap.css" />
    <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/idangerous.swiper.css" />
    <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/public.css" />

    <!-- <link href="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" /> -->
    <!-- <link href="<?php echo THEME_PATH; ?>admin/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" /> -->
    <link href="<?php echo THEME_PATH; ?>admin/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo HOME_THEME_PATH; ?>layui/css/layui.css" rel="stylesheet" />
    <!-- <link href="<?php echo HOME_THEME_PATH; ?>css/global.css" rel="stylesheet" /> -->
    <script src="<?php echo THEME_PATH; ?>admin/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="template/pc/guangxun/src/js/countUp.js" type="text/javascript"></script>
    <script src="template/pc/guangxun/src/js/my.js" type="text/javascript"></script>
    <script src="template/pc/guangxun/src/js//idangerous.swiper.min.js" type="text/javascript"></script>
    <!-- <script src="<?php echo THEME_PATH; ?>admin/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script> -->
    <!--关键JS开始-->



    <script src="<?php echo HOME_THEME_PATH; ?>layui/layui.js"></script>
    <script src="<?php echo HOME_THEME_PATH; ?>js/global.js"></script>
    <script type="text/javascript">var memberpath = "<?php echo MEMBER_PATH; ?>";</script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/<?php echo SITE_LANGUAGE; ?>.js"></script>
    <link rel="stylesheet" href="<?php echo THEME_PATH; ?>js/ui-dialog.css">
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/dialog-plus.js"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/jquery.artDialog.js?skin=default"></script>
    <script type="text/javascript" src="<?php echo THEME_PATH; ?>js/dayrui.js"></script>
    <!--关键js结束-->
</head>
<body style="background-color: white;">


<div class="header1">
    <div class="top">
            <div class="logo"><a href="/" title="北京华浦科技有限公司"><img src="template/pc/guangxun/src/images/logo.png" alt="北京华浦科技有限公司" title="北京华浦科技有限公司" /></a></div>
            <div class="zi1"><p><i></i>引领智慧城市运营 助力数字中国建设</p>让您悦享生活品质，便捷更多彩!</div>
            <div class="tel">华浦科技服务热线：<span>010-86813333</span></div>
            <div class="menu-handler" id="menu-handler">
                <span class="burger burger-1 trans"></span>
                <span class="burger burger-2 trans-fast"></span>
                <span class="burger burger-3 trans"></span>
            </div>
        </div>
    <div class="w1440">
        <!--隐藏菜单-->
        




        <div class="menuBox">
            <ul class="navMobile">
                <li><h5><a href="/">首页</a></h5></li>
 <?php $list_return = $this->list_tag("action=category module=share pid=0"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
<li style="">

    <!-- <h5><a href="<?php echo $t['url']; ?>"><?php echo $t['name']; ?></a><i></i></h5> -->
    <?php if ($t['id']==1) { ?>
    <h5><a href="/index.php?c=category&id=7"><?php echo $t['name']; ?></a><i></i></h5>
    <?php } else if ($t['id']==2 || $t['id']==3 ||$t['id']==24) { ?>
    <h5><a href="#"><?php echo $t['name']; ?></a><i></i></h5>
    <?php } else if ($t['id']==4) { ?>
    <h5><a href="/index.php?c=category&id=12"><?php echo $t['name']; ?></a><i></i></h5>
    <?php } else if ($t['id']==23) { ?>
    <h5><a href="/index.php?c=category&id=25"><?php echo $t['name']; ?></a><i></i></h5>
    <?php } else { ?>
    <h5><a href="<?php echo $t['url']; ?>"><?php echo $t['name']; ?></a><i></i></h5>
    <?php } ?>
    <div class="listDown">        
    <?php $list_return_c1 = $this->list_tag("action=category module=share pid=$t[id]  return=c1"); if ($list_return_c1) extract($list_return_c1, EXTR_OVERWRITE); $count_c1=count($return_c1); if (is_array($return_c1)) { foreach ($return_c1 as $key_c1=>$c1) {  $is_first=$key_c1==0 ? 1 : 0;$is_last=$count_c1==$key_c1+1 ? 1 : 0; ?>
        <div class="list2_one">
            <h3><a href="<?php echo $c1['url']; ?>"><?php echo $c1['name']; ?></a><i></i></h3>
            <?php $list_return_c2 = $this->list_tag("action=module module=news catid=$c1[id]  return=c2"); if ($list_return_c2) extract($list_return_c2, EXTR_OVERWRITE); $count_c2=count($return_c2); if (is_array($return_c2)) { foreach ($return_c2 as $key_c2=>$c2) {  $is_first=$key_c2==0 ? 1 : 0;$is_last=$count_c2==$key_c2+1 ? 1 : 0; ?>
                <div class="list2_two">
                    <div>
                    <h4><a href="<?php echo $c2['url']; ?>"><?php echo $c2['title']; ?></a></h4>
                    
                    </div>
                             
                </div>
            <?php } } ?>
        </div>
    <?php } } ?>
    </div></li>
<?php } } ?>


            </ul>







        </div>

        <!--隐藏菜单结束-->
<!-- <a href="/" target="_blank" class="logo"><img src="template/pc/guangxun/src/images/logo.png"></a> -->
        
        <div class="header_right">
            
            <div class="header_RB">
                <ul>
                    <li ><a href="/">首页</a></li>
<?php $list_return = $this->list_tag("action=category module=share pid=0"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
<li>
    <?php if ($t['id']==1) { ?>
    <a href="/index.php?c=category&id=7"><?php echo $t['name']; ?></a>
    <?php } else if ($t['id']==2) { ?>
    <a href="#"><?php echo $t['name']; ?></a>
    <?php } else if ($t['id']==4) { ?>
    <a href="/index.php?c=category&id=12"><?php echo $t['name']; ?></a>
    <?php } else if ($t['id']==3) { ?>
    <a href="/index.php?c=category&id=5"><?php echo $t['name']; ?></a>
    <?php } else if ($t['id']==23) { ?>
    <a href="/index.php?c=category&id=25"><?php echo $t['name']; ?></a>
    <?php } else { ?>
    <a href="<?php echo $t['url']; ?>"><?php echo $t['name']; ?></a>
    <?php }  if ($t['name']!='资讯中心' && $t['name']!='关于我们') { ?>
            <?php $list_return_c = $this->list_tag("action=category module=share pid=$t[id]  return=c"); if ($list_return_c) extract($list_return_c, EXTR_OVERWRITE); $count_c=count($return_c); if (is_array($return_c)) { foreach ($return_c as $key_c=>$c) {  $is_first=$key_c==0 ? 1 : 0;$is_last=$count_c==$key_c+1 ? 1 : 0; ?>
            <?php if ($c) { ?>
            <div class="ul_list" style="margin-top:90px;">
            <div class="ul_top">
                <div class="w1440">
                    

                       <div style="float:left;color: white;"><?php echo dr_block('remenchanpin', 1); ?></div>
                       <div style="float:left;margin-left: 20px;"><?php echo dr_block('remenchanpin'); ?></div>
                                                
                </div>
            </div>
            <div class="ul_bo w1440">
                <?php $list_return_c1 = $this->list_tag("action=category module=share pid=$t[id]  return=c1"); if ($list_return_c1) extract($list_return_c1, EXTR_OVERWRITE); $count_c1=count($return_c1); if (is_array($return_c1)) { foreach ($return_c1 as $key_c1=>$c1) {  $is_first=$key_c1==0 ? 1 : 0;$is_last=$count_c1==$key_c1+1 ? 1 : 0; ?>
                                <dl>
                    <dt><a href="<?php echo $c1['url']; ?>"><?php echo $c1['name']; ?></a></dt>
                    <?php $list_return_c2 = $this->list_tag("action=module module=news catid=$c1[id] order=id_desc  return=c2"); if ($list_return_c2) extract($list_return_c2, EXTR_OVERWRITE); $count_c2=count($return_c2); if (is_array($return_c2)) { foreach ($return_c2 as $key_c2=>$c2) {  $is_first=$key_c2==0 ? 1 : 0;$is_last=$count_c2==$key_c2+1 ? 1 : 0; ?>
                    <dd>
                                            <a href="<?php echo $c2['url']; ?>"><?php echo $c2['title']; ?></a>
                                                    
                                            </dd>
                    <?php } } ?>
                </dl>
                <?php } } ?>
                                
                            </div>
        </div>
        <?php } ?>
        <?php } } ?>
        <?php } ?>
    </li>
    <?php } } ?>

                </ul>
            </div>
        </div>
    </div>

        
    </div>
</div>

<script type="text/javascript">
    if (<?php echo $c['tid']=0; ?>) {

    }
    initCommonHeader();
    var MARK = "help";
    if (MARK == ""){ MARK = "index";}
    //    initCommonHeaderKeywords(MARK);
    $(function () {
        if ($("div.topadcs").length) {
            $("div.topadcs").after($("div.ban-ss"));
        }
        $(".main_menu ul li").removeClass("cur");
        $(".main_menu ul li").each(function () {
            $(this).stop().hover(function () {
                $(".erj").css("display", "none");
                $(this).find(".erj").css("display", "block");
            }, function () {
                $(".erj").css("display", "none");
             })
        })
        $(".main_menu ul li a").each(function () {
            if ($(this).attr("href") == window.location.href) {
                $(".main_menu ul li").removeClass("cur");
                $(this).parent().addClass("cur");
                return false;
            }
        })
        var plc = $("div.plc");
        var plc2 = $("div.plc2");
        var pro = $("div.pro_curmbs");
        var tmp = plc.size() ? plc : plc2;
        var location = tmp.size() ? tmp : pro;
        var info = location.find("a");
        var count = info.size();
        for (var i = count - 1; i >= 0; i--) {
            var currenthref = info.eq(i).attr("href");
            var acurt = $(".main_menu ul li a[href*='" + currenthref + "']");
            if (acurt.size()) {
                acurt.parent("li").addClass("cur").siblings("li").removeClass("cur");
                return false;
            }
        }
    })



//     function initCommonHeader()
// {$.get("/ajax.ashx?action=initcommonheader&t="+Math.random(),function(b){var a=gav(b,"showIM");
//     showIM(a);var c=gav(b,"username");if(c.length>0){$("#guest").hide();
//     $("#user").show();$j("commonHeaderGuest").hide();$j("commonHeaderUsername").html(c);
//     $j("commonHeaderUser").fadeIn(80)}})}function showIM(a){if($("#bodd").html()!=""){if(a=="True"){$("#bodd").show();
// $("#kefubtn").hide();$("#divOranIm").show()}else{$("#bodd").hide();
// $("#kefubtn").show();$("#divOranIm").hide()}}}

</script>
<!-- <script type="text/javascript" src="templates/pc/guangxun/src/js/jquery.js"></script> -->
<script type="text/javascript" src="template/pc/guangxun/src/js/jquery.min.js"></script>
<script type="text/javascript" src="template/pc/guangxun/src/js/jquery.SuperSlide.2.1.2.js"></script>
<!-- <script type="text/javascript" src="template/pc/guangxun/src/js/layout.js"></script> -->
<script type="text/javascript" src="template/pc/guangxun/src/js/scrollbar.js"></script>
<script type="text/javascript" src="template/pc/guangxun/src/js/bootstrap.js"></script>


