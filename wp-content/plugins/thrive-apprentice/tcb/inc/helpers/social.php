<?php
/**
 * Created by PhpStorm.
 * User: radu
 * Date: 18.06.2015
 * Time: 16:46
 */

/**
 * get the javascript sdk source link for a social network.
 * For Pinterest network another logic has been applied: a script had to be executed
 *
 * @param string $handle
 *
 * @return string the link to the javascript sdk for the network
 */
function tve_social_get_sdk_link( $handle ) {
	switch ( $handle ) {
		case 'fb':
			$app_id = tve_get_social_fb_app_id();
			$app_id = $app_id ? '&appId=' . $app_id : '';

			return '//connect.facebook.net/en_US/sdk.js#xfbml=0' . $app_id . '&version=v2.9';
		case 'twitter':
			return '//platform.twitter.com/widgets.js';
		case 'linkedin':
			return '//platform.linkedin.com/in.js';
		case 'xing':
			return '//www.xing-share.com/plugins/share.js';
	}
}

/**
 * all networks that have custom styles
 *
 * @return array
 */
function tve_social_get_custom_networks() {
	return [
		'fb_share',
		't_share',
		'pin_share',
		'in_share',
		'xing_share',
	];
}

/**
 * render the default social sharing buttons
 * this is implemented as a shortcode (having a JSON configuration saved from the editor
 *
 * @param array $config
 *
 * @return string the rendered data
 */
function tve_social_render_default( $config ) {
	$html         = '<div class="thrive-shortcode-html"><div class="tve_social_items tve_clearfix">';
	$custom_class = '';
	if ( ! empty( $config['selected'] ) ) {
		$buttons = tve_social_networks_default_html( null, $config );
		foreach ( $config['selected'] as $item ) {
			if ( $item === 'pin_share' && $config['btn_type'] === 'btn_count' ) {
				$custom_class = 'tve_s_pin_share_count';
			}
			$html .= '<div class="tve_s_item ' . $custom_class . ' tve_s_' . $item . '" data-s="' . $item . '">' . $buttons[ $item ] . '</div>';
		}
	}
	$html .= '</div></div>';

	return $html;
}

/**
 *
 * this applied only to default share buttons
 *
 * get the default html for each of the social buttons
 * usually, there would be a <div> element with some configuration for each social button
 *
 * @param string|null $network     allows returning the html for just a single button
 * @param array       $config      button configuration
 * @param bool        $editor_page whether or not we are on the editor page
 *
 * @return array|string
 */
function tve_social_networks_default_html( $network = null, $config = [], $editor_page = false ) {
	$defaults = [
		'btn_type' => 'btn',
	];
	$config   = array_merge( $defaults, $config );

	if ( ! empty( $network ) && function_exists( 'tve_social_network_default_' . $network ) ) {
		return call_user_func( 'tve_social_network_default_' . $network, $config, $editor_page );
	}

	$networks = [
		'fb_share',
		'fb_like',
		't_share',
		't_follow',
		'in_share',
		'pin_share',
		'xing_share',
	];

	$html = [];
	foreach ( $networks as $network ) {
		$html[ $network ] = call_user_func( 'tve_social_network_default_' . $network, $config, $editor_page );
	}

	return $html;
}

/**
 * default fb share button
 *
 * @param array $config
 *
 * @return string
 */
function tve_social_network_default_fb_share( $config ) {
	//this function is also used when the control panel is loaded through AJAX request and we don't have access yet at get_the_ID() function
	$post_id = get_the_ID();
	if ( ! $post_id && ! empty( $_POST['post_id'] ) ) {
		$post_id = (int) $_POST['post_id'];
	}

	return sprintf(
		'<div class="fb-share-button" data-href="%s" data-layout="%s"></div>',
		! empty( $config['fb_share']['href'] ) ? $config['fb_share']['href'] : get_permalink( $post_id ),
		$config['btn_type'] == 'btn' ? 'button' : 'button_count'
	);
}

/**
 * default fb like button
 *
 * @param array $config
 *
 * @return string
 */
function tve_social_network_default_fb_like( $config ) {
	return sprintf(
		'<div class="fb-like" data-href="%s" data-layout="%s" data-send="false"></div>',
		! empty( $config['fb_like']['href'] ) ? $config['fb_like']['href'] : '{tcb_post_url}',
		$config['btn_type'] == 'btn' ? 'button' : 'button_count'
	);
}

