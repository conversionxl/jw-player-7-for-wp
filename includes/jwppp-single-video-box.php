<?php
/*
*
* SINGLE VIDEO BOX FOR JW PLAYER FOR WORDPRESS PREMIUM
*
*/


$dashboard_player = is_dashboard_player();
$player_position = get_option('jwppp-position');

$output = null;
$output .= '<table class="widefat jwppp-' . esc_attr($number) . '" style="margin: 0.4rem 0;">';
$output .= '<tbody class="ui-sortable">';
 /*Class for preview image dimensions*/
$image_class = null;
if($player_position !== 'custom') {
	$image_class = ' small'; 
} elseif(!$dashboard_player) {
	$image_class = ' medium'; 
} 

$output .= '<tr class="row">';
$output .= '<td class="order">' . esc_attr($number) . '</td>';
$output .= '<td class="jwppp-input-wrap' . $image_class . '" style="width: 100%; padding-bottom: 1rem;">';
wp_nonce_field( 'jwppp_save_single_video_data', 'jwppp-meta-box-nonce-' . $number );

/*Single video details*/
$video_url = get_post_meta($post_id, '_jwppp-video-url-' . $number, true );

/*Is the video self hosted?*/
$sh_video = strrpos($video_url, 'http') === 0 ? true : false;

$sources_number = get_post_meta($post_id, '_jwppp-sources-number-' . $number, true);
$main_source_label = get_post_meta($post_id, '_jwppp-' . $number . '-main-source-label', true );

/*Thumbnail*/
$video_image = null;
if($video_url && $video_url !== '1') {
	if($sh_video) {
		$video_image = get_post_meta($post_id, '_jwppp-video-image-' . $number, true);
	} else {
		$single_video_image = 'https://cdn.jwplayer.com/thumbs/' . $video_url . '-720.jpg';
		if(@getimagesize($single_video_image)) {
			$video_image = $single_video_image;
		} else {
			$video_image = plugin_dir_url(__DIR__) . 'images/playlist4.png';
		}
	}
}
if($video_image) {
	$output .= '<img class="poster-image-preview ' . $number . (!$dashboard_player ? ' small' : '') . '" src="' . esc_html($video_image) . '">';
}	

if($dashboard_player) {

	$output .= '<ul class="jwppp-video-toggles ' . $number . '">';
		$output .= '<li data-video-type="choose"' . (!$sh_video ? ' class="active"' : '') . '>' . esc_html(__('Choose', 'jwppp')) . '</li>';
		$output .= '<li data-video-type="add-url"' . ($sh_video ? ' class="active"' : '') . '>' . esc_html(__('Add url', 'jwppp')) . '</li>';
		$output .= '<div class="clear"></div>';
	$output .= '</ul>';

	/*Select*/
	$output .= '<div class="jwppp-toggle-content ' . esc_attr($number) . ' choose' . (!$sh_video ? ' active' : '') . '">';
		$output .= '<p>';

			$api = new jwppp_dasboard_api();

			if($api && $api->account_validation()) {

				$output .= '<select id="_jwppp-video-url-' . esc_attr($number) . '" name="_jwppp-video-url-' . esc_attr($number) . '" class="select2" style="margin-right:1rem;">';

					$videos = $api->get_videos();
					$playlists = $api->get_playlists();

					/*Videos*/
					$output .= '<option name="" value="">' . esc_html('Select a video', 'jwppp') . '</option>';
					if($videos) {
						// for ($i=0; $i < min(10, count($videos)); $i++) { 
						// 	$output .= '<option name="' . $videos[$i]['key'] . '" data-mediaid="' . $videos[$i]['key'] . '" value="' . $videos[$i]['key'] . '"' . ($video_url === $videos[$i]['key'] ? ' selected="selecterd"' : '') . '>' . $videos[$i]['title'] . '</option>';
						// }
						foreach ($videos as $video) {
							$output .= '<option name="' . $video['key'] . '" data-mediaid="' . $video['key'] . '" value="' . $video['key'] . '"' . ($video_url === $video['key'] ? ' selected="selecterd"' : '') . '>' . $video['title'] . '</option>';
						}
					}

					/*Playlists*/
					$output .= '<option name="" value="">' . esc_html('Select a playlist', 'jwppp') . '</option>';
					if($playlists) {
						foreach ($playlists as $playlist) {
							$output .= '<option name="' . $playlist['key'] . '" class="playlist-element" data-mediaid="' . $playlist['key'] . '" value="' . $playlist['key'] . '"' . ($video_url === $playlist['key'] ? ' selected="selecterd"' : '') . '>' . $playlist['title'] . '</option>';
						}
					}

				$output .= '</select>';

			} elseif($api->args_check() && !$api->account_validation()) {

				$output .= '<span class="jwppp-alert api">' . esc_html('It seems like your API Credentials are not correct.', 'jwppp') . '</span>';

			} elseif(!$api->args_check()) {

				$output .= '<span class="jwppp-alert api">' . esc_html('API Credentials are required for using this tool.', 'jwppp') . '</span>';

			}

		$output .= '</p>';		
	$output .= '</div>';	

} 

