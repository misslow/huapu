//--页面适配
function setFont() {
    var winWidth=$(window).width();
    if(winWidth<768){//手机端
        $('html').css('font-size',winWidth/15)
    }else{
        $('html').css('font-size',"50px")
    };
}
setFont();

$(window).resize(function () {
    setFont();
})




$(function(){
    //动画效果
    var wow = new WOW({
        boxClass: 'wow',
        animateClass: 'animated',
        offset: 100,
        mobile: true,
        live: true,
    });
    wow.init();

    //微信二维码模态框距顶部距离
    $('.modal-xsm').css("margin-top",$(window).innerHeight()/3+'px')


    //返回顶部按钮
    $('#to-top').click(function(){
        $('html , body').animate({scrollTop: 0},'slow');
        return false;
    })
    var scorll_now=0;
    $(window).scroll(function(){

        if($(window).scrollTop()>200){
            if($(window).scrollTop()<scorll_now){
                $('#to-top').show();
            }else{
                $('#to-top').hide();
			}
        }else{
            $('#to-top').hide()
        }

        scorll_now=$(window).scrollTop();
    })

    var headerHeight= $('header').outerHeight();
    $('#headerHeight').height(headerHeight);

    if($(window).innerWidth()>768){
//      $('.menu-box').css('max-height',$(window).innerHeight()-headerHeight+'px')
		$('#cp-fh').click(function(){
			$(this).parents('.menu-box').fadeOut(100);
		})
        //pc端导航鼠标划过效果
        $('#nav>li').each(function(a){
            $(this).hover(function(){
                $('#nav>li').removeClass('hover')
                $(this).find('.menu-box').stop().fadeIn();
            },function(){
                $('#hover').addClass('hover')
                $(this).find('.menu-box').fadeOut(100);
            })
        })

    }else{

        $('#phone-nav,.phone-nav-1').css({'top':headerHeight+'px'});
        //移动端导航鼠标划过效果
        $('#nav-btn').click(function(){
            if($(this).hasClass('active')){
                $(this).removeClass('active');
                $('#phone-nav').css({'height':0,'opacity':0});
                $('.phone-nav-1').removeClass('active')
                $('#phone-nav>li>a').removeClass('active');
                $('.index_header').css({'transform':''})
            }else{
                $(this).addClass('active');
                $('#phone-nav,.phone-nav-1').css({'height':$(window).innerHeight()+'px','opacity':1});
                $('.index_header').css({'transform':'translateY(0)'})
            }

        })

        $('#phone-nav>li').each(function(i){
            if($(this).find('.phone-nav-1').length>0){
                $(this).find('a').eq(0).click(function(){
                    $(this).next('.phone-nav-1').addClass('active');
                    $('#phone-nav>li>a').addClass('active');
                })
            }
        })
        $('.fanhui').click(function(){
            $(this).parents('.phone-nav-1').removeClass('active')
            $('#phone-nav>li>a').removeClass('active');
        })

        $('.phone-nav-1>a').each(function(i){
            if($(this).find('.phone-nav-2').length>0){
                $(this).find('a').eq(0).click(function(){
                    $(this).next('.phone-nav-2').addClass('active');
                    $('.phone-nav-1>a').addClass('active');
                })
            }
        })
        $('.fanhui2').click(function(){
            $(this).parents('.phone-nav-2').removeClass('active')
            $('.phone-nav-1>a').removeClass('active');
        })


    }

})
//表单
$(document).on('click', '#m-subnit', function(){
    $.validator.setDefaults({
        submitHandler: function() {
            alert("提交事件!");
        }
    });
    $().ready(function() {
        // 手机号码验证
        jQuery.validator.addMethod("isMobile", function(value, element) {
            var length = value.length;
            var mobile = /^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/;
            return this.optional(element) || (length == 11 && mobile.test(value));
        }, "请正确填写您的手机号码");

        var validator = $("#form1").validate({
            errorPlacement: function(error, element) {
                $(element).closest( ".m-form2" ).find( "span" ).html( error );
            },
            errorElement: "span",
            rules: {
                phone : {
                    required : true,
                    minlength : 11,
                    isMobile : true
                },
                email:{
                    required : true,
                    email : true
                }
            },
            //提示信息
            messages: {
                name: {
                    required: "请输入姓名",
                    minlength: " (不能少于 2 个字母)",
                    maxlength: " (不能大于 5个字母)"
                },
                job:"请输入职位",
                email: {
                    email:'请输入有效的邮箱地址',
                    required:"请输电子入邮箱",
                },
                corporateName:"请输入公司名称",
                companyAddress:"请输入公司地址",
                industry:"请输入行业",
                entryName:"请输入项目名称",
                requirementDescription: {
                    required: "请填写您的重点需求描述",
                    minlength: " (不能少于 10 个字母)"
                },
                phone: {
                    required: "请输入手机号",
                    isMobile : "请正确填写您的手机号码"
                },
            },
            success: function(label) {
                label.html("&nbsp;").addClass("checked");
            }
        });
    });
} )
