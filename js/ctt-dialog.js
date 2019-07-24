var $ = jQuery;

$(document).ready(function () {
    const pre_text = $("#twtext").val().length;
    $("#charNum").text(280 - pre_text);
    $(".tabs-menu a").click(function (event) {
        event.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        const tab = $(this).attr("href");
        $(".tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });
    $("#snd-via").on("click", function () {
        if (!$(this).attr("data-handler")) {
            $(".empty-handler").show();
        } else {
            $(".empty-handler").hide();
        }
    });


    $("#cancel-ctt-theme").on("click", function () {
        $("#tab-lbl").show();
        $("a#cnew-ctt").trigger("click");
        $(".on-browse-click").hide();
    });

    let tcount = "";
    $("ul#tab-lbl li a").on("click", function () {
        $(".designBOX").hide();
        $("ul#tab-lbl li").each(function () {
            $(this).removeClass("active");
        });
        $(this).parent().addClass("active");
        tcount = $(this).attr("dataval");
        $("#Design_" + tcount).show();
        if (tcount == 3) {
            $("#row-insert-btn").hide();
        } else {
            $("#row-insert-btn").show();
        }
    })

    let idCount = "1";

    $("ul#theme-selectup li a").on("click", function () {
        $(this).parent().addClass("active");
        idCount = $(this).attr("dataval");
        $("#tab-upbox").val(idCount);
    });

    let rec_selected = "";
    $("#recomnded-theme .tweet-box").on("click", function () {
        rec_selected = $(this).attr("data-tpl");
        idCount = "";
    });

    $("#myTheme, #lets-browse-theme").on("click", function () {
        idCount = $("ul#theme-selectup li.current a").attr("dataval");
    });

    $("#ctt-insert-button").on("click", function (e) {
        let valselected = 0;
        if (idCount != "") {
            valselected = $("#tab-" + idCount + " input[type='radio']:checked").val();
        }

        const twtext = $("#twtext").val();
        const title = $("#title").val();
        const auth_id = $("#author-thumb-id").val();
        const auth_name = $("#ctt-author-name").val();
        const ibox_id = $("#tweet-thumb-id").val();

        if (idCount == 4 && auth_id == "") {
            alert("Upload author image.");
            return false;
        }

        if (idCount == 3 && ibox_id == "") {
            alert("Upload image for tweet.");
            return false;
        }

        e.preventDefault();

        if (twtext.length !== 0) {
            if (valselected > 0 || rec_selected != "") {
                $(".ctt_dialog .ctt-loader").show();
                const data = {
                    action: 'ctt_api_post',
                    security: ajax_var.ajax_nonce,
                    data: $("#ctt_new").serialize(),
                    theme_data: idCount + "|" + valselected,
                    tweet_id: $("#tweet-thumb-id").val(),
                    author_thumb: $("#author-thumb-id").val(),
                    tweet_text: twtext,
                    title: title
                };
                $.post(ajax_var.url + "/wp-admin/admin-ajax.php", data, function (response) {
                    const ctt = jQuery.parseJSON(response);
                    const via_text = $("#snd-via").is(":checked") ? "via=\"yes\"" : "via=\"no\"";
                    let ctt_flow = "";

                    ctt.title = stripslashes(ctt.title);

                    if (idCount == 1) {
                        res = '[ctt template="' + valselected + '" link="' + ctt.coverup + '" ' + via_text + ' ' + ctt_flow + ']' + ctt.title + '[/ctt]';
                    } else if (idCount == 2) {
                        res = '[ctt_hbox link="' + ctt.coverup + '" ' + via_text + ' ' + ctt_flow + ']' + ctt.tweet + '[/ctt_hbox]';
                    } else if ((idCount == 3) && (typeof (ctt.thumb_id) != "undefined")) {
                        res = '[ctt_ibox thumb="' + ctt.thumb_id + '" template="' + valselected + '" ' + via_text + ' ' + ctt_flow + ']' + ctt.title + '[/ctt_ibox]';
                    } else if ((idCount == 4) && (typeof (ctt.author) != "undefined")) {
                        res = '[ctt_author author="' + ctt.author + '" name="' + auth_name + '" template="' + valselected + '" link="' + ctt.coverup + '" ' + via_text + ' ' + ctt_flow + ']' + ctt.title + '[/ctt_author]';
                    }

                    if (rec_selected != "") {
                        rec_type = rec_selected.split("-");
                        if (rec_type[0] == "box") {
                            res = '[ctt template="' + rec_type[1] + '" link="' + ctt.coverup + '" ' + via_text + ' ' + ctt_flow + ']' + ctt.title + '[/ctt]';
                        } else {
                            res = '[ctt_hbox link="' + ctt.coverup + '" ' + ctt_flow + ']' + ctt.title + '[/ctt_hbox]';
                        }
                    }

                    if (window.tinyMCE) {
                        window.tinyMCE.execCommand('mceInsertContent', false, res);
                        tinyMCEPopup.editor.execCommand('mceRepaint');
                        tinyMCEPopup.close();
                        $("#ctt-dialogxt").removeClass("ctt-freez");
                    }
                });
            } else {
                alert("Please select theme for the text you want to Tweet.");
            }
        } else {
            alert("Please insert text in Tweet Box.");
            $("#twtext").focus();
            $("a#cnew-ctt").trigger("click");
        }
    });
});

function ctt_count_char(val) {
    const len = val.value.length;
    if (len >= 280) {
        val.value = val.value.substring(0, 280);
        $("#charNum").text("0");
    } else {
        $("#charNum").text(280 - len);
    }
}

function arc_tpl(id) {
    const title = $("#set-hideen-tweet").attr("data-title");
    const link = $("#set-hideen-tweet").attr("data-cover");
    const tag = `[ctt link="${link}" template="${id}"]${title}[/ctt]`;
    if (window.tinyMCE) {
        window.tinyMCE.execCommand('mceInsertContent', false, tag);
        tinyMCEPopup.editor.execCommand('mceRepaint');
        tinyMCEPopup.close();
    }
}

function ctt_submit(e) {
    const coverup = e.id.replace("insert_", "");
    const tweetEl = document.getElementById("tweet_" + coverup);
    const tweet = tweetEl.innerHTML;
    $(".ask-template-option").show();
    $("#set-hideen-tweet").attr("data-tweet", tweet);
    $("#set-hideen-tweet").attr("data-cover", coverup);
}

function tw_image_uploader(obj) {
    const custom_uploader = wp.media({
        //title: "Select image for iweet",
        button: {text: "Insert Image"},
        multiple: false
    });
    custom_uploader.on("select", function () {
        const attachment = custom_uploader.state().get("selection").first().toJSON();
        $("#tweet-thumb-id").val(attachment.id);
        $("#tab-3 img.twd-image").each(function () {
            $(this).attr("src", attachment.url);
        });
    }).open();
}

function author_image_uploader(obj) {
    const custom_uploader = wp.media({
        //title: 'Select Image for Author',
        button: {text: 'Insert Author Image'},
        multiple: false
    });
    custom_uploader.on('select', function () {
        const attachment = custom_uploader.state().get('selection').first().toJSON();
        $("#author-thumb-id").val(attachment.id);
        $("#tab-4 img.auth-src").each(function () {
            $(this).attr('src', attachment.url);
        });
    }).open();
}

function stripslashes(str) {
    // + original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + improved by: Ates Goral (http://magnetiq.com)
    // + fixed by: Mick@el
    // + improved by: marrtins
    // + bugfixed by: Onno Marsman
    // + improved by: rezna
    // + input by: Rick Waldron
    // + reimplemented by: Brett Zamir (http://brett-zamir.me)
    // + input by: Brant Messenger (http://www.brantmessenger.com/)
    // + bugfixed by: Brett Zamir (http://brett-zamir.me)
    // * example 1: stripslashes('Kevin\'s code');
    // * returns 1: "Kevin's code"
    // * example 2: stripslashes('Kevin\\\'s code');
    // * returns 2: "Kevin\'s code"
    return (str + '').replace(/\\(.?)/g, function (s, n1) {
        switch (n1) {
            case '\\':
                return '\\';
            case '0':
                return '\u0000';
            case '':
                return '';
            default:
                return n1;
        }
    });
}