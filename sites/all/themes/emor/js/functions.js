var nTimeOut = 500;
jQuery(document).ready(function ($) {
	//Gradient IndexTree
	var tmp = generateColor('#668BB7','#0B3567',$('.index .nav>li').length);
	$('.index .nav>li>a').each(function(index){
		$(this).css("background-color", '#'+tmp[index]);
	});
	//Same height content in Mapping Home
	var treeHeight = $('#content .index').height();
	$('#content .bubble-chart img').outerHeight(treeHeight - $('#content .bubble-chart h2').outerHeight(true));
	//END

	//Homepage Index Animation
	$('#block-indextree .nav a.dropdown-toggle').on('click',function(){
		// var width = $(this).parents().find('#block-indextree .nav>li').width();
		setTimeout(function(){
			$('#block-indextree .nav>li').addClass('col-lg-4');
		},250);
		
		
		if ($('#block-indextree').hasClass('col-lg-4')){
			$('.mapping-home-item').not($('#block-indextree')).hide('fast',function(){
				$('#block-indextree').removeClass('col-lg-4').addClass('col-lg-10 expanded');
				// $('#block-indextree .nav').addClass('expanded');			
			});
			$('.right-menu').show();
			$('.right-menu .button-index').hide();
			hideHeader();				
		} else{
			setTimeout(collapseIndexTree,nTimeOut);
		}
	});
	$(window).on('click', collapseIndexTree);
	$('body').on('click', collapseKnowledgeBase);

	$('#block-bubblechart').on('click',function(){
		if ($(this).hasClass('col-lg-8')){
			hideHeader();
			$('.mapping-home-item').not($(this)).hide('fast',function(){
				$('#block-bubblechart').removeClass('col-lg-8').addClass('col-lg-10 expanded');
				$('#content .bubble-chart img').height('auto');				
			});
			$('.right-menu').show();
			$('.right-menu .button-bubblechart').hide();	
		} else if ($(this).hasClass('expanded')){
			$('.right-menu').hide();
			$('.right-menu .item').show();
			$('#content .bubble-chart img').outerHeight($('#content .index').height() - $('#content .bubble-chart h2').outerHeight(true));
			$(this).removeClass('col-lg-10 expanded').addClass('col-lg-8');
			setTimeout(function(){
				$('.mapping-home-item').show();
			}, nTimeOut);
			showHeader();	
		}
	});

	$('#block-indexsearch input').on('click',function(){
		if ($('#block-indexsearch').hasClass('col-lg-4')){
			hideHeader();
			$('.mapping-home-item').not($('#block-indexsearch')).hide('fast',function(){
				$('#block-indexsearch').removeClass('col-lg-4').addClass('col-lg-10 expanded');
			});
			$('.right-menu').show();
			$('.right-menu .button-index-search').hide();	
		} else if ($('#block-indexsearch').hasClass('expanded')){
			$('.right-menu').hide();
			$('.right-menu .item').show();
			$('#block-indexsearch').removeClass('col-lg-10 expanded').addClass('col-lg-4');
			setTimeout(function(){
				$('.mapping-home-item').show();
			}, nTimeOut);
			
			showHeader();
		}
	});	

	$('#block-knowledgebase .list-menu, #block-knowledgebase .search ').on('click',function(){
		var parentBlockID = '#block-knowledgebase'; 
		if ($(parentBlockID).hasClass('col-lg-8')){
			$('.mapping-home-item').not($(parentBlockID)).hide('fast',function(){
				$(parentBlockID).removeClass('col-lg-8').addClass('col-lg-10 expanded');
			});
			$('.right-menu').show();	
			$('.right-menu .button-knowledge-base').hide();
		}
		hideHeader();
	});

	

	function collapseKnowledgeBase(e){
		if (!$('#block-knowledgebase').find(e.target).length & $('#block-knowledgebase').hasClass('expanded')){
			$('#block-knowledgebase').removeClass('col-lg-10 expanded').addClass('col-lg-8');
			$('.mapping-home-item').show();	
			$('.right-menu').hide();
			$('.right-menu .item').show();
			showHeader();
		}
	}

	function collapseIndexTree(){
		if( (!$('#block-indextree .nav>li').hasClass('open')) & $('#block-indextree').hasClass('expanded') ) {
			$('.right-menu').hide();
			$('.right-menu .item').show();
			$('#block-indextree').removeClass('col-lg-10 expanded').addClass('col-lg-4');
			$('#block-indextree .nav>li').removeClass('col-lg-4');
			showHeader();
			setTimeout(function(){
				$('.mapping-home-item').not($('#block-indextree')).show();
			}, nTimeOut);
		}	
	}			
});

