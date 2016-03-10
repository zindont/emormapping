<?php

	/**
	 * @file
	 * The PHP page that serves all page requests on a Drupal installation.
	 *
	 * All Drupal code is released under the GNU General Public License.
	 * See COPYRIGHT.txt and LICENSE.txt files in the "core" directory.
	 */
	use Drupal\Core\DrupalKernel;
	use Symfony\Component\HttpFoundation\Request;

	$autoloader = require_once 'autoload.php';

	$kernel = new DrupalKernel('prod', $autoloader);

	$request = Request::createFromGlobals();
	$response = $kernel->handle($request);
	// $response->send();

	// $kernel->terminate($request, $response);

	use Drupal\Core\Template\Attribute;
	use Drupal\views\Views;
	use Drupal\node\Entity\Node;
	use Drupal\taxonomy\TermStorage;

 	//Prepare for Bubble chart
	$storage = \Drupal::entityManager()->getStorage('node');

	$qIds = $storage->getQuery()
	->condition('type', 'question')
	->condition('status', 1)
	->execute();

	// $variables['questions'] = $storage->loadMultiple($qIds);
	$questions = $storage->loadMultiple($qIds);

	$questionId = $_GET['qid'];

	//get all answers depend on question
	$asIds = $storage->getQuery()
	  ->condition('type', 'answer')
	  ->condition('status', 1)
	  ->condition('field_question', $questionId)
	  ->execute();
	$answers = $storage->loadMultiple($asIds);

	$isFilterLoc = false;
	if(isset($_GET['loc'])){
		if($_GET['loc']!='')
			$isFilterLoc = true;
	}
	$isFilterName = false;
	if(isset($_GET['name'])){
		if($_GET['name']!='')
			$isFilterName = true;
	}

	$isFilterYear = false;
	if(isset($_GET['year'])){
		if($_GET['year']!='')
			$isFilterYear = true;
	}

	$isFilterAuthor = false;
	if(isset($_GET['author'])){
		if($_GET['author']!='')
			$isFilterAuthor = true;
	}

	$isFilterLang = false;
	if(isset($_GET['language'])){
		if($_GET['language']!='')
			$isFilterLang = true;
	}

	$isFilterLength = false;
	if(isset($_GET['page_length'])){
		if($_GET['page_length']!='')
			$isFilterLength = true;
	}

	$isFilterismapping = false;
	if(isset($_GET['is_mapping'])){
		if($_GET['is_mapping']!='')
			$isFilterismapping = true;
	}
	$type = $_GET['type'];
	switch ($type):
		case 'text':
			// get all articles depend on answers
			$arIds = array();
			$mediaTypes = $authors = $positions = $titles = $years = $locations = $pageLengths = array();
			foreach ($answers as $answer) {
				$tmp = $answer->get('field_articles');
				foreach ($tmp as $item) {
				  $arIds[] = (int)$item->target_id;
				}
			}
			$articles = $storage->loadMultiple($arIds); ?>
			{
				dataSeries:[
				<?php $i=1; foreach ($articles as $article): ?>
				<?php 
					$tmp = $article->get('field_author_bubble');
					$atIds = array();
					foreach ($tmp as $item) {
					  $atIds[] = (int)$item->target_id;
					}
					$auts = $storage->loadMultiple($atIds);

					if(isset($_GET['reloadFilter'])){
			    		if($article->field_location->value!='' && !in_array($article->field_location->value, $locations))
			        		$locations[] = $author->field_location->value;

			    		if($article->title->value!='' && !in_array($article->title->value, $titles))
			        		$titles[] = $article->title->value;

						$year = substr($article->field_published_date->value,0,4);
			    		if($year!='' && !in_array($year, $years))
			        		$years[] = $year;

			        	foreach ($auts as $aut) {
			        		$authors[$aut->nid->value] = $aut->title->value;
			        	}
			      	}
			    ?>
				<?php if($isFilterLoc && strpos($article->field_location->value, $_GET['loc']) === false) continue;?>
				<?php if($isFilterAuthor && ($article->field_author_bubble->target_id != $_GET['author'])) continue;?>
				<?php if($isFilterYear && strpos($article->field_published_date->value, $_GET['year']) === false) continue;?>
				<?php 
				if(is_null($article->field_language->value)) $article->field_language->value = 'English';
				if($isFilterLang && ($article->field_language->value != $_GET['language'])) continue;
				if($isFilterLength && ($article->field_total_page->value != $_GET['length'])) continue;
				if(is_null($article->field_is_mapping->value)) $article->field_is_mapping->value = 0;
				if($isFilterismapping && ($article->field_is_mapping->value != $_GET['is_mapping'])) continue;
				?>
				<?php if($isFilterName && strpos($article->title->value, $_GET['name']) === false) continue;?>
			    <?php $tmp = $i;?>
			    <?php $j=1; foreach ($answers as $answer):?>
			        <?php $tmp1 = $j; ?>
			        <?php foreach ($answer->field_articles as $ar):?>
			            <?php if ($article->nid->value == $ar->target_id): ?>
			                <?php $tmp = $tmp1 ?>
			            <?php endif; ?>
			        <?php endforeach; ?>
			    <?php $j++; endforeach;?>

			    {
			        x: <?php echo $tmp ;?>,
			        y: <?php echo substr($article->field_published_date->value,0,4) ;?>,
			        z: 63,
			        name: '<?php echo $article->title->value ;?>',
			        color: '#FFFFFF',
			        events: {
			            click: function (event) {
			                jQuery.ajax({
			                    url: 'http://emor.zdhdemo.com/node/<?php echo $article->nid->value;?>?_format=json',
			                    method: 'GET',
			                    headers: {
			                        'Content-Type': 'application/json'
			                    },
			                    success: function(data, status, xhr) {
			                    	if(data.field_author_bubble!=''){
				                        jQuery.get( data.field_author_bubble[0].url+'/?_format=json', function( dt ) {
											jQuery('.article.infos .author span').text(dt.title[0].value);
		                                    if(dt.field_link!=''){
		                                        var $el = jQuery(".works .listWorks");
		                                        $el.empty();
		                                        jQuery.each(dt.field_link, function(key,value) {
		                                            $el.append(jQuery('<li></li>').append(jQuery('<a></a>').attr('href',value.uri).attr('target','_blank').text(value.title)));
		                                        });
		                                    }else{
		                                        jQuery(".works .listWorks").empty();
		                                    }
										});
									}
									jQuery('.article.infos .title span').text(data.title[0].value);
			                        if(data.field_location!='')
			                        	jQuery('.article.infos .location span').text(data.field_location[0].value);
			                        if(data.field_published_date!='')
			                        	jQuery('.article.infos .publish span').text(data.field_published_date[0].value);
			                        if(data.field_total_page!='')
			                        	jQuery('.article.infos .totalPage span').text(data.field_total_page[0].value);
			                        if(data.field_is_mapping!=''){
			                        	if(data.field_is_mapping[0].value == 0){
			                        		jQuery('.article.infos .inMapping span').text('No');
			                        	}else{
			                        		jQuery('.article.infos .inMapping span').text('Yes');
			                        	}
			                        }else{
			                        	jQuery('.article.infos .inMapping span').text('No');
			                        }
			                        if(data.field_language!='')
			                        	jQuery('.article.infos .language span').text(data.field_language[0].value);
			                        else
			                        	jQuery('.article.infos .language span').text('English');
			                        jQuery('#showArticlePopup').click();
                                    jQuery('#bubbleDetail').show();
                                    jQuery('#bubbleDetail .infos.author').slideUp('slow');
                                    jQuery('#bubbleDetail .infos.article').slideDown('slow');
			                    }
			                })
			            }
			        }
			    },<?php $i++; endforeach ?>],cats: ['',<?php foreach ($answers as $answer) {echo "'".$answer->title->value."',";}?>]
			    <?php if(isset($_GET['reloadFilter'])):?>
				    ,titles: [<?php foreach ($titles as $title) {echo "'".$title."',";}?>]
				    ,years: [<?php foreach ($years as $year) {echo "'".$year."',";}?>]
				    ,positions: [<?php foreach ($positions as $position) {echo "'".$position."',";}?>]
				    ,locations: [<?php foreach ($locations as $location) {echo "'".$location."',";}?>]
				    ,mediaTypes: [<?php foreach ($mediaTypes as $mediaType) {echo "'".$mediaType."',";}?>]
				    ,pageLengths : [<?php foreach ($pageLengths as $pageLength) {echo "'".$pageLength."',";}?>]
				    ,authors : {<?php foreach ($authors as $key => $author) {echo $key.":'".$author."',";}?>}
				<?php endif;?>
			}
			<?php
			break;
		
		case 'video':
			// get all articles video depend on answers
			$arIds = array();
			$mediaTypes = $authors = $positions = $titles = $years = $locations = $pageLength = array();
			foreach ($answers as $answer) {
				$tmp = $answer->get('field_articles_video');
				foreach ($tmp as $item) {
				  $arIds[] = (int)$item->target_id;
				}
			}
			$articles = $storage->loadMultiple($arIds); ?>
			{
				dataSeries:[
				<?php $i=1; foreach ($articles as $article): ?>
				<?php 
					$tmp = $article->get('field_author_bubble');
					$atIds = array();
					foreach ($tmp as $item) {
					  $atIds[] = (int)$item->target_id;
					}
					$auts = $storage->loadMultiple($atIds);

					if(isset($_GET['reloadFilter'])){
			    		if($article->field_location->value!='' && !in_array($article->field_location->value, $locations))
			        		$locations[] = $author->field_location->value;

			    		if($article->title->value!='' && !in_array($article->title->value, $titles))
			        		$titles[] = $article->title->value;

						$year = substr($article->field_published_date->value,0,4);
			    		if($year!='' && !in_array($year, $years))
			        		$years[] = $year;

			        	foreach ($auts as $aut) {
			        		$authors[$aut->nid->value] = $aut->title->value;
			        	}
			      	}
			    ?>
				<?php if($isFilterLoc && strpos($article->field_location->value, $_GET['loc']) === false) continue;?>
				<?php if($isFilterAuthor && ($article->field_author_bubble->target_id != $_GET['author'])) continue;?>
				<?php if($isFilterYear && strpos($article->field_published_date->value, $_GET['year']) === false) continue;?>
				<?php 
				if(is_null($article->field_language->value)) $article->field_language->value = 'English';
				if($isFilterLang && ($article->field_language->value != $_GET['language'])) continue;
				if($isFilterLength && ($article->field_total_page->value != $_GET['length'])) continue;
				if(is_null($article->field_is_mapping->value)) $article->field_is_mapping->value = 0;
				if($isFilterismapping && ($article->field_is_mapping->value != $_GET['is_mapping'])) continue;
				?>
				<?php if($isFilterName && strpos($article->title->value, $_GET['name']) === false) continue;?>
			    <?php $tmp = $i;?>
			    <?php $j=1; foreach ($answers as $answer):?>
			        <?php $tmp1 = $j; ?>
			        <?php foreach ($answer->field_articles_video as $ar):?>
			            <?php if ($article->nid->value == $ar->target_id): ?>
			                <?php $tmp = $tmp1 ?>
			            <?php endif; ?>
			        <?php endforeach; ?>
			    <?php $j++; endforeach;?>

			    {
			        x: <?php echo $tmp ;?>,
			        y: <?php echo substr($article->field_published_date->value,0,4) ;?>,
			        z: 63,
			        name: '<?php echo $article->title->value ;?>',
			        color: '#FFFFFF',
			        events: {
			            click: function (event) {
			                jQuery.ajax({
			                    url: 'http://emor.zdhdemo.com/node/<?php echo $article->nid->value;?>?_format=json',
			                    method: 'GET',
			                    headers: {
			                        'Content-Type': 'application/json'
			                    },
			                    success: function(data, status, xhr) {
			                    	if(data.field_author_bubble!=''){
				                        jQuery.get( data.field_author_bubble[0].url+'/?_format=json', function( dt ) {
											jQuery('.article.infos .author span').text(dt.title[0].value);
		                                    if(dt.field_link!=''){
		                                        var $el = jQuery(".works .listWorks");
		                                        $el.empty();
		                                        jQuery.each(dt.field_link, function(key,value) {
		                                            $el.append(jQuery('<li></li>').append(jQuery('<a></a>').attr('href',value.uri).attr('target','_blank').text(value.title)));
		                                        });
		                                    }else{
		                                        jQuery(".works .listWorks").empty();
		                                    }
										});
									}
			                        jQuery('.article.infos .title span').text(data.title[0].value);
			                        if(data.field_location!='')
			                        	jQuery('.article.infos .location span').text(data.field_location[0].value);
			                        if(data.field_published_date!='')
			                        	jQuery('.article.infos .publish span').text(data.field_published_date[0].value);
			                        if(data.field_total_page!='')
			                        	jQuery('.article.infos .totalPage span').text(data.field_total_page[0].value);
			                        if(data.field_is_mapping!=''){
			                        	if(data.field_is_mapping[0].value == 0){
			                        		jQuery('.article.infos .inMapping span').text('No');
			                        	}else{
			                        		jQuery('.article.infos .inMapping span').text('Yes');
			                        	}
			                        }else{
			                        	jQuery('.article.infos .inMapping span').text('No');
			                        }
			                        if(data.field_language!='')
			                        	jQuery('.article.infos .language span').text(data.field_language[0].value);
			                        else
			                        	jQuery('.article.infos .language span').text('English');
			                        jQuery('#showArticlePopup').click();
                                    jQuery('#bubbleDetail').show();
                                    jQuery('#bubbleDetail .infos.author').slideUp('slow');
                                    jQuery('#bubbleDetail .infos.article').slideDown('slow');
			                    }
			                })
			            }
			        }
			    },<?php $i++; endforeach ?>],cats: ['',<?php foreach ($answers as $answer) {echo "'".$answer->title->value."',";}?>]
			    <?php if(isset($_GET['reloadFilter'])):?>
				    ,titles: [<?php foreach ($titles as $title) {echo "'".$title."',";}?>]
				    ,years: [<?php foreach ($years as $year) {echo "'".$year."',";}?>]
				    ,positions: [<?php foreach ($positions as $position) {echo "'".$position."',";}?>]
				    ,locations: [<?php foreach ($locations as $location) {echo "'".$location."',";}?>]
				    ,mediaTypes: [<?php foreach ($mediaTypes as $mediaType) {echo "'".$mediaType."',";}?>]
				    ,pageLengths : [<?php foreach ($pageLengths as $pageLength) {echo "'".$pageLength."',";}?>]
				    ,authors : [<?php foreach ($authors as $author) {echo "'".$author."',";}?>]
				<?php endif;?>
			}
			<?php
			break;
		
		case 'audio':
			// get all articles audio depend on answers
			$auIds = array();
			$mediaTypes = $authors = $positions = $titles = $years = $locations = $pageLength = array();
			foreach ($answers as $answer) {
				$tmp = $answer->get('field_articles_audio');
				foreach ($tmp as $item) {
				  $arIds[] = (int)$item->target_id;
				}
			}
			$articles = $storage->loadMultiple($arIds); ?>
			{
				dataSeries:[
				<?php $i=1; foreach ($articles as $article):?>
				<?php 
					$tmp = $article->get('field_author_bubble');
					$atIds = array();
					foreach ($tmp as $item) {
					  $atIds[] = (int)$item->target_id;
					}
					$auts = $storage->loadMultiple($atIds);

					if(isset($_GET['reloadFilter'])){
			    		if($article->field_location->value!='' && !in_array($article->field_location->value, $locations))
			        		$locations[] = $author->field_location->value;

			    		if($article->title->value!='' && !in_array($article->title->value, $titles))
			        		$titles[] = $article->title->value;

						$year = substr($article->field_published_date->value,0,4);
			    		if($year!='' && !in_array($year, $years))
			        		$years[] = $year;

			        	foreach ($auts as $aut) {
			        		$authors[$aut->nid->value] = $aut->title->value;
			        	}
			      	}
			    ?>
				<?php if($isFilterLoc && strpos($article->field_location->value, $_GET['loc']) === false) continue;?>
				<?php if($isFilterAuthor && ($article->field_author_bubble->target_id != $_GET['author'])) continue;?>
				<?php if($isFilterYear && strpos($article->field_published_date->value, $_GET['year']) === false) continue;?>
				<?php 
				if(is_null($article->field_language->value)) $article->field_language->value = 'English';
				if($isFilterLang && ($article->field_language->value != $_GET['language'])) continue;
				if($isFilterLength && ($article->field_total_page->value != $_GET['length'])) continue;
				if(is_null($article->field_is_mapping->value)) $article->field_is_mapping->value = 0;
				if($isFilterismapping && ($article->field_is_mapping->value != $_GET['is_mapping'])) continue;
				?>
				<?php if($isFilterName && strpos($article->title->value, $_GET['name']) === false) continue;?>
			    <?php $tmp = $i;?>
			    <?php $j=1; foreach ($answers as $answer):?>
			        <?php $tmp1 = $j; ?>
			        <?php foreach ($answer->field_articles_audio as $ar):?>
			            <?php if ($article->nid->value == $ar->target_id): ?>
			                <?php $tmp = $tmp1 ?>
			            <?php endif; ?>
			        <?php endforeach; ?>
			    <?php $j++; endforeach;?>

			    {
			        x: <?php echo $tmp ;?>,
			        y: <?php echo substr($article->field_published_date->value,0,4) ;?>,
			        z: 63,
			        name: '<?php echo $article->title->value ;?>',
			        color: '#FFFFFF',
			        events: {
			            click: function (event) {
			                jQuery.ajax({
			                    url: 'http://emor.zdhdemo.com/node/<?php echo $article->nid->value;?>?_format=json',
			                    method: 'GET',
			                    headers: {
			                        'Content-Type': 'application/json'
			                    },
			                    success: function(data, status, xhr) {
			                    	if(data.field_author_bubble!=''){
				                        jQuery.get( data.field_author_bubble[0].url+'/?_format=json', function( dt ) {
											jQuery('.article.infos .author span').text(dt.title[0].value);
		                                    if(dt.field_link!=''){
		                                        var $el = jQuery(".works .listWorks");
		                                        $el.empty();
		                                        jQuery.each(dt.field_link, function(key,value) {
		                                            $el.append(jQuery('<li></li>').append(jQuery('<a></a>').attr('href',value.uri).attr('target','_blank').text(value.title)));
		                                        });
		                                    }else{
		                                        jQuery(".works .listWorks").empty();
		                                    }
										});
									}
			                        jQuery('.article.infos .title span').text(data.title[0].value);
			                        if(data.field_location!='')
			                        	jQuery('.article.infos .location span').text(data.field_location[0].value);
			                        if(data.field_published_date!='')
			                        	jQuery('.article.infos .publish span').text(data.field_published_date[0].value);
			                        if(data.field_total_page!='')
			                        	jQuery('.article.infos .totalPage span').text(data.field_total_page[0].value);
			                        if(data.field_is_mapping!=''){
			                        	if(data.field_is_mapping[0].value == 0){
			                        		jQuery('.article.infos .inMapping span').text('No');
			                        	}else{
			                        		jQuery('.article.infos .inMapping span').text('Yes');
			                        	}
			                        }else{
			                        	jQuery('.article.infos .inMapping span').text('No');
			                        }
			                        if(data.field_language!='')
			                        	jQuery('.article.infos .language span').text(data.field_language[0].value);
			                        else
			                        	jQuery('.article.infos .language span').text('English');
			                        jQuery('#showArticlePopup').click();
                                    jQuery('#bubbleDetail').show();
			                        jQuery('#bubbleDetail .infos.article').slideDown('slow');
                                    jQuery('#bubbleDetail .infos.author').slideUp('slow');
			                    }
			                })
			            }
			        }
			    },<?php $i++; endforeach ?>],cats: ['',<?php foreach ($answers as $answer) {echo "'".$answer->title->value."',";}?>]
			    <?php if(isset($_GET['reloadFilter'])):?>
				    ,titles: [<?php foreach ($titles as $title) {echo "'".$title."',";}?>]
				    ,years: [<?php foreach ($years as $year) {echo "'".$year."',";}?>]
				    ,positions: [<?php foreach ($positions as $position) {echo "'".$position."',";}?>]
				    ,locations: [<?php foreach ($locations as $location) {echo "'".$location."',";}?>]
				    ,mediaTypes: [<?php foreach ($mediaTypes as $mediaType) {echo "'".$mediaType."',";}?>]
				    ,pageLengths : [<?php foreach ($pageLengths as $pageLength) {echo "'".$pageLength."',";}?>]
				    ,authors : [<?php foreach ($authors as $author) {echo "'".$author."',";}?>]
				<?php endif;?>
			}
			<?php
			break;

		default:
			// get all authors depend on answers
			$auIds = array();
			$cities = array();
			$names = array();
			$years = array();
			$positions = array();
			foreach ($answers as $answer) {
				$tmp = $answer->get('field_author_bubble');
				foreach ($tmp as $item) {
				  $auIds[] = (int)$item->target_id;
				}
			}
			$authors = $storage->loadMultiple($auIds); ?>
			{
				dataSeries:[
				<?php $i=1; foreach ($authors as $author):?>
				<?php 
					if(isset($_GET['reloadFilter'])){
			    		if($author->field_city->value!='' && !in_array($author->field_city->value, $cities))
			        		$cities[] = $author->field_city->value;
			    		if($author->title->value!='' && !in_array($author->title->value, $names))
			        		$names[] = $author->title->value;
			    		if($author->field_author_position->value!='' && !in_array($author->field_author_position->value, $positions))
			        		$positions[] = $author->field_author_position->value;
						$dob = substr($author->field_dob->value,0,4);
			    		if($dob!='' && !in_array($dob, $years))
			        		$years[] = $dob;
			      	}
			    ?>
				<?php if($isFilterLoc && strpos($author->field_city->value, $_GET['loc']) === false) continue;?>
				<?php if($isFilterName && strpos($author->title->value, $_GET['name']) === false) continue;?>
				<?php if($isFilterYear && strpos($author->field_dob->value, $_GET['year']) === false) continue;?>
			    <?php $tmp = $i;?>
			    <?php $j=1; foreach ($answers as $answer):?>
			        <?php $tmp1 = $j; ?>
			        <?php foreach ($answer->field_author_bubble as $au):?>
			            <?php if ($author->nid->value == $au->target_id): ?>
			                <?php $tmp = $tmp1 ?>
			            <?php endif; ?>
			        <?php endforeach; ?>
			    <?php $j++; endforeach;?>
			    {
			        x: <?php echo $tmp ;?>,
			        y: <?php echo substr($author->field_dob->value,0,4) ;?>,
			        z: <?php if (empty($author->field_size_bubble)):?> 63 <?php else: ?> <?php echo $author->field_size_bubble->value;?> <?php endif; ?>,
			        name: '<?php echo $author->title->value ;?>',
			        color: '<?php if(empty($author->field_color_bubble)):?> #FFFFFF <?php else: ?> <?php echo $author->field_color_bubble->value ;?> <?php endif; ?>',
			        events: {
			            click: function (event) {
			                jQuery.ajax({
			                    url: 'http://emor.zdhdemo.com/node/<?php echo $author->nid->value;?>?_format=json',
			                    method: 'GET',
			                    headers: {
			                        'Content-Type': 'application/json'
			                    },
			                    success: function(data, status, xhr) {
                                    jQuery('.author.infos .name span').text(data.title[0].value);
                                    jQuery('.author.infos .location span').text(data.field_city[0].value + ' ' + data.field_country[0].value);
                                    jQuery('.author.infos .dob span').text(data.field_dob[0].value);
                                    if(data.field_dod!='')
                                        jQuery('.author.infos .dod span').text(data.field_dod[0].value);
                                    else
                                        jQuery('.author.infos .dod span').text('');

                                    if(data.field_author_position!='')
                                        jQuery('.author.infos .position span').text(data.field_author_position[0].value);
                                    else
                                        jQuery('.author.infos .position span').text('');

                                    if(data.field_language!='')
                                        jQuery('.author.infos .language span').text(data.field_language[0].value);
                                    else
                                        jQuery('.author.infos .language span').text('English');

                                    if(data.field_profile_photo!=''){
                                        jQuery('.author.infos .authorAvatar').attr({
                                            src: data.field_profile_photo[0].url,
                                            alt: data.field_profile_photo[0].alt,
                                        });
                                    }else{
                                        jQuery('.author.infos .authorAvatar').attr({
                                            src: '',
                                            alt: '',
                                        });
                                    }

                                    if(data.field_link!=''){
                                        var $el = jQuery(".works .listWorks");
                                        $el.empty();
                                        jQuery.each(data.field_link, function(key,value) {
                                            $el.append(jQuery('<li></li>').append(jQuery('<a></a>').attr('href',value.uri).attr('target','_blank').text(value.title)));
                                        });
                                    }else{
                                        jQuery(".works .listWorks").empty();
                                    }

                                    jQuery('#showAuthorPopup').click();
                                    jQuery('#bubbleDetail').show();
                                    jQuery('#bubbleDetail .infos.article').slideUp('slow');
                                    jQuery('#bubbleDetail .infos.author').slideDown('slow');
			                    }
			                })
			            }
			        }
			    },<?php $i++; endforeach ?>],cats: ['',<?php foreach ($answers as $answer) {echo "'".$answer->title->value."',";}?>]
			    <?php if(isset($_GET['reloadFilter'])):?>
				    ,cities: [<?php foreach ($cities as $city) {echo "'".$city."',";}?>]
				    ,names: [<?php foreach ($names as $name) {echo "'".$name."',";}?>]
				    ,years: [<?php foreach ($years as $year) {echo "'".$year."',";}?>]
				    ,positions: [<?php foreach ($positions as $position) {echo "'".$position."',";}?>]
				<?php endif;?>
			}
			<?php
			break;
	endswitch;