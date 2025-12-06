
// Set global vars
var browser_width,
	browser_height,
	columnCount = 3,
	columnPadding = 0; 
//var stepCounter = 0;
	
	// Resize site
	var resizeFotos = function(event) {
		//console.log('resizeFotos: '+ ++stepCounter );
		var old_width = browser_width;
		browser_width = $(window).width();
		browser_height = $(window).height();		
		
		//Homepage
		
		//Contact
		if($("#contact-sheet").length){
			getLiWidth = $("#contact-sheet li").first().outerWidth();
			$("#contact-sheet li").css("height", getLiWidth);
		};
		
		// Columns
		if(browser_width < 768) columnCount = 1;
		else if(browser_width <= 991) columnCount = 2;
		else columnCount = 3;
		columnCount = 2;
		
		// Prepare and start plugins
		if(1 || browser_width >= 768) {
			$('#sector-container').imagesLoaded(updateIsotope);
			//if($("#sector-container").length) updateIsotope();
		} else if(old_width >= 768 && browser_width < 768) {
			$('#sector-container, #blog-container').isotope("destroy");
			$("#sector-container .sector-thumbnail, #blog-container .sector-thumbnail").removeAttr("style");
		}
		
		//Projects 
		if(1 || browser_width > 768) {
			if($('body').hasClass('home')) {
				$('#sector-container img').unbind('click').click(openImg);
				if($('#sector-container .opened').length) {
					$('#sector-container .opened').unbind('click');
					openImg(event,'#sector-container .opened');
				} else {
					$("body").unbind('keyup').keyup(function(e) {
						if(e.keyCode >= 37 && e.keyCode <= 40 || e.keyCode == 13)
							$('#sector-container img:first').click();	
					});
				}
			}
		} else {
			if($('#sector-container .opened').length) closeImg();
			$('#sector-container img').unbind('click');
		}
		
	}

	// Init function for Isotope
	var updateIsotope = function() {
		var $container = $("#sector-container");
		//if(!$container.hasClass('stacked')) {
			var $thumbs = $("#sector-container").find(".sector-thumbnail");
			var baseWidth = Math.floor($("#sector-container").width() / columnCount);
			var basePadding = Math.floor((baseWidth - columnPadding) * .66 );
			// Set heights for each thumb 
			$thumbs.each(function(){
				if($(this).is(".natural-frame")) {
					$(this).width(baseWidth).height($(this).find().height());
				} else if($(this).is(".double-frame")) {
					if(browser_width < 768) {
						$(this).width(baseWidth).height(basePadding);
					} else {
						$(this).width(baseWidth * 2).height(basePadding * 2);			   					   	
					}
				} else if($(this).is(".thin-frame")) {
					$(this).width(baseWidth).height(basePadding * 2);
				} else if($(this).is(".wide-frame")) {
					if(browser_width < 991) {
						$(this).width(baseWidth * 2).height($('img',this).height());
						$('img',this).load(function(){
							$(this).height($('img',this).height());
						});
					} else if(browser_width < 768) {
						$(this).width(baseWidth).height(basePadding);
					} else {
						$(this).width(baseWidth * 3).height($('img',this).height());
						if($('img',this).height()>800) $(this).width(baseWidth * 3).height(800);
					}
				} else {
					$(this).width(baseWidth).height(basePadding);
				}
			});
			var refreshIsotope = function() {
				$('#sector-container').isotope({
					itemSelector: '.sector-thumbnail',
					animationEngine: 'css',
					transformsEnabled: true,
					resizable: true,
					masonry: {
						columnWidth: baseWidth,
						gutterWidth: 0
					}
				});
			}
			refreshIsotope();
		//} else {
		//	$("#sector-container").find(".sector-thumbnail img").css('max-height',800)
		//}
	}
	
	var openImg = function(event,opened) {
		
		if(opened) {
			//If receive opened img parameter, that image has to be repositioned
			var i = $(opened)
			var i1 = {
				w: $(i).width(),
				h: $(i).height(),
				x: $(i).data('curx'),
				y: $(i).data('cury')
			};
			i.data('dislocated',1).unbind('click');
		} else {
			// Else, normal start position for the image, close any remaining and append overlay
			closeImg(event,'replace');
			if(!$('.overlay').length) $('<div class="overlay"></div>').appendTo('body').click(closeImg);
			$('body').addClass('openedImage');
			// prepare element and current properties
			var i=$(this);
			var i1 = {
				w: $(i).width(),
				h: $(i).height(),
				x: $(i).offset().left,
				y: $(i).offset().top,
				thumbsrc: $(i).attr('src'),
				fullsrc:  $(i).data('full')
			};
			
			/* prepare a virtual image, when it is loaded its full size src will replace the original image src*/
			$("<img/>")
				.load(function() { i.attr('src',i1.fullsrc); })
				.error(function() { i.attr('src',i1.thumbsrc); })
				.attr("src", i1.fullsrc);
		
			// make element fixed on screen
			$(i).addClass('opened').data({'curx':i1.x,'cury':i1.y,'thumbsrc': i1.thumbsrc}).css({
				position: 'fixed',
				left: 0,
				top: 0,
				width: i1.w,
				height: i1.h,
				"z-index" : 20,
				"transform": 'translate3d('+(i1.x - $(window).scrollLeft())+'px,'+(i1.y - $(window).scrollTop())+'px,0)',
				"-moz-transform": 'translate3d('+(i1.x - $(window).scrollLeft())+'px,'+(i1.y - $(window).scrollTop())+'px,0)',
				"-webkit-transform": 'translate3d('+(i1.x - $(window).scrollLeft())+'px,'+(i1.y - $(window).scrollTop())+'px,0)',
				"transform-origin": "left top",
				"-moz-transform-origin": "left top",
				"-webkit-transform-origin": "left top"
			});
			$(i).offsetHeight;
			console.log('open scroll.left: '+$(window).scrollLeft()+', scroll.top: '+$(window).scrollTop());
		}
		
		//  prepare values for scale and translate 
		var proportion = i1.w / i1.h;
		if(proportion > $(window).width()/$(window).height() ) {
			var i2 = {
				s: ($(window).width() * 0.9) / i1.w,
				x: $(window).width() * 0.05,
				y: ($(window).height() - ($(window).width() * 0.9 * (1/proportion))) / 2
			};
		} else {
			var i2 = {
				s: ($(window).height() * 0.85) / i1.h,
				y: $(window).height() * 0.075,
				x: ($(window).width() - ($(window).height() * 0.9 * proportion)) / 2
			};
		}
		// let's scale and translate 
		$(i).offsetHeight;
		$(i).css({
			'transition': '.5s ease-out, opacity .1s ease-out ',
			left: 0,
			top: 0,
			"transform": 'translate3d('+i2.x+'px,'+i2.y+'px,0) scale('+i2.s+')',
			"-moz-transform": 'translate3d('+i2.x+'px,'+i2.y+'px,0) scale('+i2.s+')',
			"-webkit-transform": 'translate3d('+i2.x+'px,'+i2.y+'px,0) scale('+i2.s+')',
		}).unbind('click').click(closeImg);
		
		// Image keyboard navigation
		$("body").unbind('keyup').keyup(function(e) {
			if( // if key is pressed and image opened
				[37,38,39,40,27,13].includes(e.keyCode)
				&&
				$(this).hasClass('openedImage')
			) {
				if(e.keyCode == 37 || e.keyCode == 38) { // left
					$(i).parent().parent().prev().find('img').click();
				  }
				  else if(e.keyCode == 39 || e.keyCode == 40) { // right
					$(i).parent().parent().next().find('img').click();
				  }
				  else if(e.keyCode == 27) { // esc
					closeImg(e);
				  }
				  else if(e.keyCode == 13) { // enter
					closeImg(e);
					//if($(this).hasClass('openedImage')) closeImg(e);
					//else $(i).click(); //
				  }
			}
		});
	};	
	var closeImg = function(event,mode) {
		var i=$('.opened');
		var overlay=$('.overlay');
		i.each(function(){
			var j = $(this);
			//j.attr('src',j.data('thumbsrc'));
			j.css({
				"z-index" : 18,
				"transform": 'translate3d('+(j.data('curx') - $(window).scrollLeft())+'px,'+(j.data('cury') - $(window).scrollTop())+'px,0) scale(1)',
				"-moz-transform": 'translate3d('+(j.data('curx') - $(window).scrollLeft())+'px,'+(j.data('cury') - $(window).scrollTop())+'px,0) scale(1)',
				"-webkit-transform": 'translate3d('+(j.data('curx') - $(window).scrollLeft())+'px,'+(j.data('cury') - $(window).scrollTop())+'px,0) scale(1)'
			});
			if(j.data('thumbsrc')) j.attr('src',j.data('thumbsrc'));
			if(j.data('dislocated')) {
				j.css('opacity',0).removeData('dislocated');
				setTimeout(function(){
					j.removeAttr('style').css({
						'opacity':1,
						'transition': 'opacity .2s ease-out'
					});
				},300);
			}
		});
		setTimeout(function(){
			i.removeClass('opened').removeAttr('style').unbind('click');
			/*if(browser_width > 768)*/ i.click(openImg);
			//console.log('Mode: '+mode);
			if(mode!='replace') {
				overlay.remove();
				$('body').removeClass('openedImage');
			}
		},500);
	};

