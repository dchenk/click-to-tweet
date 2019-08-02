<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
$check_token = get_option('ctt-token');
if (empty($check_token)) {
	echo '<p class="check-token" style="background:#ffffff; height: 100%; margin-top: 50px; padding: 10px; text-align: center;width: 100%;">You need to sign in with Twitter to connect to ClickToTweet.com.
	<a href="' . get_admin_url() . 'options-general.php?page=ctt" target="_parent">Click here</a> to sign in.';
	return;
}
$plug_url = plugins_url() . '/click-to-tweet/';
$setting = get_option('ctt_settings');
$permalink = $_REQUEST['permalink'];
$post_id = $_GET['post_id'] ?? '';

?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Click To Tweet Plugin</title>
		<?php iframe_header(); ?>
	</head>
	<body>
	<?php
	$pretxt = '';
	if (isset($_GET['pretext'])) {
		$pretxt = stripslashes($_GET['pretext']);
	}
	?>
		<div class="ctt_dialog">
			<div class="ctt-loader"></div>
			<div id="ctt-dialogxt" class="ctt_new postbox" style="display: block">
				<div class="inside">
					<form name="ctt_new" id="ctt_new" method="post">
						<ul id="tab-lbl">
							<li class="active"><a href="javascript:void(0);" dataval="1" id="cnew-ctt">Create a new CTT</a></li>
							<li><a href="javascript:void(0);" dataval="2" id="myTheme">Select A Theme</a></li>
							<li><a href="javascript:void(0);" dataval="3">Insert Existing CTT</a></li>
						</ul>
						<div id="Design_1" class="designBOX" style="display:blobk">
							<input type="hidden" name="token" value="<?php echo $token; ?>">
							<label for="twtweet" class="textarea-label" title="Enter text which you want to Tweet">Message to be tweeted</label>
							<textarea name="tweet" id="twtext" rows="2" onkeyup="ctt_count_char(this)" title="Enter the text to be tweeted"><?php echo $pretxt; ?></textarea>
							<div class="chars-left"><span id="charNum"></span>&nbsp;characters remaining</div>
							<label for="title" class="textarea-label">Message to be displayed in blog post</label>
							<textarea name="title" id="title" rows="2"></textarea>
							<div>
								<input type="checkbox" name="send-via" id="snd-via" value="1" data-handler="<?php echo $setting['ctt-handler'] ?? ''; ?>" title="Select to append Twitter Username into your tweet">
								<label for="snd-via" title="Select to append your Twitter username to your tweet">Include Twitter username
									<span class="empty-handler">(The username was not found. <a href="<?php echo admin_url(); ?>/options-general.php?page=ctt" target="_parent">Click here</a> to manage your Twitter Username)</span>
								</label>
							</div>
							<div>
								<input type="checkbox" name="include_ref_link" id="include-ref" value="1" data-handler="<?php echo $setting['ctt-handler'] ?? ''; ?>" title="Include link back to blog post">
								<label for="include-ref" title="Select to append Twitter Username into your tweet">Include a link back to the blog post</label>
								<input type="hidden" name="ref_url_post_id" value="<?php echo $post_id; ?>">
							</div>
							<!-- <div>
								<input type="checkbox" name="ctt-shorten-links" id="ctt-shorten-links" value="1" title="Select this to make links nofollow and not to count some of their links to other pages">
								<label for="ctt-shorten-links">Shorten links</label>
							</div> -->
							<p class="edit-settings">
								<a href="<?php echo admin_url(); ?>options-general.php?page=ctt" target="_parent">Switch accounts or edit settings</a>
							</p>
							<p><span class="reqfld-label">Fill all above fields.</span></p>
						<?php
						$themes = get_option('ctt-used-theme');
						if ($themes) {
							echo '<div id="recomnded-theme"><h3>Select a recently used theme</h3>';
							for ($i = 0; $i < count($themes); $i++) {
								$tpl = explode('-', $themes[$i]);
								$iclass = '';
								if ($tpl[0] == 'box') {
									$ival = $tpl[1];
									if ($ival == 1) {
										$iclass = 'first';
									} elseif ($ival == 3) {
										$iclass = 'second';
									} elseif ($ival == 2) {
										$iclass = 'third';
									} elseif ($ival == 6) {
										$iclass = 'fourth';
									} elseif ($ival == 5) {
										$iclass = 'forteenth';
									} elseif ($ival == 4) {
										$iclass = 'sixth';
									} elseif ($ival == 7) {
										$iclass = 'fifth';
									} elseif ($ival == 8) {
										$iclass = 'fifteenth';
									} elseif ($ival == 9) {
										$iclass = 'seventh';
									} elseif ($ival == 10) {
										$iclass = 'eighth';
									} elseif ($ival == 11) {
										$iclass = 'ninth';
									} elseif ($ival == 12) {
										$iclass = 'twelth';
									}
								} else {
									$iclass = 'hint-box';
								}
								if ($iclass != 'hint-box') {
									echo '<div class="tweet-box ' . $iclass . '" data-tpl="' . $themes[$i] . '">
									<label>
									<p class="td_">Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
									<span class="click-to-tweet"> <span><i></i>CLICK TO TWEET</span></span>
									<input type="radio" value="" name="rec-theme"><span class="select"></span></label></div>';
								} else {
									echo '<div class="tweet-box rhint-ctt" data-tpl="' . $themes[$i] . '"><div class="hint-box-container"><label>
									<p>Don\'t read this text. It is here just to represent <span class="click_hint"><a href="#" class="background-type color_1">
									<span class="click-text_hint">Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes
									<i></i></span><span class="tweetdis_hint_icon"></span> </a></span></p><input type="radio" value="" name="rec-theme"><span class="select"></span></label></div></div>';
								}
							}
							echo '</div>';
						} ?>
						</div>
						<div id="Design_2" class="designBOX" style="display:none;">
							<input type="hidden" name="tab-upbox" id="tab-upbox" value="1">
							<h3 class="on-browse-click">Select Theme</h3>
							<div id="tabs-container">
								<ul id="theme-selectup" class="tabs-menu">
									<li class="current"><a href="#tab-1" dataval="1">Select Box Design</a></li>
									<li><a href="#tab-3" dataval="3">Select Image Design</a></li>
									<li><a href="#tab-2" dataval="2">Select Hint Design</a></li>
									<li><a href="#tab-4" dataval="4">Select Author Box Design</a></li>
								</ul>
								<div class="tab">
									<div id="tab-1" class="tab-content">
										<div class="tweet-box first">
											<label>
												<p class="td_">Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
												<span class="click-to-tweet"> <span><i></i>CLICK TO TWEET</span> </span>
												<input type="radio" name="designBOX1" value="1" checked>
												<span class="select"> </span></label>
										</div>

										<div class="tweet-box third">
											<label>
												<p class="td_">"Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
												<span class="click-to-tweet"> <span><i></i>CLICK TO TWEET</span> </span>
												<input type="radio" name="designBOX1" value="2">
												<span class="select"> </span></label>
										</div>

										<div class="tweet-box second">
											<label>
												<p class="td_">"Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes"
													<div class="click-to-tweet"> <i></i><span class="cta-pr" data-cta="Click To Tweet 3">CLICK TO TWEET</span></div>
												</p>
												<input type="radio" name="designBOX1" value="3">
												<span class="select"> </span></label>
										</div>

										<div class="clear"></div>

										<div class="tweet-box sixth">
											<label>
												<p class="td_">Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
												<span class="click-to-tweet"> <span><i></i>CLICK TO TWEET</span> </span>
												<input type="radio" name="designBOX1" value="4">
												<span class="select"> </span></label>
										</div>

										<div class="tweet-box forteenth">
											<label>
												<p class="td_">Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
												<span class="click-to-tweet"> <span><i></i>CLICK TO TWEET</span> </span>
												<input type="radio" name="designBOX1" value="5">
												<span class="select"> </span></label>
										</div>


										<div class="tweet-box fourth">
											<label>
												<p class="td_">Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
												<span class="click-to-tweet"> <span><i></i>CLICK TO TWEET</span> </span>
												<input type="radio" name="designBOX1" value="6">
												<span class="select"> </span></label>
										</div>

										<div class="clear"></div>

										<div class="tweet-box fifth">
											<label>
												<p class="td_">Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
												<span class="click-to-tweet"> <span><i></i>CLICK TO TWEET</span> </span>
												<input type="radio" name="designBOX1" value="7">
												<span class="select"> </span></label>
										</div>


										<div class="tweet-box fifteenth">
											<label>
												<p class="td_">Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
												<span class="click-to-tweet"> <span><i></i>CLICK TO TWEET</span> </span>
												<input type="radio" name="designBOX1" value="8">
												<span class="select"> </span></label>
										</div>


										<div class="tweet-box seventh">
											<label>
												<p class="td_">Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
												<span class="click-to-tweet"> <span><i></i>CLICK TO TWEET</span> </span>
												<input type="radio" name="designBOX1" value="9">
												<span class="select"> </span></label>
										</div>

										<div class="clear"></div>

										<div class="tweet-box eighth">
											<label>
												<p class="td_">Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
												<span class="click-to-tweet"> <span><i></i>CLICK TO TWEET</span> </span>
												<input type="radio" name="designBOX1" value="10">
												<span class="select"> </span>
											</label>
										</div>

										<div class="tweet-box ninth">
											<label>
												<p class="td_">Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
												<span class="click-to-tweet"> <span><i></i>CLICK TO TWEET</span> </span>
												<input type="radio" name="designBOX1" value="11">
												<span class="select"> </span>
											</label>
										</div>

										<div class="tweet-box twelth">
											<label>
												<p class="td_">Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
												<span class="click-to-tweet"> <span><i></i>CLICK TO TWEET</span> </span>
												<input type="radio" name="designBOX1" value="12">
												<span class="select"></span>
											</label>
										</div>
										<div class="clear"></div>
									</div>
									<div id="tab-2" class="tab-content">
										<?php $hb_opt = get_option('ctt_hint_box'); ?>
										<div class="box-design hint-box">
											<div class="hint-box-container">
											<label>
												<p>Don't read this text. It is here just to represent
												<span class="click_hint inpop-up"><a href="#" class="<?php echo $hb_opt['background'] . '-type color_' . $hb_opt['color']; ?>">
												<span class="click-text_hint">an example of any article on your blog. So this is kinda the paragraph of usual text in your article and what you see below is the "tweet box" created by CTT plugin. <i> </i> </span><span class="tweetdis_hint_icon"></span> </a></span>
												</p><input type="radio" name="designBOX2" value="1"><span class="select"></span>
											</label>
											</div>
										</div>
									</div>
									<div id="tab-3" class="tab-content">
										<input type=hidden id="tweet-thumb-id" name="tweet-thumb-id" value="">
                                        <a class="browse-theme" href="javascript:void(0)" onclick="return tw_image_uploader(this);">Upload Image</a>
										<div id="testerup"></div>
										<div class="tweet-box image-box first-image">
											<label> <img src="<?php echo plugin_dir_url(__FILE__) . 'images/sample-image.jpg'; ?>" alt="" class="twd-image">
												<input type="radio" name="designBOX3" value="1">
												<span class="select"> </span> </label>
											<span class="click-to-tweet"> <a href="#" class="click_image_link"> <i></i><span class="click_action">Tweet</span></a> </span>
										</div>
										<div class="tweet-box image-box second-image">
											<label> <img src="<?php echo plugin_dir_url(__FILE__) . 'images/sample-image.jpg'; ?>" alt="" class="twd-image">
												<input type="radio" name="designBOX3" value="2">
												<span class="select"> </span> </label>
											<span class="click-to-tweet"> <a href="#" class="click_image_link"> <i></i><span class="click_action">Tweet</span></a> </span>
										</div>

										<div class="tweet-box image-box third-image">
											<label> <img src="<?php echo plugin_dir_url(__FILE__) . 'images/sample-image.jpg'; ?>" alt="" class="twd-image">
												<input type="radio" name="designBOX3" value="3">
												<span class="select"> </span> </label>
											<span class="click-to-tweet"> <a href="#" class="click_image_link"> <i></i><span class="click_action">Tweet</span></a> <span class="ctt_action">Tweet</span> </span>
										</div>

										<div class="clear"></div>
										<div class="tweet-box image-box fourth-image">
											<label> <img src="<?php echo plugin_dir_url(__FILE__) . 'images/sample-image.jpg'; ?>" alt="" class="twd-image">
												<input type="radio" name="designBOX3" value="4">
												<span class="select"> </span> </label>
											<span class="click-to-tweet"> <a href="#" class="click_image_link"> <i></i><span class="click_action">Tweet</span></a> <span class="ctt_action">Tweet</span> </span>
										</div>

										<div class="tweet-box image-box fifth-image">
											<label> <img src="<?php echo plugin_dir_url(__FILE__) . 'images/sample-image.jpg'; ?>" alt="" class="twd-image">
												<input type="radio" name="designBOX3" value="5">
												<span class="select"> </span> </label>
											<span class="click-to-tweet"> <a href="#" class="click_image_link btn_original"> <i></i><span class="click_action">Click To Tweet</span></a> </span>
										</div>

										<div class="tweet-box image-box sixth-image">
											<label> <img src="<?php echo plugin_dir_url(__FILE__) . 'images/sample-image.jpg'; ?>" alt="" class="twd-image">
												<input type="radio" name="designBOX3" value="6">
												<span class="select"> </span> </label>
											<span class="click-to-tweet"> <a href="#" class="click_image_link btn_original"> <i></i><span class="click_action">Click To Tweet</span></a> </span>
										</div>
									</div>
									<div id="tab-4" class="tab-content">
									<input type=hidden id="author-thumb-id" name="author-thumb-id" value="">
									<div class="row">
									<label>Author Name</label>
									<input type="text" name="ctt-author-name" id="ctt-author-name" value="" placeholder="Enter Author Name">
									</div>
									<a class="browse-theme" href="javascript:void(0)" onclick="return author_image_uploader(this);">Upload Author Image</a>
									<br>
									<div class="auth-box-one">
										<div id="col-pe-13" class="col-preview">
											<label>
												<input type="radio" name="author-box" value="1">
												<div class="author-first-inner">
													<div class="thumb"><img src="<?php echo $plug_url; ?>/images/timface.jpeg" alt="" class="auth-src"></div>
													<div class="tweet-text">
														<p>Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
														<div class="lower-btn"><label>Nick</label><a href="#">CLICK TO TWEET</a></div>
														<span class="select"> </span>
													</div>
													<div class="clearfix"></div>
												</div>
											</label>
										</div>

										<div style="display: block;" id="col-pe-14" class="col-preview">
											<label>
												<input type="radio" name="author-box" value="2">
												<div class="author-second-inner">
													<div class="thumb"><img src="<?php echo $plug_url; ?>/images/timface.jpeg" alt="" class="auth-src"></div>
													<div class="tweet-text">
														<p>Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p>
														<div class="lower-btn">
															<label class="auth-lbl">Nick</label>
															<a href="#"><span class="cta-pr">CLICK TO TWEET</span></a>
														</div>
													</div>
													<div class="clearfix"></div>
												</div>
											</label>
										</div>

										<div style="display: block;" id="col-pe-15" class="col-preview">
											<label>
												<input type="radio" name="author-box" value="3">
												<div class="author-third-inner">
													<div class="thumb"><img src="<?php echo $plug_url; ?>/images/timface.jpeg" alt="" class="auth-src"></div>
													<div class="tweet-text">
														<blockquote class="style1"><p>Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes</p></blockquote>
														<div class="lower-btn">
															<label class="auth-lbl">Nick</label>
														</div>
													</div>
													<div class="clearfix"></div>
												</div>
											</label>
										</div>
									</div>
									</div>
								</div>
							</div>

						</div><!--#Design_3-->

						<div id="Design_3" class="designBOX" style="display:none;">
							<div class="ctt_insert postbox">
								<div class="ask-template-option">
									<div class="col-templs">
										<input id="set-hideen-tweet" type="hidden" data-tweet="" data-cover="" data-title="">
										<h3>Select Box Template</h3>
										<?php
										for ($i = 1; $i <= 12; $i++) {
											echo '<a href="javascript:void(0);" data-tpl="' . $i . '" onclick="return arc_tpl(' . $i . ');">Box Template ' . $i . '</a>';
										}
										?>
									</div>
								</div>
								<h3>Existing CTT</h3>
								<div class="inside list"><?php echo $content; ?></div>
							</div>
						</div>
						<input id="ctt-insert-button" type="submit" value="Insert New CTT" name="submit" class="button button-primary button-large">
						<a href="javascript:void(0);" id="cancel-ctt-theme" class="button on-browse-click">Cancel Theme</a>
					</form>
				</div>
			</div>
		</div>
	<?php iframe_footer(); ?>
	</body>
</html>
