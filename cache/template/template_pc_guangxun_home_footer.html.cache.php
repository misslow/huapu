<!-- 底部 -->

<div style="">
        <hr width="100%"/><br />


</div><br />


<div class="footer">
    <div class="w1440">
        <div class="footop">
            <!-- <div style="margin-left: 120px;"> -->
            <div style="">
<?php $list_return_c = $this->list_tag("action=category module=share pid=0 num=6  return=c"); if ($list_return_c) extract($list_return_c, EXTR_OVERWRITE); $count_c=count($return_c); if (is_array($return_c)) { foreach ($return_c as $key_c=>$c) {  $is_first=$key_c==0 ? 1 : 0;$is_last=$count_c==$key_c+1 ? 1 : 0; ?>
<dl>
    <?php if ($c['name']=='资讯中心' | $c['name']=='关于我们') { ?>
    <dt><a href="<?php echo $c['url']; ?>" style="color: #004599"><?php echo $c['name']; ?></a></dt>
    <?php } else { ?>
    <dt><?php echo $c['name']; ?></dt>
    <?php } ?>
    <dd>
        
                        <?php $list_return_c1 = $this->list_tag("action=category module=share pid=$c[id]  return=c1"); if ($list_return_c1) extract($list_return_c1, EXTR_OVERWRITE); $count_c1=count($return_c1); if (is_array($return_c1)) { foreach ($return_c1 as $key_c1=>$c1) {  $is_first=$key_c1==0 ? 1 : 0;$is_last=$count_c1==$key_c1+1 ? 1 : 0; ?>
                        
                        <a href="<?php echo $c1['url']; ?>"><?php echo $c1['name']; ?></a>
                        
                        <?php } } ?>
                        
                       
                        
                

            </dd>
</dl>
<?php } } ?>
</div>

<!-- <div ></div> -->

        <div class="footertopR" style="border-left:1px dashed #000;margin-right: 2px;">
                <h4 style="margin-top: 20px;padding-left: 30px">24小时服务热线</h4>
                <div style="margin-top: 20px;padding-left: 30px"><span class="num">售前：400-878-3313</span></div>
                <div>&nbsp;</div>
                <div style="margin-top: 20px;padding-left: 30px"><span class="num">售后：400-878-3389</span></div>
                <p>&nbsp;</p>
                <div class="footattention clearfix" >
                    <dl class="clearfix1" style="width: 250px">
                        <dt class="name" >关注我们：</dt>
                        <!-- <dd class="btn1"><a target="_blank" href="http://weibo.com/u/5033148547"> -->
                        <dd class="btn1"><a target="_blank" href="#">
                            <img class="img1" src="template/pc/guangxun/src/images/footicon-01.png" /><img class="img2" src="template/pc/guangxun/src/images/footicon-01a.png" /></a></dd>
                        <dd class="btn2"><a target="_blank" href="javascript:;" style="overflow:overflow ">
                            <img class="img1" src="template/pc/guangxun/src/images/footicon-02.png" /><img class="img2" src="template/pc/guangxun/src/images/footicon-02a.png" /></a>
                            <div class="EWMbox">微信扫一扫，关注我们<img src="template/pc/guangxun/src/images/EWM.jpg"/></div>

                        </dd>

                        
                    </dl>
                </div>
                    
            </div>

            
        </div>
        <div class="footen">
            <p> © 2018北京华浦科技有限公司 版权所有. &nbsp;&nbsp;<a   href="http://www.beian.miit.gov.cn/" target="_blank">京ICP备  13011831号&nbsp;&nbsp;</a><!-- <script id="jsgovicon"   src="http://odr.jsdsgsxt.gov.cn:8081/mbm/app/main/electronic/js/go  vicon.js?  siteId=3a1614eed2ee4a1783f113c1d1c9bfd6&width=32&height=45&type=1"   type="text/javascript" charset="utf-8"></script> --> &nbsp;&nbsp;</p>
            <!-- <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1274656933'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s22.cnzz.com/stat.php%3Fid%3D1274656933%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script> -->
            <script type="text/javascript" src="https://s4.cnzz.com/z_stat.php?id=1277865614&web_id=1277865614"></script>
            <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? "https://" : "http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1277865614'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s4.cnzz.com/z_stat.php%3Fid%3D1277865614%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script>
            <!-- <div>
                                <a href="#">网站地图</a>
                                <a href="#">法律声明</a>
                                <a href="#">联系我们</a>
                            </div> -->
        </div>
    </div>
</div>

<script>
    var mySwiper1 = new Swiper('.banner .swiper-container', {
        pagination: '.banner .pagination',
        loop: true,
        grabCursor: true,
        speed:1000,
        autoplay:4000,
        paginationClickable: true
    })
    var mySwiper = new Swiper('.inB_L .swiper-container', {
        loop: true,
        grabCursor: true,
        paginationClickable: true
    })
    $('.arrow-left').on('click', function (e) {
        e.preventDefault()
        mySwiper.swipePrev()
    })
    $('.arrow-right').on('click', function (e) {
        e.preventDefault()
        mySwiper.swipeNext()
    })

    var options = {
        useEasing: true,
        useGrouping: true,
        separator: ',',
        decimal: '.',
        prefix: '',
        suffix: ''
    };
    var demo = new CountUp("num", 0, 2003, 0, 5, options);
    var demo1 = new CountUp("num1", 0, 2000, 0, 5, options);
    var demo2 = new CountUp("num2", 0, 22, 0, 5, options);
    var demo3 = new CountUp("num3", 2, 8, 0, 5, options);
    demo.start();
    demo1.start();
    demo2.start();
    demo3.start();
</script>

<script type="text/javascript">
    
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


</script>

<!--侧边导航-->

<div class="blog-share layui-hide">
    <div class="blog-share-body">
        <div style="width: 200px;height:100%;">
            <div class="bdsharebuttonbox">
                <a class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
                <a class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
                <a class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>
                <a class="bds_sqq" data-cmd="sqq" title="分享到QQ好友"></a>
            </div>
        </div>
    </div>
</div>

</body>
</html>