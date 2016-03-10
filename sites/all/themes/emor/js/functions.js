var nTimeOut = 500;
jQuery(document).ready(function ($) {
	//Gradient IndexTree
	var tmp = generateColor('#668BB7','#0B3567',$('.index .nav>li').length);
	$('.index .nav>li>a').each(function(index){
		$(this).css("background-color", '#'+tmp[index]);
	});
	//Same height content in Mapping Home
	// var treeHeight = $('#content .index').height();
	// $('#content .bubble-chart img').outerHeight(treeHeight - $('#content .bubble-chart h2').outerHeight(true));
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
			//setTimeout(collapseIndexTree,nTimeOut);
		}
	});
	$('#block-indextree .dropdown-toggle').on('click',function(){
		$(this).parent().toggleClass('open');
		var rootEl = $(this).parentsUntil('.indextree')[$(this).parentsUntil('.indextree').length - 1];
		var parentSiblings = $(this).parent().siblings('li');
		// 
		$('#block-indextree .indextree > .dropdown').not($(rootEl)).removeClass('open');
		parentSiblings.removeClass('open');
		// var eClick = function(e){
		// 	e.preventDefault();	
		// 	console.log('Disable hide');
		// };
		// jQuery('.dropdown').on('hide.bs.dropdown', eClick);
		// jQuery('.dropdown').off('hide.bs.dropdown', eClick);
		// jQuery(this).parent().prev().click();
		// console.log(jQuery(this).parent().prev());
	});	
	// $(window).on('click', collapseIndexTree);
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
		}/* else if ($(this).hasClass('expanded')){
			$('.right-menu').hide();
			$('.right-menu .item').show();
			$('#content .bubble-chart img').outerHeight($('#content .index').height() - $('#content .bubble-chart h2').outerHeight(true));
			$(this).removeClass('col-lg-10 expanded').addClass('col-lg-8');
			setTimeout(function(){
				$('.mapping-home-item').show();
			}, nTimeOut);
			showHeader();	
		}*/
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

	/* Emor Notes*/
	$('.details fn a').click(function(){
		jQuery('.nav-tabs li:last-child a').click()
		pos = $(this).offset().top - $(window).scrollTop();
		rightpos = $( $.attr(this, 'href') ).offset().top;
		if($('#toolbar-administration').length > 0) extra = 98;
		else extra = 20;
	    $('html, body').animate({
	        scrollTop: rightpos - extra
	    }, 500);
	    return false;
	});
	$('#table-of-content a').click(function(event) {
		leftpos = $( $.attr(this, 'href') ).offset().top;
		if($('#toolbar-administration').length > 0) extra = 98;
		else extra = 20;
	    $('html, body').animate({
	        scrollTop: leftpos - extra
	    }, 500);
	});

	$('#emor-notes p').click(function(event) {
		id = $(this).attr('id');
		item = $('.details fn a[href=#'+id+']');
		if($('#toolbar-administration').length > 0) extra = 98;
		else extra = 20;
	    $('html, body').animate({
	        scrollTop: item.offset().top - extra
	    }, 500);
	    return false;
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

function ajaxRequest(queryString, path){
	var queryUrl = path +'?' + queryString;
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
		mappingExplorerFilterHTMLFix();
		initSelecter();
		//Change URL
		History.pushState(null, result.title, queryUrl)
	});
}

