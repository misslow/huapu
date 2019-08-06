<?php if ($fn_include = $this->_include("header.html")) include($fn_include);  if ($admin['usermenu']) { ?>
<div class="row">
    <div class="col-md-12">
        <div class="admin-usermenu">
            <?php if (is_array($admin['usermenu'])) { $count=count($admin['usermenu']);foreach ($admin['usermenu'] as $t) { ?>
            <a class="btn <?php if ($t['color'] && $t['color']!='default') {  echo $t['color'];  } else { ?>btn-default<?php } ?>" <?php if ($t['target']) { ?> target="_blank" <?php } ?> href="<?php echo $t['url']; ?>"> <?php echo $t['name']; ?> </a>
            <?php } } ?>
        </div>
    </div>
</div>
<?php } ?>

<div class="row">

    <div class="col-md-6 col-sm-6">

        <div class="portlet light bordered myportlet ">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="fa fa-cog"></i>
                    <span class="caption-subject"> <a style="color:#666" href="<?php echo dr_url('cloud/index'); ?>"><?php echo dr_lang('程序信息'); ?></a> </span>
                </div>
            </div>
            <div class="portlet-body">

                <ul class="use-info" style="margin:-10px 0 30px 10px;height: 130px;">
                    <li>
                        <span>框架版本：</span>
                        <a target="_blank" href="http://www.tpcmf.com/">v<?php echo $cmf_version; ?></a>
                        <a id="dr_cmf_update" href="<?php echo dr_url('cloud/update'); ?>" style="margin-left: 10px;display: none" class="badge badge-danger badge-roundless">  </a>
                    </li>

                    <li>
                        <span>漏洞上报：</span>
                        <a>tpcmf@qq.com</a>
                    </li>


                    <li>
                        <span>官方网站：</span>
                        <a><a target="_blank" href="http://www.tpcmf.com/" style="margin-right: 10px;">www.tpcmf.com</a>
                    </li>

                    <li>
                        <span>技术支持：</span>

                        <a target="_blank" href="http://help.phpcmf.net/" style="margin-right: 10px;">使用文档</a>
                        <a target="_blank" href="http://www.wendacms.com/tpcmf/" style="margin-right: 10px;">产品论坛</a>
                        <a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=f0fbe4240aec09f99a7949701235c8cde6809f96ad9ee44775da41418632a105" style="margin-right: 10px;">用户QQ群</a>
                    </li>

                </ul>
            </div>
        </div>

        <div class="portlet light bordered myportlet">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="fa fa-bar-chart"></i>
                    <span class="caption-subject"> <?php echo dr_lang('数据统计'); ?> </span>
                </div>
            </div>
            <div class="portlet-body">
                <?php


		$mtotal = [];
		$module = \Phpcmf\Service::C()->get_cache('module-'.SITE_ID.'-content');
                if ($module) {
                foreach ($module as $dir => $t) {
                // 判断权限
                $mtotal[$dir] = [
                'name' => dr_lang($t['name']),
                'today' => \Phpcmf\Service::M('auth')->_menu_link_url($dir.'/home/index', $dir.'/home/index'),
                'all' => \Phpcmf\Service::M('auth')->_menu_link_url($dir.'/home/index', $dir.'/home/index'),
                'verify' => \Phpcmf\Service::M('auth')->_menu_link_url($dir.'/verify/index', $dir.'/verify/index'),
                'recycle' => \Phpcmf\Service::M('auth')->_menu_link_url($dir.'/home/index', $dir.'/recycle/index'),
                'timing' => \Phpcmf\Service::M('auth')->_menu_link_url($dir.'/home/index', $dir.'/time/index'),
                ];
                }
                }
                ?>
                <div class="table-scrollable">
                    <table class="table table-mtotal table-nomargin table-bordered table-striped table-bordered table-advance">
                        <thead>
                        <tr>
                            <th><?php echo dr_lang('模块'); ?></th>
                            <th><?php echo dr_lang('总数据'); ?></th>
                            <th><?php echo dr_lang('今日'); ?></th>
                            <th><?php echo dr_lang('待审核'); ?></th>
                            <th><?php echo dr_lang('待发布'); ?></th>
                            <th><?php echo dr_lang('回收站'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (is_array($mtotal)) { $count=count($mtotal);foreach ($mtotal as $dir=>$t) { ?>
                        <tr>
                            <td><?php echo $t['name']; ?></td>
                            <td><a class="drlabel drlabel-success" href="<?php echo $t['all']; ?>" id="<?php echo $dir; ?>_all"><img src="<?php echo THEME_PATH; ?>assets/images/mloading.gif"></a></td>
                            <td><a class="drlabel drlabel-success" href="<?php echo $t['today']; ?>" id="<?php echo $dir; ?>_today"><img src="<?php echo THEME_PATH; ?>assets/images/mloading.gif"></a></td>
                            <td><a class="drlabel drlabel-important" href="<?php echo $t['verify']; ?>" id="<?php echo $dir; ?>_verify"><img src="<?php echo THEME_PATH; ?>assets/images/mloading.gif"></a></td>
                            <td><a class="drlabel drlabel-important" href="<?php echo $t['timing']; ?>" id="<?php echo $dir; ?>_timing"><img src="<?php echo THEME_PATH; ?>assets/images/mloading.gif"></a></td>
                            <td><a class="drlabel drlabel-important" href="<?php echo $t['recycle']; ?>" id="<?php echo $dir; ?>_recycle"><img src="<?php echo THEME_PATH; ?>assets/images/mloading.gif"></a></td>
                        </tr>
                        <script type="text/javascript">
                            $(function(){
                                $.getScript("<?php echo dr_url('api/mtotal'); ?>&dir=<?php echo $dir; ?>");
                            });
                        </script>
                        <?php } } ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>



    </div>

    <div class="col-md-6 col-sm-6">



        <div class="portlet light bordered myportlet">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="fa fa-bell"></i>
                    <span class="caption-subject"> <a style="color:#666" href="<?php echo dr_url('notice/my_index'); ?>"><?php echo dr_lang('系统提醒'); ?></a> </span>
                </div>
            </div>
            <div class="portlet-body">

                <div class="scroller" style="height: 130px;">
                    <?php $notice = \Phpcmf\Service::M('auth')->admin_notice(12); if (!$notice) { ?>
                    <div style="padding-top: 30px;padding-bottom: 30px;text-align: center;color: #d7d8da;"> <?php echo dr_lang('无任何提醒'); ?> </div>
                    <?php } else { ?>
                    <ul class="feeds" style="padding-bottom: 20px">
                        <?php if (is_array($notice)) { $count=count($notice);foreach ($notice as $t) { ?>
                        <li>
                            <div class="col1" style="padding-top: 2px;padding-left: 3px;">
                                <div class="cont">
                                    <div class="cont-col1 user-avatar">
                                        <a href="<?php echo dr_url('api/notice', array('id' => $t['id'])); ?>"><img style="height: 25px!important;" src="<?php echo dr_avatar($t['uid']); ?>" /></a>
                                    </div>
                                    <div class="cont-col2">
                                        <div class="desc"><a href="<?php echo dr_url('api/notice', array('id' => $t['id'])); ?>"><?php echo $t['msg']; ?></a></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col2">
                                <div class="date"> <?php echo dr_fdate($t['inputtime']); ?> </div>
                            </div>
                        </li>
                        <?php } } ?>
                    </ul>
                    <?php } ?>

                </div>
            </div>




    </div>

        <div class="portlet light bordered myportlet">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="fa fa-cloud"></i>
                    <span class="caption-subject"> <a style="color:#666" href="http://www.tpcmf.com/news/"><?php echo dr_lang('官方公告'); ?></a> </span>
                </div>
            </div>
            <div class="portlet-body" id="dr_tpcmf_news">

                <div style="padding-top: 30px;padding-bottom: 30px;text-align: center;color: #d7d8da;"> ... </div>

            </div>

    </div>



</div>

<script>
    $(function () {

        $.ajax({type: "GET",dataType:"json", url: "<?php echo dr_url('api/version_cmf'); ?>",
            success: function(json) {
                if (json.code) {
                    $('#dr_cmf_update').show();
                    $('#dr_cmf_update').html('<?php echo dr_lang('升级'); ?> v'+json.msg);
                }
            }
        });

        $.ajax({type: "GET",dataType:"jsonp", url: "http://www.tpcmf.com/news.php",
            success: function(json) {
                if (json.code) {
                    $('#dr_tpcmf_news').html(json.msg);
                }
            }
        });
    });
</script>

<?php if ($fn_include = $this->_include("footer.html")) include($fn_include); ?>