/**
 * default twitter tweet button
 *
 * @param array $config
 *
 * @return string
 */
function tve_social_network_default_t_share( $config ) {
	return sprintf(
		'<a href="https://twitter.com/share" class="twitter-share-button" %s %s %s %s></a>',
		! empty( $config['t_share']['href'] ) ? 'data-url="' . $config['t_share']['href'] . '"' : '',
		! empty( $config['t_share']['tweet'] ) ? 'data-text="' . $config['t_share']['tweet'] . '"' : '',
		! empty( $config['t_share']['via'] ) ? 'data-via="' . $config['t_share']['via'] . '"' : '',
		$config['btn_type'] == 'btn' ? ' data-count="none"' : ''
	);
}

/**
 * default twitter follow button
 *
 * @param array $config
 *
 * @return string
 */
function tve_social_network_default_t_follow( $config ) {
	$username = ! empty( $config['t_follow']['username'] ) ? trim( $config['t_follow']['username'], "@" ) : "";

	return sprintf(
		'<a href="https://twitter.com/%s" class="twitter-follow-button" %s %s>Follow</a>',
		$username,
		$config['btn_type'] == 'btn' ? 'data-show-count="false"' : '',
		! empty( $config['t_follow']['hide_username'] ) ? 'data-show-screen-name="false"' : ''

	);
}

/**
 * default linkedin button
 *
 * @param array $config
 *
 * @return string
 */
function tve_social_network_default_in_share( $config ) {
	return sprintf(
		'<script type="IN/Share" data-showZero="true" %s data-url="%s"></script>',
		$config['btn_type'] == 'btn_count' ? 'data-counter="right"' : '',
		! empty( $config['in_share']['href'] ) ? $config['in_share']['href'] : '{tcb_post_url}'
	);
}

/**
 * default pinterest button
 *
 * @param array $config
 * @param bool  $editor_page
 *
 * @return string
 */
function tve_social_network_default_pin_share( $config, $editor_page ) {
	$html = sprintf(
		'<a href="//www.pinterest.com/pin/create/button/?url=%s&media=%s&description=%s" data-pin-do="buttonPin" %s data-pin-color="red"></a>',
		! empty( $config['pin_share']['href'] ) ? urlencode( $config['pin_share']['href'] ) : '{tcb_encoded_post_url}',
		! empty( $config['pin_share']['media'] ) ? $config['pin_share']['media'] : '{tcb_post_image}',
		! empty( $config['pin_share']['description'] ) ? $config['pin_share']['description'] : '{tcb_post_title}',
		$config['btn_type'] == 'btn_count' ? 'data-pin-config="beside" data-pin-zero="true"' : ''
	);
	if ( ! $editor_page ) {
		$html .= <<< EOT
<script type="text/javascript">
    (function () {
        window.PinIt = window.PinIt || {loaded: false};
        if (window.PinIt.loaded) {
            return;
        }
        window.PinIt.loaded = true;
        function async_load() {
            var s = document.createElement("script");
            s.type = "text/javascript";
            s.async = true;
            s.src = "//assets.pinterest.com/js/pinit.js";
            s["data-pin-build"] = "parsePins";
            var x = document.getElementsByTagName("script")[0];
            x.parentNode.insertBefore(s, x);
        }

        if (window.attachEvent) {
            window.attachEvent("onload", async_load);
        } else {
            window.addEventListener("load", async_load, false);
        }
        if(typeof TCB_Front !== 'undefined') {
            ThriveGlobal.\$j(TCB_Front).on('tl-ajax-loaded', async_load);
        }
    })();
</script>
EOT;
	}

	return $html;
}

/**
 * default xing button
 *
 * @param array $config
 *
 * @return string
 */
function tve_social_network_default_xing_share( $config ) {
	return sprintf(
		'<div data-type="xing/share" %s data-url="%s"></div>',
		$config['btn_type'] == 'btn_count' ? 'data-counter="true"' : '',
		! empty( $config['xing_share']['href'] ) ? $config['xing_share']['href'] : '{tcb_post_url}'
	);
}

/**
 * fetch and decode a JSON response from a URL
 *
 * @param string $url
 * @param string $fn
 *
 * @return array
 */
function _tve_social_helper_get_json( $url, $fn = 'wp_remote_get' ) {
	$response = $fn( $url, [ 'sslverify' => false ] );
	if ( $response instanceof WP_Error ) {
		return [];
	}

	$body = wp_remote_retrieve_body( $response );
	if ( empty( $body ) ) {
		return [];
	}

	$data = json_decode( $body, true );

	return empty( $data ) ? [] : $data;
}

