
$(function(){
     $('.met1_top').click(function(){
        var index = $(this).index('.met1_top');
		$('.met1_en .met1_ETop').eq(index).stop().show().siblings().stop().hide();
		
	});

     $('.met1_ETop>img').click(function(event) {
        $('.met1_en .met1_ETop').stop().hide();
     });


});

$(document).ready(function() {
	
	$(window).scroll(function() {
		ST = $(window).scrollTop();
		w_height = $(window).height();

		if (ST >= $('.header').height()+$('.M_banner').height()-50) {
			$('.M_banner_en').addClass('fkis');
		} else{
			$('.M_banner_en').removeClass('fkis');
		}

	})
	
	$(function(){
		 $('.navMobile li>h5 i').click(function(){
			$(this).parent().parent().find('.listDown').stop().slideToggle();
			$(this).toggleClass('i_class');
		});
		$('.navMobile li .listDown .list2_one h3').click(function(){
			$(this).parent().find('.list2_two').stop().slideToggle();
			$(this).find('i').toggleClass('i_class');
			
		});
		$('.navMobile li .listDown .list2_one .list2_two h4').click(function(){
			$(this).parent().find('p').stop().slideToggle();
			$(this).find('i').toggleClass('i_class');
			
		});
		 $('.menu-handler').click(function(){
			$(this).toggleClass('active');
			if ($(this).hasClass('active')) {
				$('.menuBox').animate({'right' : 0} , 300);
			}
			else{
				$('.menuBox').animate({'right' : -100+'%'} , 300);
			}
		});
		 $('#lsearch').click(function(){
            keys = $('#keys').val();
			if(keys.length>0){
				location.href = '/search?key='+keys;
			}
		 })
	});
	if($('.metC_con .swiper-slide').length>1){
		 $('.method1C .arrow-right,.method1C .arrow-left').show();
		}
	
	if($('.metxqJ_con .swiper-slide').length>1){
		 $('.metxqJ_con .arrow-right,.metxqJ_con .arrow-left').show();
		}
	if($('.metxqO_con .swiper-slide').length>1){
		 $('.metxqO_con .arrow-right,.metxqO_con .arrow-left').show();
		}
	
});