// Run imageLoaded() before document loaded to avoid image blinking
// /(^|\s)single-project($|\s)/.test(document.body.className)

if(document.getElementById('sector-container')) {
	var images = document.getElementById('sector-container').getElementsByClassName('img');
	for(var i=0,len=images.length;i<len;i++) images[i].className += ' loading';
	var imgLoad = imagesLoaded( '#sector-container');
	imgLoad.on( 'progress', function( imgLoad, image ) {
		jQuery(image.img).parents('.img').addClass('transition').removeClass('loading');
	});
} else if(document.getElementById('big-picture')) {
	var image = document.getElementById('big-picture').getElementsByTagName('img');
	image.className += ' loading';
	var imgLoad = imagesLoaded( '#big-picture');    
	$(window).resize(resizeHomeImage);	
	imgLoad.on( 'progress', function( imgLoad, image ) {
		var image = image.img;
		resizeHomeImage(image);
		jQuery(image).addClass('transition').removeClass('loading');
	});
} else if(document.getElementById('blog-container')) {
	var images = document.getElementById('blog-container').getElementsByClassName('img');
	for(var i=0,len=images.length;i<len;i++) images[i].className += ' loading';
	var imgLoad = imagesLoaded( '#blog-container');
	imgLoad.on( 'progress', function( imgLoad, image ) {
		jQuery(image.img).parents('.img').addClass('transition').removeClass('loading');
	});
}