function hideHeader(){
	jQuery('#block-headerwelcometext, #block-headercoverphoto').slideUp('slow');
	jQuery('body').animate({scrollTop:0}, nTimeOut, 'swing');
}

function showHeader(){
	jQuery('#block-headerwelcometext, #block-headercoverphoto').slideDown('slow');
}

function hex (c) {
  var s = "0123456789abcdef";
  var i = parseInt (c);
  if (i == 0 || isNaN (c))
    return "00";
  i = Math.round (Math.min (Math.max (0, i), 255));
  return s.charAt ((i - i % 16) / 16) + s.charAt (i % 16);
}

/* Convert an RGB triplet to a hex string */
function convertToHex (rgb) {
  return hex(rgb[0]) + hex(rgb[1]) + hex(rgb[2]);
}

/* Remove '#' in color hex string */
function trim (s) { return (s.charAt(0) == '#') ? s.substring(1, 7) : s }

/* Convert a hex string to an RGB triplet */
function convertToRGB (hex) {
  var color = [];
  color[0] = parseInt ((trim(hex)).substring (0, 2), 16);
  color[1] = parseInt ((trim(hex)).substring (2, 4), 16);
  color[2] = parseInt ((trim(hex)).substring (4, 6), 16);
  return color;
}

function generateColor(colorStart,colorEnd,colorCount){

	// The beginning of your gradient
	var start = convertToRGB (colorStart);    

	// The end of your gradient
	var end   = convertToRGB (colorEnd);    

	// The number of colors to compute
	var len = colorCount;

	//Alpha blending amount
	var alpha = 0.0;

	var saida = [];
	
	for (i = 0; i < len; i++) {
		var c = [];
		alpha += (1.0/len);
		
		c[0] = start[0] * alpha + (1 - alpha) * end[0];
		c[1] = start[1] * alpha + (1 - alpha) * end[1];
		c[2] = start[2] * alpha + (1 - alpha) * end[2];

		saida.push(convertToHex (c));
		
	}
	
	return saida;
	
}

function ajaxSearchNavBar(){
	var timer, searchString;
	jQuery('.search input.search').on('keyup',function(){
		clearTimeout(timer);
		value = jQuery(this).val();
		if (value.length >= 3  && searchString != value){
			timer = setTimeout(function(){
				searchString = value;
				queryUrl = '/emor-mapping-explore?title='+searchString;
				jQuery('.ajax-loading').fadeIn('slow');
				jQuery.ajax({
					url: queryUrl,
				}).done(function(data){
					var result = new Object();
					result.title = jQuery(data).filter('title').text();
					result.bodyClass = jQuery(data).find('#body-class').attr('class');
					result.content = jQuery(data).find('#content');
					result.headerwelcometext = jQuery(data).find('#block-headerwelcometext');
					result.headercoverphoto = jQuery(data).find('#block-headercoverphoto');
					//Append result
					jQuery('body').toggleClass(jQuery('body').attr('class')).addClass(result.bodyClass);
					jQuery('#block-headerwelcometext').replaceWith(result.headerwelcometext);
					jQuery('#block-headercoverphoto').replaceWith(result.headercoverphoto);
					jQuery('#content').replaceWith(result.content);
					jQuery('.ajax-loading').fadeOut('slow');
					//Change URL
					console.log(result);
					History.pushState(null, result.title, queryUrl)
				});
			},500);			
		}
	});
	jQuery('.search .btn-clear').on('click',function(){
		jQuery('.search input.search').val('');
		return false;
	});
}
jQuery(document).ready(function(){ajaxSearchNavBar()});

function movingIndexTreeDropdown(){
	jQuery('#block-indextree .dropdown-menu').each(function(){
		var $this = jQuery(this);
		var top = 0;
		var index = $this.parent().siblings('li').length - $this.parent().nextAll('li').length;
		top = ( $this.parent().parent().actual('outerHeight', { includeMargin : true }) - ($this.parent().actual('outerHeight', { includeMargin : true }) * index) ) - $this.actual('outerHeight', { includeMargin : true });
		$this.css('top',top+'px');
		// console.log(jQuery(this).parent().siblings().length);
	});
}
jQuery(document).ready(function(){movingIndexTreeDropdown()});
