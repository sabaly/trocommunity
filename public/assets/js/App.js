!(function($) {
    "use strict";

	$(".alert-popup").hide();
	$(".back-to-top").hide();
    $(window).scroll(function() {

        if($(window).scrollTop() > 5) {
            $('#header').css({
                'background': '#000000'
			});
			$(".back-to-top").fadeIn(1500);
        }else {
            $('#header').css({
                'background': 'rgba(0,0,0,0.1)'
            });
			$(".back-to-top").fadeOut(1500);
        }
        
    });

    $(document).on('click', ".nav-menu a, .mobile-nav a, .scrollto, .connexion-btn, .ca-btn, .back-to-top, .espace-text a", function(e) {
        e.preventDefault();

        var target = $(this.hash);

		if (target.length) {

			var scrollto = target.offset().top;
			var scrolled = 20;

			  if ($('#header').length) {
			  scrollto -= $('#header').outerHeight()

			  if (!$('#header').hasClass('header-scrolled')) {
				scrollto += scrolled;
			  }
			}

			if ($(this).attr("href") == '#header') {
			  scrollto = 0;
			}

			$('html, body').animate({
			  scrollTop: scrollto
			}, 1500, 'easeInOutExpo');

			return false;
		} else {
			if($(this).attr('href') != undefined)
				location.assign($(this).attr('href'));
		}
	});

	if($('.nav-menu').length) {
		var $mobile_nav = $('.nav-menu').clone().prop({
			class: 'mobile-nav d-lg-none'
		});
		$('body').append($mobile_nav);
		$('.mobile-nav').hide();

		$(document).on('click', '.mobile-nav-toggle', function(e) {
			$('body').toggleClass('mobile-nav-active');
			$('.mobile-nav-toggle').toggleClass('icofont-navigation-menu icofont-close');
			$('.mobile-nav').slideDown(1500);
		});

		$(document).click(function(e) {
			var container = $(".mobile-nav, .mobile-nav-toggle");
			if (!container.is(e.target) && container.has(e.target).length === 0) {
			  if ($('body').hasClass('mobile-nav-active')) {
				$('body').removeClass('mobile-nav-active');
				$('.mobile-nav-toggle').toggleClass('icofont-navigation-menu icofont-close');
			  }
			}
		});

	}
	
	
})(jQuery);

function addToFavorite(e, id) {
	e.preventDefault();
	$(".card-actions i").css({
		'cursor': 'progress'
	});
	$.ajax({
		type: "POST",
		url: "/reader/addToFavorite",
		data: "id=" + id + "&action=heart",
		success: function(msg) {
			$(".card-actions i").css({
				'cursor': 'pointer'
			});
			if(msg == "__NOT_CONNECTED__") {
				var scrollto = $("#connexion").offset().top - 100;

                $("html, body").animate({
                    scrollTop: scrollto
                }, 1500, 'easeInOutExpo');
			}else if(msg == "__ACTION__") {
				$(".alert-popup").css({
					top: e.clientY,
					left: e.clientX
				});
				$('.alert-popup p').html("Ajouté au favories");
				$('.alert-popup p').attr("class", "alert alert-success");
				$('.alert-popup').fadeIn(1000).delay(500).fadeOut();
			}else if(msg == "__DEJA_AJOUTE__") {
				$(".alert-popup").css({
					top: e.clientY,
					left: e.clientX
				});
				$('.alert-popup p').html("Déjà ajoute");
				$('.alert-popup p').attr("class", "alert alert-danger");
				$('.alert-popup').fadeIn(1000).delay(500).fadeOut();
			}else if(msg == "__USER_BOOK__") {
				$(".alert-popup").css({
					top: e.clientY,
					left: e.clientX
				});
				$('.alert-popup p').html("C'est votre livre !");
				$('.alert-popup p').attr("class", "alert alert-primary");
				$('.alert-popup').fadeIn(1000).delay(500).fadeOut();
			}
		}
	})
}