function ajaxSearchNavBar(){
	var timer, searchString;
	jQuery('.search input.search-query').on('keyup',function(){
		clearTimeout(timer);
		var $this = this;
		value = jQuery(this).val();
		if (value.length >= 3  && searchString != value){
			timer = setTimeout(function(){
				// searchString = value;
				// queryUrl = '/emor-mapping-explore?search='+searchString;
				queryString = jQuery($this).parent().serialize();
				ajaxRequest(queryString, '/emor-mapping-explore');
				// jQuery('.ajax-loading').fadeIn('slow');
				// jQuery.ajax({
				// 	url: queryUrl,
				// }).done(function(data){
				// 	var result = new Object();
				// 	result.title = jQuery(data).filter('title').text();
				// 	result.bodyClass = jQuery(data).find('#body-class').attr('class');
				// 	result.content = jQuery(data).find('#content');
				// 	result.headerwelcometext = jQuery(data).find('#block-headerwelcometext');
				// 	result.headercoverphoto = jQuery(data).find('#block-headercoverphoto');
				// 	//Append result
				// 	jQuery('body').toggleClass(jQuery('body').attr('class')).addClass(result.bodyClass);
				// 	jQuery('#block-headerwelcometext').replaceWith(result.headerwelcometext);
				// 	jQuery('#block-headercoverphoto').replaceWith(result.headercoverphoto);
				// 	jQuery('#content').replaceWith(result.content);
				// 	jQuery('.ajax-loading').fadeOut('slow');
				// 	mappingExplorerFilterHTMLFix();
				// 	//Change URL
				// 	console.log(result);
				// 	History.pushState(null, result.title, queryUrl)
				// });
			},500);			
		}
	});
	// jQuery('.search .btn-clear').on('click',function(){
	// 	jQuery('.search input.search').val('');
	// 	return false;
	// });
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

function initfancyBox(){
	jQuery('.fancybox').fancybox({
		maxWidth	: 800,
		maxHeight	: 600,
		fitToView	: false,
		width		: '70%',
		height		: '70%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});	
}
jQuery(document).ready(function(){initfancyBox()});

function topNavbarFix(){
	// //Reset height and attribute
	// jQuery('ul.child-menu').each(function(){
	// 	jQuery(this).height(0);
	// 	jQuery(this).attr('aria-expanded','false');
	// });
	//Bind event
	jQuery('.navbar-static-top a[data-toggle="collapse"]').on('click',function(){
		$this = jQuery(this);
		jQuery('ul.child-menu').removeAttr('style');
		var initHeight = jQuery('ul.child-menu').actual('height');
		var height = 0;
		setTimeout(function(){
			var $parent;
			jQuery('li.panel.root').each(function(){
				if(jQuery(this).has($this).length)
					$parent = jQuery(this);
			});
			$parent.find('ul.child-menu').each(function(){
				if(jQuery(this).attr('aria-expanded') == 'true'){
					height += initHeight;
				}
			});
			jQuery('.panel.root > a').each(function(){
				if($parent.children('a').attr('aria-expanded') == 'false')
					height = 0;
			});
			if (jQuery('#block-headerwelcometext').length > 0)
				jQuery('#block-headerwelcometext').css('margin-top',height+'px');
			else
				jQuery('#content').css('margin-top',height+'px');

		},0);
	});
}
jQuery(document).ready(function(){topNavbarFix()});

function articleEmorNotes(){
	//Get & Set height
	var height = jQuery(window).height() - jQuery('.content-table .nav-tabs').height() - jQuery('.content-tags').height();
	jQuery('.emor-article .tab-content').outerHeight(height);
	//Tags tab fix
	var tagHeight = jQuery('.emor-article .content-tags .panel-collapse').actual('height');
	jQuery('.emor-article .content-tags a.panel-title').on('click',function(){
		$this = jQuery(this);
		setTimeout(function(){
			if ($this.attr('aria-expanded') == 'false')
				jQuery('.emor-article .tab-content').outerHeight(jQuery('.emor-article .tab-content').outerHeight()+tagHeight);
			else
				jQuery('.emor-article .tab-content').outerHeight(jQuery('.emor-article .tab-content').outerHeight()-tagHeight);
		},0)
	});
	jQuery(window).on('scroll', function(){
		// articleEmorNotes();
		articleEmorNotesFixed();
	});
}
function articleEmorNotesFixed(){
    var scrollTop     = jQuery(window).scrollTop(),
    	element = jQuery('.emor-article .content-tabs');
    	elementToStop = jQuery('.emor-article .content-details');     
   distance = 0;
   if(elementToStop.length>0)  
    	distance      = (scrollTop - elementToStop.offset().top);

    // console.log(distance);
    if(distance >= 0)
    	element.addClass('fixed');
    else
    	element.removeClass('fixed');	

}
jQuery(document).ready(function(){
	// articleEmorNotes();
	// articleEmorNotesFixed();
});

function mappingExplorerFilterHTMLFix(){
	jQuery('.js-form-item-title, .js-form-item-author, .filter-keys').remove();
}
jQuery(document).ready(function(){mappingExplorerFilterHTMLFix()});

function initSelecter(){
	//Reset selecter
	jQuery('select').selecter('destroy');
	//Selecter on explore page
	jQuery('.explore-filter-select select').selecter();
	//set Height
	var baseHeight = jQuery('.explore-filter-select .selecter-options > .selecter-item').outerHeight();
	jQuery('.explore-filter-select .selecter-options').height(baseHeight * 3);
	//Limit select
	// jQuery('.custom-filter-type').removeAttr('multiple');
	jQuery('.explore-filter-select select').removeAttr('multiple').on('change',function(){
        // jQuery(this).find('option').attr('disabled','true');
        jQuery(this).selecter('update');
	});

}
jQuery(document).ready(function(){initSelecter()});