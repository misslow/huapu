
<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>走进华浦 - 华浦科技</title>
    <meta name="keywords" content="关键词" />
    <meta name="description" content="描述" />
    <!-- <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/public.css" /> -->
    <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/swiper.min.css" />
    <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/erji.css" />
    <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/iconfont.css" />
    <!-- <link type="text/css" rel="stylesheet" href="template/pc/guangxun/src/css/font.css" /> -->
    <!-- [if IE 7]>
    <link type="text/css" rel="stylesheet" href="/statics/default/css/ie7.css" />
    <![endif] -->

<script type="text/javascript" src="template/pc/guangxun/src/js/swiper.min.js"></script>
<script type="text/javascript" src="template/pc/guangxun/src/js/bootstrap.js"></script>
    <?php $list_return_b = $this->list_tag("action=category module=share id=$catid num=1  return=b"); if ($list_return_b) extract($list_return_b, EXTR_OVERWRITE); $count_b=count($return_b); if (is_array($return_b)) { foreach ($return_b as $key_b=>$b) {  $is_first=$key_b==0 ? 1 : 0;$is_last=$count_b==$key_b+1 ? 1 : 0; ?>
<?php $list_return_b1 = $this->list_tag("action=category module=share id=$b[pid] num=1  return=b1"); if ($list_return_b1) extract($list_return_b1, EXTR_OVERWRITE); $count_b1=count($return_b1); if (is_array($return_b1)) { foreach ($return_b1 as $key_b1=>$b1) {  $is_first=$key_b1==0 ? 1 : 0;$is_last=$count_b1==$key_b1+1 ? 1 : 0; ?>
<section class="nr-banner a-background" style="background-image: url(<?php echo dr_thumb($b1['thumb']); ?>);">
        <div class="width-1220">

            <h1 class="wow fadeInUpa"><?php echo $b1['name']; ?></h1>

            <p class="wow fadeInUpa" data-wow-delay=".2s"><?php echo dr_clearhtml($b1['content']); ?></p>
<?php } } ?>
<?php } } ?>

        </div>
    </section>

    <!--二级导航-->
    <section id="nrnav-box">
        <div class="hxjs-nav" id="product">
        <div class="hxjs-nav1" style="position: relative;">
            <?php $list_return_c = $this->list_tag("action=category module=share id=$catid num=1  return=c"); if ($list_return_c) extract($list_return_c, EXTR_OVERWRITE); $count_c=count($return_c); if (is_array($return_c)) { foreach ($return_c as $key_c=>$c) {  $is_first=$key_c==0 ? 1 : 0;$is_last=$count_c==$key_c+1 ? 1 : 0; ?>

            <?php $list_return_c1 = $this->list_tag("action=category module=share id=$c[pid] num=1  return=c1"); if ($list_return_c1) extract($list_return_c1, EXTR_OVERWRITE); $count_c1=count($return_c1); if (is_array($return_c1)) { foreach ($return_c1 as $key_c1=>$c1) {  $is_first=$key_c1==0 ? 1 : 0;$is_last=$count_c1==$key_c1+1 ? 1 : 0; ?>
            <div class="hxjs-nav2"><span style="padding-right: .2rem;"><?php echo $c1['name']; ?></span>
                <!-- <span class="glyphicon glyphicon-menu-down"></span> -->
                <span class="mui-icon mui-icon-arrowdown"></span>
            </div>
            <?php } } ?>
            <?php } } ?>
            <div class="hxjs-nav3">
                <div class="box_Arrow"></div>
                <?php $list_return_c = $this->list_tag("action=category module=share id=$catid num=1  return=c"); if ($list_return_c) extract($list_return_c, EXTR_OVERWRITE); $count_c=count($return_c); if (is_array($return_c)) { foreach ($return_c as $key_c=>$c) {  $is_first=$key_c==0 ? 1 : 0;$is_last=$count_c==$key_c+1 ? 1 : 0; ?>

                <?php $list_return_c1 = $this->list_tag("action=category module=share id=$c[pid] num=1  return=c1"); if ($list_return_c1) extract($list_return_c1, EXTR_OVERWRITE); $count_c1=count($return_c1); if (is_array($return_c1)) { foreach ($return_c1 as $key_c1=>$c1) {  $is_first=$key_c1==0 ? 1 : 0;$is_last=$count_c1==$key_c1+1 ? 1 : 0; ?>
                
                <?php $list_return_c2 = $this->list_tag("action=category module=share pid=$c1[id]  return=c2"); if ($list_return_c2) extract($list_return_c2, EXTR_OVERWRITE); $count_c2=count($return_c2); if (is_array($return_c2)) { foreach ($return_c2 as $key_c2=>$c2) {  $is_first=$key_c2==0 ? 1 : 0;$is_last=$count_c2==$key_c2+1 ? 1 : 0; ?>
                
                <?php if ($key==0) { ?>
                <a href="<?php echo $c2['url']; ?>" class="hover"><?php echo $c2['name']; ?></a>
                <?php } else { ?>
                <a href="<?php echo $c2['url']; ?>"><?php echo $c2['name']; ?></a>
                <?php } ?>
                <?php } } ?>
                <?php } } ?>
                <?php } } ?>
            </div>
        </div>
        <p><?php echo $c['name']; ?> </p>
        </div>
    </section>

    <!--第二个banner-->
    <section class="hxjs-banner m-center">
        <p class="wow fadeInUpa delay-2 width-1220"><i class="sensetime sensetime-renlianyurentifenxijishu"></i><?php echo $c['name']; ?></p>
        <div class="width-1220 Technology_banner  a-background" style="background-image: url('<?php echo dr_thumb($c['thumb']); ?>');"></div>
    </section>

    <section>
        <div class="width-100 hxjs-menu">
            <!-- <h1 class="hxjs-title1 width-1220">技术能力</h1> -->
        </div>  
        
        <div class="width-1220 hxjs-xg-1">
        <!--标题-->
            <div class="hxjs-menu-box" id="hxjsMenu">

                <?php $return = [];$list_return = $this->list_tag("action=module catid=$catid order=id_desc"); if ($list_return) { extract($list_return); $count=count($return);} if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0;  if ($key==0) { ?>
                         <div class="hxjs-menu-item active"><?php echo $t['title']; ?></div>
                        <?php } else { ?>
                        <div class="hxjs-menu-item"><?php echo $t['title']; ?></div>
                        <?php }  } } ?>

                
            </div>
            
                    <div class="hxjs-con-box" id="hxjsCon">
            <div class="swiper-wrapper">


                <?php $return = [];$list_return = $this->list_tag("action=module catid=$catid order=id_desc"); if ($list_return) { extract($list_return); $count=count($return);} if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                
                <div class="swiper-slide hxjs-con-item">
                    
                    <div class="hxjs-con-left width-50">
                        <div>
                            <h2 class="wo delay-5"><a href="javascript:void(0);" onclick="window.location.href='<?php echo $t['url']; ?>'"><?php echo $t['title']; ?></a></h2>
                            <!-- <h2 class="wo delay-5"><a href='<?php echo $t['url']; ?>'><?php echo $t['title']; ?></a></h2> -->
                            <p class="wo delay-7"><?php echo $t['description']; ?></p>
                        </div>
                    </div>

                    <!-- <?php $img=dr_thumb($t['thumb']); ?> -->
                    <?php if ($t['thumb']) { ?>
                    <div class="width-50 a-background" style="background-image: url('<?php echo dr_thumb($t['thumb']); ?>');"></div>
                    <?php } else { ?>
                    <div class="width-50 a-background" style="background-image: url('<?php echo SITE_URL; ?>template/pc/guangxun/src/images/Technology_autodrive10.jpg');"></div>
                    <?php } ?>
                </div>
                <?php } } ?>   

            </div>



        </div>
            
            
        </div>  
            
            
        