/**
 * format big numbers in the form of 2.4K
 *
 * @param int $count
 */
function tve_social_count_format( $count ) {
	$suffixes = [ '', 'K', 'M', 'G' ];

	$suffixIndex = 0;

	while ( $count >= 1000 ) {
		$suffixIndex ++;
		$count /= 1000;
	}

	return $suffixIndex ? number_format( $count, 1, '.', '' ) . $suffixes[ $suffixIndex ] : $count;
}

/**
 * get the cached share count (or, if expired, fetch the count from the API for the network)
 *
 * @param mixed|null   $post_id
 * @param string       $post_permalink optional, if passed in will be used instead of get_permalink
 * @param array|string $networks       the network to fetch the share count for (can also be an array)
 * @param bool         $force_fetch    if true, it will bypass the cache and make the API request
 *
 * Allowed values for the network / network keys:
 *  'fb_share',
 *  't_share',
 *  'pin_share',
 *  'in_share',
 *  'xing_share'
 *
 * @return array
 * [fb_share] => $count
 * [t_share] => $count
 * ..
 */
function tve_social_get_share_count( $post_id, $post_permalink = null, $networks = null, $force_fetch = false ) {
	$cache_lifetime = apply_filters( 'thrive_cache_shares_lifetime', 300 );

	/* the share count is returned for a URL */
	if ( null === $post_permalink ) {
		$post_permalink = get_permalink( $post_id );
	}

	/**
	 * all possible networks
	 */
	$all_networks = [
		'fb_share',
		't_share',
		'pin_share',
		'in_share',
		'xing_share',
	];

	/* make sure the $networks will be an array */
	if ( $networks !== null ) {
		$networks = is_array( $networks ) ? $networks : [ $networks ];
		$networks = array_intersect( $networks, $all_networks );
	} else {
		$networks = $all_networks;
	}

	$count = get_post_meta( $post_id, 'thrive_ss_count', true );

	/* if no cache or if the URL has changed => re-fetch the whole thing */
	if ( empty( $count ) || $count['url'] != $post_permalink ) {
		$count       = [];
		$force_fetch = true;
	}

	/* cache expired => re-fetch */
	if ( ! empty( $count['last_fetch'] ) && $count['last_fetch'] < time() - $cache_lifetime ) {
		$force_fetch = true;
	}

	/* check to see if we have all of the required networks added to cache */
	if ( ! $force_fetch && ! empty( $count ) ) {

		foreach ( $networks as $network ) {
			if ( ! isset( $count[ $network ] ) ) {
				$force_fetch = true;
				break;
			}
		}
	}

	if ( $force_fetch ) {
		$count['url']        = $post_permalink;
		$count['last_fetch'] = time();
		foreach ( $networks as $network ) {
			$shares = call_user_func( 'tve_social_fetch_count_' . $network, $post_permalink );
			/* do not set the share count if it already exists and the value received from API is 0 */
			if ( isset( $count[ $network ] ) && empty( $shares ) ) {
				continue;
			}
			$count[ $network ] = $shares;
		}
		update_post_meta( $post_id, 'thrive_ss_count', $count );
	}

	return $count;
}

/**
 * get the social share count for a range of networks (array param), for all (empty) or for an individual network (string)
 *
 * this triggers API calls to the network
 *
 * @param string            $url the URL to get the shares for
 * @param null|array|string $for
 *
 * @return array | int | false for error
 */
function tve_social_fetch_count( $url, $for = null ) {
	$all = tve_social_get_custom_networks();

	$response = [];

	if ( $for === null ) {
		foreach ( $all as $network ) {
			$response[ $network ] = call_user_func( 'tve_social_fetch_count_' . $network, $url );
		}

		return $response;
	}

	if ( is_array( $for ) ) {
		$for = array_intersect( $for, $all );
		foreach ( $for as $network ) {
			$response[ $network ] = call_user_func( 'tve_social_fetch_count_' . $network, $url );
		}

		return $response;
	}

	if ( is_string( $for ) && in_array( $for, $all ) ) {
		return call_user_func( 'tve_social_fetch_count_' . $for, $url );
	}

	return false;
}

/**
 * fetch the FB total number of shares for an url
 *
 * @param string $url
 *
 * @return int
 */