// DOM Ready Stuff
// Adding "debouncedresize" as a jquery event
(function($) {

var $event = $.event,
	$special,
	resizeTimeout;

$special = $event.special.debouncedresize = {
	setup: function() {
		$( this ).on( "resize", $special.handler );
	},
	teardown: function() {
		$( this ).off( "resize", $special.handler );
	},
	handler: function( event, execAsap ) {
		// Save the context
		var context = this,
			args = arguments,
			dispatch = function() {
				// set correct event type
				event.type = "debouncedresize";
				$event.dispatch.apply( context, args );
			};

		if ( resizeTimeout ) {
			clearTimeout( resizeTimeout );
		}

		execAsap ?
			dispatch() :
			resizeTimeout = setTimeout( dispatch, $special.threshold );
	},
	threshold: 150
};

})(jQuery);


var initFotos = function(event) {
	//Start fotos mechanism
    resizeFotos(); 
	// Redraw things on resizing 
	$(window)
		.unbind("debouncedresize",resizeFotos)
		.bind("debouncedresize",resizeFotos)
		.unbind('orientationchange',orientationchangeFotos)
		.bind('orientationchange',orientationchangeFotos);
};

// Destroy isotope and related resize events 
var destroyFotos = function(event) {
	//console.log('destroy fotos');
	$('#sector-container, #blog-container').isotope("destroy");
	$("#sector-container .sector-thumbnail, #blog-container .sector-thumbnail").removeAttr("style");
	$(window).unbind("debouncedresize",resizeFotos);
	$(window).unbind('orientationchange',orientationchangeFotos);
};
var orientationchangeFotos = function(event) {
	$("body").addClass("rotating");
	setTimeout(function(){
	  resizeFotos();
	  setTimeout(function(){
		  $("body").removeClass("rotating");
	  }, 800);
  }, 100);
};