
<?php if ($fn_include = $this->_include("header.html")) include($fn_include); ?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>华浦科技</title>
    <meta name="keywords" content="华浦科技" />
    <meta name="description" content="华浦科技" />
    

    <!--[if IE 7]>
    <link type="text/css" rel="stylesheet" href="/statics/default/css/ie7.css" />
    <![endif]-->
</head>

<!-- <script src="http://siteapp.baidu.com/static/webappservice/uaredirect.js" type="text/javascript"></script> -->
<script type="text/javascript">uaredirect("http://m.tpcmf.com","http://www.tpcmf.com");</script>


<!-- // 下面调用标题
<?php echo dr_block('huandengpian', 1); ?>
// 下面调用内容
<?php $block=dr_block('huandengpian');  if (is_array($block['file'])) { $count=count($block['file']);foreach ($block['file'] as $i=>$file) { ?>
文件地址: <?php echo dr_get_file($file); ?>
文件标题: <?php echo $block['title'][$i];  } } ?>
 -->
<!-- <div class="banner0">
    <div class="bimg">
	<ul>
        <?php $block=dr_block('lunbo');  if (is_array($block['file'])) { $count=count($block['file']);foreach ($block['file'] as $i=>$file) { ?>
        <li style="background: url('<?php echo dr_get_file($file); ?>') center center;"> </li>    
        <?php } } ?>
        </ul>
    </div>
    <div class="hd clearfix">
        <ul>
		<li></li><li></li><li></li>           
        </ul>
    </div>
</div> -->

<div class="banner">
    <div class="swiper-container">
        <div class="swiper-wrapper">
        <?php $block=dr_block('lunbo');  if (is_array($block['file'])) { $count=count($block['file']);foreach ($block['file'] as $i=>$file) { ?>
        <div class="swiper-slide"><img src="<?php echo dr_get_file($file); ?>"></div> 
        <?php } } ?>
        </div>
    </div>
    <div class="pagination"></div>
</div>

<div class="inA">
    <div class="w1440">
        <h4 class="in_tit">产品解决方案</h4>
        <div class="inA_con">
                        <a href="/index.php?c=category&id=12" class="images">
                <img src="template/pc/guangxun/src/images/1531235258700336_1.jpg">
                <span>智能城市</span>
            </a>
                        <a href="/index.php?c=category&id=13" class="images">
                <img src="template/pc/guangxun/src/images/1531235395281117_1.jpg">
                <span>智能制造</span>
            </a>
                        <a href="/index.php?c=category&id=16" class="images">
                <img src="template/pc/guangxun/src/images/1531235667307944.jpg">
                <span>智能人居</span>
            </a>
                        <a href="/index.php?c=category&id=17" class="images">
                <img src="template/pc/guangxun/src/images/1531235488718326.jpg">
                <span>智能生活</span>
            </a>
                        <a href="/index.php?c=category&id=18" class="images">
                <img src="template/pc/guangxun/src/images/1531235557644583.jpg">
                <span>智能园区</span>
            </a>
                        <a href="/index.php?c=category&id=19" class="images">
                <img src="template/pc/guangxun/src/images/1531235721464925.jpg">
                <span>农林牧渔</span>
            </a>
                        <a href="/index.php?c=category&id=20" class="images">
                <img src="template/pc/guangxun/src/images/1531235609849556.jpg">
                <span>物联创新</span>
            </a>
        </div>
    </div>
</div>

<section>
    <div class="index-bt index_about_info">
        <h1 class=" wow fadeInUp">
            资讯中心<br class="visible-xs"><!-- 让 AI 引领人类进步 -->
        </h1>

        <!-- <p class=" wow fadeInUp">致力于研发创新人工智能技术，为经济、社会和人类发展做出积极的贡献。</p> -->
        <!-- <a href="/index.php?c=category&id=10" class="btn-ljgd btn-ljgd2  wow fadeInUp" data-wow-delay="0.4s">了解更多<span></span></a> -->
    </div>
