<?php

if (!class_exists('ctt')) {
	class ctt {
		public function __construct() {
			$this->glob_translate();
		}

		public function glob_translate() {
			load_plugin_textdomain('click-to-tweet', false, basename(__DIR__) . '/languages/');
		}

		public function debug($array) {
			echo '<pre>';
			var_dump($array);
			echo '</pre>';
		}

		public function deactivation() {
		}

		/**
		 * Handles uninstallation tasks, such as deleting plugin options.
		 */
		public function uninstall() {
			delete_option('ctt-twitter-handle');
		}

		/**
		 * Register global hooks, these are added to both the admin and front-end.
		 */
		public function register_global_hooks() {
			add_action('wp_enqueue_scripts', [$this, 'add_css']);
			add_action('admin_enqueue_scripts', [$this, 'ctt_admin_style']);
			add_filter('the_content', [$this, 'replace_tags']);
			//Add Gutenberg Block
			add_action('init', [$this, 'gutenberg_block']);
		}

		/**
		 * Register admin only hooks.
		 */
		public function register_admin_hooks() {
			// Cache bust tinymce
			add_filter('tiny_mce_version', [$this, 'refresh_mce']);

			// Add Settings Link
			add_action('admin_menu', [$this, 'admin_menu']);

			// Add settings link to plugins listing page
			add_filter('plugin_action_links', [$this, 'plugin_settings_link'], 2, 2);

			// Add button plugin to TinyMCE
			add_action('init', [$this, 'tinymce_button']);

			// AJAX dialog form
			add_action('wp_ajax_ctt_show_dialog', [$this, 'ctt_show_dialog_callback']);

			// AJAX post form data
			add_action('wp_ajax_ctt_api_post', [$this, 'ctt_api_post_callback']);
		}

		public function ctt_show_dialog_callback() {
			wp_enqueue_media();

			wp_register_style('ctt-dialog', plugins_url('/css/design-box-style.css', __DIR__));
			wp_enqueue_style('ctt-dialog');

			wp_register_script('tiny_mce_popup', includes_url('/js/tinymce/tiny_mce_popup.js'));
			wp_enqueue_script('tiny_mce_popup');

			wp_register_script('ctt-dialog', plugins_url('/js/ctt-dialog.js', __DIR__), ['jquery']);
			wp_enqueue_script('ctt-dialog');
			wp_localize_script(
				'ctt-dialog',
				'ajax_var',
				['ajax_nonce' => wp_create_nonce('ctt_nonce_string'),'url' => get_site_url()]
			);

			$token = get_option('ctt-token');
			$res = wp_remote_get('https://ctt.ec/Wp/listCTTs?token=' . $token);
			if (is_wp_error($res)) {
				$content = 'Error: ' . $res->get_error_message();
			} else {
				$content = $res['body'];
			}
			include(plugin_dir_path(__DIR__) . '/ctt-dialog.php');
			exit;
		}

		public function ctt_counter() {
			$ctt_pub_counter = get_option('ctt-pcounter');
			if ($ctt_pub_counter) {
				update_option('ctt-pcounter', $ctt_pub_counter + 1);
			} else {
				update_option('ctt-pcounter', 1);
			}
		}

		// Post data via ctt.ec API
		public function ctt_api_post_callback() {
			check_ajax_referer('ctt_nonce_string', 'security');
			$ctt_pub_counter = get_option('ctt-pcounter');
			$token = get_option('ctt-token');
			$theme = explode('|', $_POST['theme_data']);
			$theme = ['tab' => $theme[0], 'box' => $theme[1]];
			$chk_tab = $theme['tab'];

			if ($chk_tab == 3 && isset($_POST['tweet_id'])) {
				$url = wp_get_attachment_image_src($_POST['tweet_id'], 'large');
				$thumb = $url[0];
				$thumb_meta = get_post_meta($_POST['tweet_id'], '_ctt_twitter_url', true);
				if ($thumb_meta) {
					$send_tweet = stripslashes($_POST['tweet_text']) . ' ' . $thumb_meta;
					parse_str($_POST['data'], $return_arg);
					$post_array = ['thumb_id' => $_POST['tweet_id'], 'tweet' => $send_tweet, 'tab' => $return_arg['tab-upbox'], 'tab_box' => $return_arg['designBOX3']];
					$post_array_json = json_encode($post_array);
					print_r($post_array_json);
				} else {
					$image_path = '';
					if (function_exists('get_home_path')) {
						$image_path = str_replace(home_url('/'), get_home_path() . '/', $thumb);
					}
					$tweet_src = $this->send_image_to_twitter($image_path, $thumb);
					update_post_meta($_POST['tweet_id'], '_ctt_twitter_url', $tweet_src);
					parse_str($_POST['data'], $return_arg);
					$tw_with_img = $return_arg['tweet'] . ' ' . $tweet_src;
					$post_array = [
						'thumb_id' => $_POST['tweet_id'],
						'tweet'    => stripslashes($tw_with_img),
						'tab'      => $return_arg['tab-upbox'],
						'tab_box'  => $return_arg['designBOX3'],
					];
					$post_array_json = json_encode($post_array);
					print_r($post_array_json);
				}
				die;
			}

			if (isset($theme) && $theme['tab'] != '' && $theme['box'] != 0) {
				$check_theme = get_option('ctt-used-theme');
				$var = '';
				if ($theme['tab'] == 1) {
					$in_ary = 'box-' . $theme['box'];
					$var = ['box-' . $theme['box']];
				} elseif ($theme['tab'] == 2) {
					$in_ary = 'hint-box';
					$var = ['hint-box'];
				}
				if ($check_theme && $var) {
					if ((count($check_theme) < 6) && (!in_array($in_ary, $check_theme))) {
						$mrg_ary = array_merge($check_theme, $var);
						update_option('ctt-used-theme', $mrg_ary);
					}
				} elseif (!empty($var)) {
					update_option('ctt-used-theme', $var);
				}
			}

			$td = [];
			parse_str($_POST['data'], $td);
			$post_tweet = '';
			if (!empty($td['send-via'])) {
				$setting = get_option('ctt_settings');
				$handler = $setting['ctt-handler'] ?? '';
				$post_tweet = $td['tweet'] . ' ' . $handler;
			} else {
				$post_tweet = $td['tweet'];
			}
			if (!empty($td['include_ref_link']) && !empty($td['ref_url_post_id'])) {
				$post_tweet = $post_tweet . ' ' . get_permalink(intval($td['ref_url_post_id']));
				$post_tweet = stripslashes($post_tweet);
			}

			// Encode hashtags.
			$post_tweet = str_replace('#', '%23', $post_tweet);

			$rec_theme = isset($td['rec-theme']) ? "&rec-theme={$td['rec-theme']}" : '';
			$gmdata = 'links=' . $td['links'] . '&token=' . $token . '&tweet=' . $post_tweet . '&tab-upbox=' . $td['tab-upbox'] . $rec_theme;
			$gourl = 'https://ctt.ec/Wp/createSubmit?' . $gmdata;
			$res = wp_remote_get($gourl);

			if (is_wp_error($res)) {
				$temp = 'Error: ' . $res->get_error_message();
			} else {
				$temp = json_decode($res['body'], true);
			}

			if (isset($td['author-thumb-id'])) {
				$temp['author'] = $td['author-thumb-id'];
			} else {
				$temp['author'] = 9999;
			}

			$temp['title'] = $_POST['title'];
			$send_data = json_encode($temp);
			print_r($send_data);
			$this->ctt_counter();
			exit;
		}

		public function tinymce_button() {
			if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
				return;
			}

			if (get_user_option('rich_editing') == 'true') {
				add_filter('mce_external_plugins', [$this, 'tinymce_register_plugin']);
				add_filter('mce_buttons', [$this, 'tinymce_register_button']);
			}
		}

		public function tinymce_register_button($buttons) {
			array_push($buttons, '|', 'ctt');
			return $buttons;
		}

		public function tinymce_register_plugin($plugin_array) {
			$plugin_array['ctt'] = plugins_url('js/ctt.js', __DIR__);
			return $plugin_array;
		}

		/**
		 * Admin: Add settings link to plugin management page
		 */
		public function plugin_settings_link($actions, $file) {
			if (false !== strpos($file, 'ctt')) {
				$actions['settings'] = '<a href="options-general.php?page=ctt">' . __('Settings', 'click-to-tweet') . '</a>';
			}
			return $actions;
		}

		/**
		 * Admin: Add Link to sidebar admin menu
		 */
		public function admin_menu() {
			add_action('admin_init', [$this, 'register_settings']);
			add_options_page('Click To Tweet Options', 'Click To Tweet', 'manage_options', 'ctt', [$this,'settings_page']);
		}

		public function get_ibox_data($ary, $index, $data) {
			return $ary['template_' . $index][$data];
		}
		// Admin Options
		public function settings_page() {
			require_once plugin_dir_path(__DIR__) . 'ctt-admin-options.php';
		}

		/**
		 * Admin: Whitelist the settings used on the settings page
		 */
		public function register_settings() {
			register_setting('ctt-options', 'ctt-twitter-handle', [$this, 'validate_settings']);
			register_setting('ctt-options', 'ctt-token', [$this, 'validate_settings']);
		}

		/**
		 * Admin: Validate settings
		 */
		public function validate_settings($input) {
			return str_replace('@', '', strip_tags(stripslashes($input)));
		}

		/**
		 * Add CSS needed for styling the plugin
		 */
		public function add_css() {
			wp_register_style('ctt', plugins_url('/css/ctt-module-design.css', __DIR__)); // for fronend design we use style.css in css folder
			wp_enqueue_style('ctt');

			wp_register_script('ctt_plug_script', plugins_url('/js/ctt-script.js', __DIR__), ['jquery'], '1.0.0', true);
			wp_enqueue_script('ctt_plug_script');
		}

		public function ctt_admin_style() {
			if (is_admin()) {
				wp_register_style('ctt_admin_style', plugins_url('/css/ctt-admin-style.css', __DIR__));
				wp_enqueue_style('ctt_admin_style');

				wp_enqueue_script('custom-script-handle', plugins_url('js/ctt-script.js', __DIR__), ['jquery'], false, true);
			}
		}

		// Shorten text length to 100 characters.
		public function shorten($input, $length, $ellipses = true, $strip_html = true) {
			if ($strip_html) {
				$input = strip_tags($input);
			}
			if (strlen($input) <= $length) {
				return $input;
			}
			$last_space = strrpos(substr($input, 0, $length), ' ');
			$trimmed_text = substr($input, 0, $last_space);
			if ($ellipses) {
				$trimmed_text .= '...';
			}

			return $trimmed_text;
		}

		/**
		 * Replacement of Tweet tags with the correct HTML
		 */
		public function tweet($matches) {
			$handle = get_option('ctt-twitter-handle');
			if (!empty($handle)) {
				$handle_code = '&via=' . $handle;
			} else {
				$handle_code = '';
			}
			$text = $matches[1];
			$short = $this->shorten($text, 100);

			return '<div style=\'clear:both\'></div><div class=\'click-to-tweet\'><div class=\'ctt-text\'><a href=\'https://twitter.com/share?text=' . urlencode($short) . $handle_code . '&url=' . get_permalink() . '\' target=\'_blank\'>' . $short . '</a></div><a href=\'https://twitter.com/share?text=' . urlencode($short) . '' . $handle_code . '&url=' . get_permalink() . '\' target=\'_blank\' class=\'ctt-btn\'>Click To Tweet</a><div class=\'ctt-tip\'></div></div>';
		}

		// Replacement of Tweet tags with the correct HTML for a rss feed
		public function tweet_feed($matches) {
			$handle = get_option('ctt-twitter-handle');
			if (!empty($handle)) {
				$handle_code = '&via=' . $handle;
			} else {
				$handle_code = '';
			}
			$text = $matches[1];
			$short = $this->shorten($text, 100);
			return '<hr><p><em>' . $short . '</em><br><a href=\'https://twitter.com/share?text=' . urlencode($short) . $handle_code . '&url=' . get_permalink() . '\' target=\'_blank\'>Click To Tweet</a></p><hr>';
		}

		/**
		 * Regular expression to locate tweet tags
		 */
		public function replace_tags($content) {
			if (is_feed()) {
				return preg_replace_callback('/\\[tweet "(.*?)"]/i', [$this, 'tweet_feed'], $content);
			}
			return preg_replace_callback('/\\[tweet "(.*?)"]/i', [$this, 'tweet'], $content);
		}

		/**
		 * Cache bust tinymce
		 */
		public function refresh_mce($ver) {
			return $ver + 3;
		}

		public function gutenberg_block() {
			if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
				return;
			}
			if (function_exists('register_block_type')) {
				if (is_admin()) {
					wp_register_script('block_handler_script', plugins_url('/js/ctt-block.min.js', __DIR__), ['jquery','wp-element', 'wp-blocks','wp-components','wp-compose','wp-i18n','wp-editor']);
					wp_localize_script('block_handler_script', 'ajax_var', [
						'sampleImage'       => plugins_url('/images/sample-image.jpg', __DIR__),
						'authorSampleImage' => plugins_url('/images/timface.jpeg', __DIR__),
						'hintBox'           => [
							'background' => get_option('ctt_hint_box')['background'],
							'color'      => get_option('ctt_hint_box')['color'],
						],
						'ajax_nonce' => wp_create_nonce('ctt_nonce_string'),
						'url'        => get_site_url(),
						'admin_url'  => get_admin_url(),
						'token'      => get_option('ctt-token'),
					]);
					wp_register_style('block_handler_style', plugins_url('/css/ctt-block.css', __DIR__));
				}
				register_block_type('ctt/block', [
					'editor_script' => 'block_handler_script',
					'editor_style'  => 'block_handler_style',
					'attributes'    => ['submit' => ['type' => 'boolean', 'default' => false],
						'submitProcessing'          => ['type' => 'boolean', 'default' => false],
						'generated'                 => ['type' => 'boolean', 'default' => false],
						'tweetText'                 => ['type' => 'string'],
						'displayText'               => ['type' => 'string'],
						'userName'                  => ['type' => 'boolean'],
						'postLink'                  => ['type' => 'boolean'],
						'imageBoxMediaID'           => ['type' => 'string'],
						'imageBoxMediaUrl'          => ['type' => 'string', 'default' => ''],
						'authorMediaID'             => ['type' => 'string'],
						'authorMediaUrl'            => ['type' => 'string', 'default' => ''],
						'authorName'                => ['type' => 'string'],
						'boxType'                   => ['type' => 'string', 'default' => '1'],
						'boxTypeSelect_1'           => ['type' => 'string', 'default' => '1'],
						'boxTypeSelect_2'           => ['type' => 'string', 'default' => '1'],
						'boxTypeSelect_3'           => ['type' => 'string', 'default' => '1'],
						'boxTypeSelect_4'           => ['type' => 'string', 'default' => '1'],
						'tweetCoverup'              => ['type' => 'string'],
						'tweetWithImage'            => ['type' => 'string'],
					],
					'render_callback' => [$this, 'renderBlock'],
				]);
			}
		}

		public function renderBlock($attributes) {
			$outputShortcode = '';
			if (isset($attributes['tweetCoverup']) || isset($attributes['tweetWithImage'])) {
				$boxType = $attributes['boxType'] ?? 1;
				$valselected = $attributes['boxTypeSelect_' . $boxType] ?? 1;
				$via_text = 'via="no"';
				$ctt_flow = '';
				if ($attributes['userName'] ?? false) {
					$via_text = 'via="yes"';
				}
				$cttDisplay = $attributes['displayText'] ?? '' ;
				switch (intval($boxType)) {
					case 1:
						$outputShortcode = '[ctt template="' . $valselected . '" link="' . $attributes['tweetCoverup'] . '" ' . $via_text . ' ' . $ctt_flow . ']' . $cttDisplay . '[/ctt]';
						break;
					case 2:
						$outputShortcode = '[ctt_hbox link="' . $attributes['tweetCoverup'] . '" ' . $via_text . ' ' . $ctt_flow . ']' . $cttDisplay . '[/ctt_hbox]';
						break;
					case 3:
						$outputShortcode = '[ctt_ibox thumb="' . $attributes['imageBoxMediaID'] . '" template="' . $valselected . '" ' . $via_text . ' ' . $ctt_flow . ']' . $cttDisplay . '[/ctt_ibox]';
						break;
					case 4:
						$outputShortcode = '[ctt_author author="' . $attributes['authorMediaID'] . '" name="' . $attributes['authorName'] . '" template="' . $valselected . '" link="' . $attributes['tweetCoverup'] . '" ' . $via_text . ' ' . $ctt_flow . ']' . $cttDisplay . '[/ctt_author]';
						break;
				}
			}
			return  do_shortcode($outputShortcode);
		}

		// Upload function on Twitter APP
		private function send_image_to_twitter($image_path, $image_url) {
			$wptoken = get_option('ctt-token');
			$image_name = basename($image_path);
			$url = 'https://ctt.ec/twitter/pluginPostImage?image=' . $image_url . '&wptoken=' . $wptoken;
			$request = wp_remote_get($url);
			$response = wp_remote_retrieve_body($request);

			if ($response != '') {
				return $response;
			}
			return $image_url;
		}
	}
}
