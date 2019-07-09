(function() {
	const showSelection = () => tinyMCE.activeEditor.selection.getContent({ format: "text" });
	tinymce.create("tinymce.plugins.ctt", {
		init: function(ed, url) {
			const s = new URLSearchParams(window.location.search);
			ed.addButton("ctt", {
				title: "Click To Tweet",
				image: url.replace("/js", "") + "/images/editor-icon.png",
				onclick: function() {
					const seltxt = showSelection();
					const pre_txt = encodeURIComponent(seltxt).replace("%A0", "%20");
					const postID = s.get("post");
					jQuery(document).ready(function($) {
						tinymce.activeEditor.windowManager.open({
							title: "Click To Tweet Plugin",
							width: 1030,
							height: 600,
							classes: "myAwesomeClass-panel",
							url: `${ajaxurl}?action=ctt_show_dialog&pretext=${pre_txt}&post_id=${postID}`,
						});
					});
				}
			});
		},
		createControl: (n, cm) => null,
		getInfo: () => ({
			longname: "Click To Tweet",
			author: "ClickToTweet.com",
			authorurl: "https://clicktotweet.com",
			infourl: "https://github.com/dchenk/click-to-tweet",
			version: "3.0.0"
		}),
	});
	tinymce.PluginManager.add("ctt", tinymce.plugins.ctt);
})();