</section>
<section>
    <div class="width-1220 index_block  wow fadeInUpa">

        <div class="index_block_box">
            <div class="index_block_top">
                <div class="class_name">成长之路</div>
                <a href="/index.php?c=category&id=10">
                    <div class="class_Arrow">
                        <span>了解更多</span>
                    <img src="template/pc/guangxun/src/images/index_block_Arrow.png" alt="">
                    </div>
                </a>
                <img src="template/pc/guangxun/src/images/index_block1.jpg" alt="" class="index_block_img img-responsive">
            </div>
            <div class="index_block_bottom">
                <?php $list_return = $this->list_tag("action=module module=news catid=10 num=0,2"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <li>
                    <a href="<?php echo $t['url']; ?>"><div class="news_title"><?php echo $t['title']; ?></div></a>
                    <div class="index_block_data"><?php echo $t['inputtime']; ?></div>
                </li>
                <?php } } ?>
                          </div>
        </div>

        <div class="index_block_box">
            <div class="index_block_top">
                <div class="class_name">前沿探索</div>
                <a href="/index.php?c=category&id=10">
                    <div class="class_Arrow">
                        <span>了解更多</span>
                    <img src="template/pc/guangxun/src/images/index_block_Arrow.png" alt="">
                    </div>
                </a>
                <img src="template/pc/guangxun/src/images/index_block2.jpg" alt="" class="index_block_img img-responsive">
            </div>
            <div class="index_block_bottom">
                <?php $list_return = $this->list_tag("action=module module=news catid=10 num=2,2"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <li>
                        <a href="<?php echo $t['url']; ?>"><div class="news_title"><?php echo $t['title']; ?></div></a>
                        <div class="index_block_data"><?php echo $t['inputtime']; ?></div>
                </li>
                <?php } } ?>
                              </div>
        </div>

        <div class="index_block_box">
            <div class="index_block_top">
                <div class="class_name">行业赋能</div>
                <a href="/index.php?c=category&id=10">
                    <div class="class_Arrow">
                        <span>了解更多</span>
                    <img src="template/pc/guangxun/src/images/index_block_Arrow.png" alt="">
                    </div>
                </a>
                <img src="template/pc/guangxun/src/images/index_block3.jpg" alt="" class="index_block_img img-responsive">
            </div>
            <div class="index_block_bottom">
                
                <?php $list_return = $this->list_tag("action=module module=news catid=10 num=4,2"); if ($list_return) extract($list_return, EXTR_OVERWRITE); $count=count($return); if (is_array($return)) { foreach ($return as $key=>$t) { $is_first=$key==0 ? 1 : 0;$is_last=$count==$key+1 ? 1 : 0; ?>
                <li>
                        <a href="<?php echo $t['url']; ?>"><div class="news_title"><?php echo $t['title']; ?></div></a>
                        <div class="index_block_data"><?php echo $t['inputtime']; ?></div>
                </li>
                <?php } } ?>

                         </div>
        </div>
    </div>
</section>




<!-- <script type="text/javascript" src="templates/pc/guangxun/src/js/NSW_Details.js"></script> -->
<!-- <script type="text/javascript" src="templates/pc/guangxun/src/js/MobileRewrite.js"></script> -->

<script type="text/javascript">
 //JS代码部分
 jQuery(".banner0").slide({titCell:".hd ul",mainCell:".bimg ul",autoPage:true,effect:"leftLoop",autoPlay:true,interTime:6000});
 jQuery(".company").slide({titCell:".hd ul",mainCell:".category ul",autoPage:true,effect:"left",vis:4});
    //产品中心content01
 $(function(){
     $(".content01 .cleft").find("li").hover(function(){
         $(this).find(".cpinfo2").slideDown();
     },function(){
         $(this).find(".cpinfo2").hide();
     })
     $(".footer .footbt .fbright").find("li").hover(function(){
         $(this).find(".wxtc").show();
     },function(){
         $(this).find(".wxtc").hide();
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