function tve_social_fetch_count_fb_share( $url ) {
	/* not nice but the idea would be to use same function */
	return tve_dash_fetch_share_count_facebook( $url );
}

/**
 * fetch the total number of shares for an url from twitter
 *
 * @param string $url
 *
 * @return int
 */
function tve_social_fetch_count_t_share( $url ) {
	return 0;
}

/**
 * fetch the total number of shares for an url from Pinterest
 *
 * @param string $url
 *
 * @return int
 */
function tve_social_fetch_count_pin_share( $url ) {
	$response = wp_remote_get( 'https://api.pinterest.com/v1/urls/count.json?callback=_&url=' . rawurlencode( $url ), [
		'sslverify' => false,
	] );

	$body = wp_remote_retrieve_body( $response );
	if ( empty( $body ) ) {
		return 0;
	}
	$body = preg_replace( '#_\((.+?)\)$#', '$1', $body );
	$data = json_decode( $body, true );

	return empty( $data['count'] ) ? 0 : (int) $data['count'];
}

/**
 * fetch the total number of shares for an url from LinkedIn
 *
 * @param string $url
 *
 * @return int
 */
function tve_social_fetch_count_in_share( $url ) {
	$data = _tve_social_helper_get_json( 'http://www.linkedin.com/countserv/count/share?format=json&url=' . rawurlencode( $url ) );

	return empty( $data['count'] ) ? 0 : (int) $data['count'];
}

/**
 * fetch the total number of shares for an url from Xing
 *
 * @param string $url
 *
 * @return int
 */
function tve_social_fetch_count_xing_share( $url ) {
	$response = _tve_social_helper_get_json( 'https://www.xing-share.com/spi/shares/statistics?url=' . rawurlencode( $url ), 'wp_remote_post' );

	return isset( $response['share_counter'] ) ? $response['share_counter'] : 0;
}

/**
 * entry point for the main ajax request - count social shares
 *
 * @param array $current   no used
 * @param array $post_data post data received from the main ajax request
 *
 * @see tve_dash_frontend_ajax_load
 *
 */
function tve_social_dash_ajax_share_counts( $current, $post_data ) {
	return tve_social_ajax_count( true, $post_data );
}


/**
 * get the social count an array of networks
 *
 * POST['for'] each element is an object : a key-value pair - key=network value=url to get the counts for
 *
 * @param bool  $return    whether to return the counts or send them as json
 * @param array $post_data allow overriding the default $_POST data
 */
function tve_social_ajax_count( $return = false, $post_data = null ) {
	$response = [
		'counts' => [],
		'totals' => [],
	];
	$data     = null !== $post_data ? $post_data : $_POST;

	if ( empty( $data['for'] ) || ! is_array( $data['for'] ) ) {
		if ( $return ) {
			return $response;
		}
		wp_send_json( $response );
	}
	$url_cache   = [];
	$count_cache = [];
	foreach ( $data['for'] as $index => $item ) {

		if ( ! is_array( $item ) ) {
			continue;
		}

		$default        = tve_social_get_custom_networks();
		$networks       = array_intersect( $default, array_keys( $item ) );
		$total          = 0;
		$post_permalink = empty( $data['post_id'] ) ? '' : get_permalink( $data['post_id'] );

		$response['counts'][ $index ] = [];

		foreach ( $networks as $network ) {
			$url = $item[ $network ];
			if ( $url == '{tcb_post_url}' ) {
				$url     = $post_permalink;
				$post_id = $data['post_id'];
			} else {
				if ( ! isset( $url_cache[ $url ] ) ) {
					$url_cache[ $url ] = url_to_postid( $url );
				}
				$post_id = $url_cache[ $url ];
			}

			if ( ! empty( $post_id ) ) {
				/* get the count value from cache */
				if ( ! isset( $count_cache[ $post_id ] ) ) {
					$count_cache[ $post_id ] = tve_social_get_share_count( $post_id, $url, $networks );
				}

				$count = $count_cache[ $post_id ][ $network ];
			} else {
				$count = call_user_func( 'tve_social_fetch_count_' . $network, $url );
			}

			$total += $count;

			$response['counts'][ $index ][ $network ] = array(
				'value'     => $count,
				'formatted' => tve_social_count_format( $count ),
			);
		}
		$response['totals'][ $index ] = array(
			'value'     => $total,
			'formatted' => tve_social_count_format( $total ),
		);
	}

	if ( $return ) {
		return $response;
	}

	wp_send_json( $response );

}