/*Input*/
$output .= $dashboard_player ? '<div class="jwppp-toggle-content ' . esc_attr($number) . ' add-url' . ($sh_video ? ' active' : '') . '">' : '';

	if(!$dashboard_player) {
		$output .= '<label for="_jwppp-video-url-' . esc_attr($number) . '">';
		$output .= '<strong>' . esc_html(__( 'Media URL', 'jwppp' )) . '</strong>';
		$output .= '<a class="question-mark" href="http://www.ilghera.com/support/topic/media-formats-supported/" title="More informations" target="_blank"><img class="question-mark" src="' . esc_url(plugin_dir_url(__DIR__)) . 'images/question-mark.png" /></a></th>';
		$output .= '</label> ';
	}

	$output .= '<p>';
		$output .= '<input type="text" id="_jwppp-video-url-' . esc_attr($number) . '" class="jwppp-url" name="_jwppp-video-url-' . esc_attr($number) . '" placeholder="' . esc_html(__('Add here your media url', 'jwppp')) . '" ';
		$output .= ($video_url !== '1') ? 'value="' . esc_attr( $video_url ) . '" ' : 'value="" ';
		$output .= 'size="60" />';

		$output .= '<input type="text" name="_jwppp-' . esc_attr($number) . '-main-source-label" id ="_jwppp-' . esc_attr($number) . '-main-source-label" class="source-label-' . esc_attr($number) . '" style="margin-right:1rem; display: none;';
		$output .= '" value="' . esc_html($main_source_label) . '" placeholder="' . esc_html(__('Label (HD, 720p, 360p)', 'jwppp')) . '" size="30" />';

	$output .= '</p>';

$output .= $dashboard_player ? '</div>' : '';

if(get_option('jwppp-position') === 'custom') {
	$output .= '<code style="display:inline-block;margin:0.1rem 0.5rem 0 0;color:#888;">[jwp-video n="' . esc_attr($number) . '"]</code>';
}

$more_options_button = '<a class="button more-options-' . esc_attr($number) . '">' . esc_html(__('More options', 'jwppp')) . '</a>';
if(!$dashboard_player || $sh_video) {
	$output .= $more_options_button;
}

if($dashboard_player && !$sh_video) {
	$jwppp_playlist_carousel = get_post_meta($post_id, '_jwppp-playlist-carousel-' . $number, true);
	$output .= '<div class="playlist-carousel-container ' . esc_attr($number) . '"' . ($jwppp_playlist_carousel ? ' style="display: inline-block;"' : '') . '>';
		$output .= '<label for="_jwppp-playlist-carousel-' . esc_attr($number) . '">';
		$output .= '<input type="checkbox" id="_jwppp-playlist-carousel-' . esc_attr($number) . '" name="_jwppp-playlist-carousel-' . esc_attr($number) . '" value="1"';
		$output .= ($jwppp_playlist_carousel === '1') ? ' checked="checked"' : '';
		$output .= ' />';
		$output .= '<strong>' . esc_html(__('Show carousel', 'jwppp')) . '</strong>';
		$output .= '</label>';
		$output .= '<input type="hidden" name="playlist-carousel-hidden-' . esc_attr($number) . '" value="1" />';
	$output .= '</div>';	
}

?>