<script>
        $(function(){
            //二级导航下拉
                        $('.hxjs-nav1').click(function(){
                            event.stopPropagation();
                                if($(this).hasClass('active')){
                                        $(this).find('.hxjs-nav3').slideUp();
                                        $(this).removeClass('active')
                                }else{
                                        $(this).find('.hxjs-nav3').slideDown()
                                        $(this).addClass('active')
                                }
                        });
                        $('body').on('click',function(){
                            $('.hxjs-nav1').removeClass('active');
                            $('.hxjs-nav1').find('.hxjs-nav3').slideUp();
                        })
                        //固定下拉菜单
                        var headerHeight= $('header').outerHeight();//获得header高度外部高度

            var nrnavHeight = $('#nrnav-box').outerHeight();//获得nrnav-boxr高度外部高度
            var nrnavTop = $('#nrnav-box').offset().top;//获得nrnav-boxr高度上部高度
            $(window).scroll(function(){
                            if($(window).scrollTop()> nrnavTop){
                                $('header').addClass("headeractive")
                            } else {
                                $('header').removeClass('headeractive');
                            }
                                if($(window).scrollTop()>nrnavTop){
                                        $('#nrnav-box').addClass('hxjs-nav_active');
                                        $('#nrnav-box').css('top',0)
                }else{
                    $('#nrnav-box').removeClass('hxjs-nav_active');
                    $('#nrnav-box').css('top',0)
                };
            })
                        //
        })
    </script>






        <script>
             var hxjsCon = new Swiper('#hxjsCon', {
                autoHeight: true, //高度随内容变化
                effect : 'fade',
                onSlideChangeStart: function(swiper){
                    $('#hxjsMenu').find('.hxjs-menu-item').removeClass('active');
                    $('#hxjsMenu').find('.hxjs-menu-item').eq(swiper.activeIndex).addClass('active');

                },
                onSlideChangeEnd: function(swiper){
                    $('#hxjsCon').find('.swiper-slide').find('.wo').removeClass('zhixing');
                    $('#hxjsCon').find('.swiper-slide').eq(swiper.activeIndex).find('.wo').addClass('zhixing');
                },
            });
            $('#hxjsMenu').find('.hxjs-menu-item').each(function(i){
                $(this).click(function(){
                    $('#hxjsMenu').find('.hxjs-menu-item').removeClass('active');
                    $(this).addClass('active');
                    hxjsCon.slideTo(i, 500, true);
                })
            });
        </script>


    </section>


    



<!-- <script type="text/javascript" src="/statics/default/js/jquery.js"></script> -->
<!-- <script type="text/javascript" src="template/pc/guangxun/src/js/jquery.min.js"></script> -->
<script type="text/javascript" src="template/pc/guangxun/src/js/wow.js"></script>
<script type="text/javascript" src="template/pc/guangxun/src/js/js.js"></script>
<script type="text/javascript" src="template/pc/guangxun/src/js/scrollBar.js"></script>
<!-- <script type="text/javascript" src="/statics/default/js/jquery.SuperSlide.2.1.2.js"></script> -->
<!-- <script type="text/javascript" src="template/pc/guangxun/src/js/layout.js"></script> -->

<script type="text/javascript">
 //JS代码部分
 $(function(){
     $(".content .cleft").find("li").hover(function(){
        $(this).find("p").css("color","#217ac6");
     },function(){
         $(this).find("p").css("color","#333");
     })
 })
</script>


<!--[if IE 6]>
<script type="text/javascript" src="/statics/default/js/DD_belatedPNG.js"></script>
<script type="text/javascript">
    DD_belatedPNG.fix(".");
</script>
<![Endif]-->
<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>