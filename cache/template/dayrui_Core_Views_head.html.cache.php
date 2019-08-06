<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="zh">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8" />
    <title><?php if ($meta_title) {  echo $meta_title;  } else {  echo dr_lang('%s - 后台管理平台', SITE_NAME);  } ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <link href="<?php echo THEME_PATH; ?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>assets/global/css/common.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>assets/global/css/components-rounded.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>assets/layouts/layout/css/layout.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>assets/layouts/layout/css/themes/default.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo THEME_PATH; ?>assets/layouts/layout/css/custom.css" rel="stylesheet" type="text/css" />
    <!--[if lt IE 9]>
    <script src="<?php echo THEME_PATH; ?>assets/global/plugins/respond.min.js"></script>
    <script src="<?php echo THEME_PATH; ?>assets/global/plugins/excanvas.min.js"></script>
    <![endif]-->
    <script type="text/javascript">
        var admin_file = '<?php echo SELF; ?>';
        var assets_path = '<?php echo THEME_PATH; ?>assets/';
        var is_mobile_cms = '<?php echo $is_mobile; ?>';
        var is_admin = 1;
    </script>
    <script src="<?php echo LANG_PATH; ?>lang.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/layer/layer.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/global/scripts/app.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/layouts/layout/scripts/layout.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/js/cms.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/js/admin.js" type="text/javascript"></script>
    <script src="<?php echo THEME_PATH; ?>assets/js/jquery.cookie.js" type="text/javascript"></script>
    <script type="text/javascript">
    <?php if (isset($_GET['isback']) && $_GET['isback']) { ?>
        $(function(){
            dr_tips(<?php echo intval($_GET['iscode']); ?>, "<?php echo $_GET['isback']; ?>")
        });
    <?php } ?>
        function dr_update_cache_all() {
            var index = layer.load(2, {
                shade: [0.3,'#fff'], //0.1透明度的白色背景
                time: 10000
            });
            $.ajax({type: "GET",dataType:"json", url: admin_file+"?c=api&m=cache_update",
                success: function(json) {
                    layer.close(index);
                    dr_tips(1, "<?php echo dr_lang('缓存更新完成'); ?>");
                },
                error: function(HttpRequest, ajaxOptions, thrownError) {
                    layer.closeAll('loading');
                    dr_tips(0, "<?php echo dr_lang('更新失败'); ?>");
                }
            });
        }
        function dr_update_cache_data() {
            var index = layer.load(2, {
                shade: [0.3,'#fff'], //0.1透明度的白色背景
                time: 10000
            });
            $.ajax({type: "GET",dataType:"json", url: admin_file+"?c=api&m=cache_clear",
                success: function(json) {
                    layer.close(index);
                    dr_tips(1, "<?php echo dr_lang('缓存更新完成'); ?>");
                },
                error: function(HttpRequest, ajaxOptions, thrownError) {
                    layer.closeAll('loading');
                    dr_tips(0, "<?php echo dr_lang('更新失败'); ?>");
                }
            });
        }
        function show_category_field(catid) {
            <?php if ($category_field_url) { ?>
            window.location.href = '<?php echo $category_field_url; ?>&catid='+catid;
            <?php } ?>
        }
    </script>
</head>