<!-- BASIC SINGLE VIDEO OPTIONS -->
<script>
(function($) {
	$(document).ready(function() {
		var post_id = <?php echo $post_id; ?>;
		var number = <?php echo $number; ?>;
		var $url = $('#_jwppp-video-url-' + number).val();
		var $ext = $url.split('.').pop();
		var $arr = ['xml', 'feed', 'php', 'rss'];
		var $more_options_button = '<?php echo $more_options_button; ?>';



		/*Video toggles*/
		$(document).on('click', '.jwppp-video-toggles.' + number + ' li', function(){
			$('.jwppp-video-toggles.' + number + ' li').removeClass('active');
			$(this).addClass('active');

			var video_type = $(this).data('video-type');

			$('.jwppp-toggle-content.' + number).removeClass('active');
			$('.jwppp-toggle-content.' + number + '.' + video_type).addClass('active');

			/*Delete the input field value on toggle change*/
			$('input#_jwppp-video-url-' + number).val('');
			$('select#_jwppp-video-url-' + number).val('').trigger('change');

			/*With cloud player and self hosted sources, all the tools are shown*/
			if(video_type === 'add-url') {

				/*Add the More options button*/
				// console.log($('.jwppp-' + number + ' .jwppp-input-wrap').find('.more-options-' + number));
				if($('.jwppp-' + number + ' .jwppp-input-wrap').find('.more-options-' + number).length === 0) {
					$('.jwppp-' + number + ' .jwppp-input-wrap').append($more_options_button);
				}

				var data = {
					'action': 'self-media-source',
					'confirmation': 1,
					'post-id': post_id,
					'number': number
				}
				$.post(ajaxurl, data, function(response){
					$(response).appendTo($('.jwppp-' + number + ' .jwppp-input-wrap'));
					sh_video_script(number);
				})
			} else {
				$('.button.more-options-' + number).remove();
				$('.jwppp-more-options-' + number).remove();
			}

		})

		/*Changwe playlist-how-to*/
		var tot = $('.jwppp-input-wrap:visible').length;
		if(tot > 1) {
			$('.playlist-how-to').show('slow');
			
			var string = [];
			$('.order:visible').each(function(i, el) {
				string.push($(el).html());	
			})
			// $('.playlist-how-to code').html('[jwp-video n="' + string + '"]');
		} else {
			$('.playlist-how-to').hide();
		}

		$('.jwppp-more-options-' + number).hide();

		if($.inArray($ext, $arr)>-1) {
			$('.more-options-' + number).hide();
		};

		/*Media url change*/
		$(document).on('change','#_jwppp-video-url-' + number, function() {

			var $url = $(this).val();
			
			/*Getting the extension for old type playlist*/
			var $ext = $url.split('.').pop();
			var $arr = ['xml', 'feed', 'php', 'rss'];
			if($.inArray($ext, $arr)>-1) {
				$('.more-options-' + number).hide();
				$('.jwppp-more-options-' + number).hide();
			} else {
				$('.more-options-' + number).show();	
			}
		});

		if($('.jwppp-video-toggles.' + number + ' li.active').data('video-type') == 'choose' && $('select#_jwppp-video-url-' + number + ' option:selected').hasClass('playlist-element')) {
        	$('.playlist-carousel-container.' + number).css({
        		'display': 'inline-block'
        	})			
		}	

		/*Select value is required in input too*/
		$(document).on('change', 'select#_jwppp-video-url-' + number, function(){			
			$('input#_jwppp-video-url-' + number).val($(this).val());

			var width = $(document).width();

			//POSTER PREVIEW IMAGE
            if($(this).val() && width > 1112) {
            	var image_url = null;
            	if($('option:selected', this).hasClass('playlist-element')) {
	            	image_url = '../wp-content/plugins/jw-player-7-for-wp/images/playlist4.png';

	            	$('.playlist-carousel-container.' + number).css({
	            		'display': 'inline-block'
	            	})

            	} else {
	            	image_url = 'https://cdn.jwplayer.com/thumbs/' + $(this).val() + '-720.jpg'
					$('.playlist-carousel-container.' + number).hide();
					$('#_jwppp-playlist-carousel-' + number).removeAttr('checked');

					/*Delete carousel option from the db*/
					// var data = {
					// 	'action': 'remove-carousel-option',
					// 	'post_id': post_id,
					// 	'number': number
					// }
					// $.post(ajaxurl, data, function(response){
					// 	console.log(response);
					// })
            	}

                $('.poster-image-preview.' + number).remove();
                $('.jwppp-' + number + ' .jwppp-input-wrap').prepend('<img class="poster-image-preview ' + number + '" src="' + image_url + '" style="display: none;">');
                $('.poster-image-preview.' + number).fadeIn(300);
            } else {
                $('.poster-image-preview.' + number).fadeOut(300, function(){
                    $(this).remove();
                });
            }

		})
	});
})(jQuery);
</script>

<?php

if(!$dashboard_player || $sh_video) {

	/*Self hosted video tools*/
	$output .= sh_video_tools($post_id, $number);
	?>
	<script>
		jQuery(document).ready(function($){
			var number = <?php echo $number; ?>;
			sh_video_script(number);
		})
	</script>
	<?php
}

$output .= '</div>';
$output .= '</td>';

if($number<2) {
	$output .= '<td class="add-video"><a class="jwppp-add"><img src="' . esc_url(plugin_dir_url(__DIR__)) . 'images/add-video.png" /></a></td>';
} else {
	$output .= '<td class="remove-video"><a class="jwppp-remove" data-numb="' . esc_attr($number) . '"><img src="' . esc_url(plugin_dir_url(__DIR__)) . 'images/remove-video.png" /></a></td>';
}
$output .= '</tr>';
$output .= '</tbody>';
$output .= '</table>';

echo $output;