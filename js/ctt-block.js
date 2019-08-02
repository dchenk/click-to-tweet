class cttBlock {
    constructor() {
        this.init();
    }

    init() {
        let {registerBlockType,} = wp.blocks;
        let {__} = wp.i18n;
        let {TextareaControl, ToggleControl, Icon, RadioControl, TextControl, Button, PanelBody, PanelRow, Spinner, ServerSideRender} = wp.components;
        let {InspectorControls, MediaUpload, MediaUploadCheck} = wp.editor;
        let {getCurrentPostId} = wp.data.select("core/editor");
        registerBlockType('ctt/block', {
            title: __('Click To Tweet'),
            icon: 'twitter',
            category: 'embed',
            attributes: {
                submit: {
                    type: 'boolean',
                    default: false
                },
                submitProcessing: {
                    type: 'boolean',
                    default: false
                },
                generated: {
                    type: 'boolean',
                    default: false
                },
                tweetText: {
                    type: 'string'
                },
                displayText: {
                    type: 'string'
                },
                userName: {
                    type: 'boolean'
                },
                postLink: {
                    type: 'boolean'
                },
                imageBoxMediaID: {
                    type: 'string'
                },
                imageBoxMediaUrl: {
                    type: 'string',
                    default: ''
                },
                authorMediaID: {
                    type: 'string'
                },
                authorMediaUrl: {
                    type: 'string',
                    default: ''
                },
                authorName: {
                    type: 'string'
                },
                boxType: {
                    type: 'string',
                    default: '1'
                },
                boxTypeSelect_1: {
                    type: 'string',
                    default: '1'
                },
                boxTypeSelect_2: {
                    type: 'string',
                    default: '1'
                },
                boxTypeSelect_3: {
                    type: 'string',
                    default: '1'
                },
                boxTypeSelect_4: {
                    type: 'string',
                    default: '1'
                },
                tweetCoverup: {
                    type: 'string',
                },
                tweetWithImage: {
                    type: 'string',
                }
            },
            save: (props) => {
                return null
            },
            edit: (props) => {
                let displayText = 'Sample Dummy Text for ClickToTweet plugin - A Wordpress plugin for creating Customize tweetable quotes';
                if (props.attributes.displayText !== "" && typeof props.attributes.displayText !== 'undefined') {
                    displayText = props.attributes.displayText;
                }
                let boxImageError = (props.attributes.boxType == 3 && (typeof props.attributes.imageBoxMediaID == 'undefined' || props.attributes.imageBoxMediaID == ""));
                let authorImageError = (props.attributes.boxType == 4 && (typeof props.attributes.authorMediaID == 'undefined' || props.attributes.authorMediaID == ""));

                function updateContent(attrName, state) {
                    let buildObj = {};
                    buildObj[attrName] = state;
                    props.setAttributes(buildObj)
                }

                function getBoxDesigns() {
                    let buildBoxes = [];
                    let boxes = ['first', 'third', 'second', 'sixth', 'forteenth', 'fourth', 'fifth', 'fifteenth', 'seventh', 'eighth', 'ninth', 'twelth'];
                    boxes.map((element, index) => {
                        index += 1;
                        buildBoxes.push(
                            <PanelRow className="box_container">
                                <div className={"tweet-box " + element}>
                                    <label>
                                        <p className="td_">{displayText}</p>
                                        <span className="click-to-tweet"> <span><i></i>CLICK TO TWEET</span> </span>
                                        <input type="radio" name="designBOX1" value={index}
                                               checked={props.attributes.boxTypeSelect_1 == index}
                                               onChange={updateContent.bind(this, 'boxTypeSelect_1', String(index))}/>
                                        <span className="select"> </span>
                                    </label>
                                </div>
                            </PanelRow>
                        );
                    });
                    return (
                        <PanelBody
                            title={__('Select a box design')}
                            initialOpen={true}
                        >
                            {buildBoxes}
                        </PanelBody>
                    );
                }

                function getImageDesigns() {
                    let buildBoxes = [];
                    let boxes = ['first', 'second', 'third', 'fourth', 'fifth', 'sixth'];
                    let imgUrl = ajax_var.sampleImage;
                    if (props.attributes.imageBoxMediaUrl !== "" && typeof props.attributes.imageBoxMediaUrl !== 'undefined') {
                        imgUrl = props.attributes.imageBoxMediaUrl;
                    }
                    boxes.map((element, index) => {
                        index += 1;
                        buildBoxes.push(
                            <PanelRow className="box_container">
                                <div className={"tweet-box image-box " + element + "-image"}>
                                    <label>
                                        <img src={imgUrl} className="twd-image"/>
                                        <input type="radio" name="designBOX3" value={index}
                                               checked={props.attributes.boxTypeSelect_3 == index}
                                               onChange={updateContent.bind(this, 'boxTypeSelect_3', String(index))}/>
                                        <span className="select"> </span>
                                    </label>
                                    <span className="click-to-tweet"><a href="#"
                                                                        className="click_image_link"> <i></i><span
                                        className="click_action">Tweet</span></a> </span>
                                </div>
                            </PanelRow>
                        );
                    });
                    return (
                        <PanelBody
                            title={__('Select a image design')}
                            initialOpen={true}
                        >
                            {buildBoxes}
                        </PanelBody>
                    );
                }

                function getAuthorBoxDesigns() {
                    let buildBoxes = [];
                    let boxes = ['first', 'second', 'third',];
                    let imgUrl = ajax_var.authorSampleImage;
                    if (props.attributes.authorMediaUrl !== "" && typeof props.attributes.authorMediaUrl !== 'undefined') {
                        imgUrl = props.attributes.authorMediaUrl;
                    }
                    let authorName = 'Nick';
                    if (props.attributes.authorName !== "" && typeof props.attributes.authorName !== 'undefined') {
                        authorName = props.attributes.authorName
                    }
                    boxes.map((element, index) => {
                        index += 1;
                        buildBoxes.push(
                            <PanelRow className="box_container">
                                <div className="auth-box-one">
                                    <div className="col-preview">
                                        <label>
                                            <input type="radio" name="author-box" value={index}
                                                   checked={props.attributes.boxTypeSelect_4 == index}
                                                   onChange={updateContent.bind(this, 'boxTypeSelect_4', String(index))}/>
                                            <div className={'author-' + element + '-inner'}>
                                                <div className="thumb">
                                                    <img src={imgUrl} className="auth-src"/>
                                                </div>
                                                <div className="tweet-text">
                                                    <p>{displayText}</p>
                                                    <div className="lower-btn">
                                                        <label>{authorName}</label>
                                                        <a href="#">CLICK TO TWEET</a>
                                                    </div>
                                                    <span className="select"> </span>
                                                </div>
                                                <div className="clearfix"></div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </PanelRow>
                        );
                    });
                    return (
                        <PanelBody
                            title={__('Select a author box design')}
                            initialOpen={true}
                        >
                            {buildBoxes}
                        </PanelBody>
                    );
                }

                function getHintDesigns() {
                    let buildBoxes = [];
                    let boxes = ['first'];
                    boxes.map((element, index) => {
                        index += 1;
                        buildBoxes.push(
                            <PanelRow className="box_container">
                                <div className="box-design hint-box">
                                    <div className="hint-box-container">
                                        <label>
                                            <p>Don't read this text. It is here just to represent
                                                <span className="click_hint inpop-up">
                                                    <a href="#"
                                                       className={ajax_var.hintBox.background + '-type color_' + ajax_var.hintBox.color}>
                                                        <span className="click-text_hint">{displayText}<i> </i></span>
                                                        <span className="tweetdis_hint_icon"> </span>
                                                    </a>
                                                </span>
                                            </p>
                                            <input type="radio" name="designBOX2" value={index}
                                                   checked={props.attributes.boxTypeSelect_2 == index}
                                                   onChange={updateContent.bind(this, 'boxTypeSelect_2', String(index))}/>
                                            <span className="select"> </span>
                                        </label>
                                    </div>
                                </div>

                            </PanelRow>
                        );
                    });
                    return (
                        <PanelBody
                            title={__('Select a hint design')}
                            initialOpen={true}
                        >
                            {buildBoxes}
                        </PanelBody>
                    );
                }

                function getRemaining() {
                    let maxChar = 280;
                    let remaining = maxChar;
                    let len = 0;
                    if (props.attributes.tweetText !== "" && typeof props.attributes.tweetText !== 'undefined') {
                        len = props.attributes.tweetText.length
                    }
                    if (len >= maxChar) {
                        props.setAttributes({tweetText: props.attributes.tweetText.substring(0, remaining)});
                        remaining = 0;
                    } else {
                        remaining -= len
                    }
                    return remaining;
                }

                function handleSubmit(event) {
                    props.setAttributes({submit: true});
                    if (!boxImageError && !authorImageError) {
                        let valselected = props.attributes['boxTypeSelect_' + props.attributes.boxType];
                        let data = {
                            action: 'ctt_api_post',
                            security: ajax_var.ajax_nonce,
                            data: $.param({
                                token: ajax_var.token,
                                tweet: props.attributes.tweetText,
                                title: props.attributes.displayText,
                                ref_url_post_id: getCurrentPostId(),
                                'tab-upbox': props.attributes.boxType,
                                'tweet-thumb-id': props.attributes.imageBoxMediaID || '',
                                'author-thumb-id': props.attributes.authorMediaID || '',
                                'ctt-author-name': props.attributes.authorName || '',
                                theme_data: props.attributes.boxType + "|" + valselected,
                                tweet_id: props.attributes.imageBoxMediaID || '',
                                author_thumb: props.attributes.authorMediaID || '',
                                tweet_text: props.attributes.tweetText,
                                designBOX1: props.attributes.boxTypeSelect_1 || '',
                                designBOX2: props.attributes.boxTypeSelect_2 || '',
                                designBOX3: props.attributes.boxTypeSelect_3 || '',
                                'author-box': props.attributes.boxTypeSelect_4 || '',
                                'send-via': props.attributes.userName || '',
                                'include_ref_link': props.attributes.postLink || '',
                            }),
                            theme_data: props.attributes.boxType + "|" + valselected,
                            tweet_id: props.attributes.imageBoxMediaID || '',
                            author_thumb: props.attributes.authorMediaID || '',
                            tweet_text: props.attributes.tweetText,
                            title: props.attributes.displayText
                        };
                        props.setAttributes({submitProcessing: true});
                        jQuery.post(ajax_var.url + "/wp-admin/admin-ajax.php", data, function (response) {
                            props.setAttributes({submitProcessing: false});
                            const ctt = jQuery.parseJSON(response);
                            props.setAttributes({generated: true});
                            if (typeof ctt.coverup !== 'undefined') {
                                props.setAttributes({tweetCoverup: ctt.coverup});
                            } else if (typeof ctt.tweet !== 'undefined') {
                                props.setAttributes({tweetWithImage: ctt.tweet});
                            }
                        });

                    }
                    event.preventDefault();
                    return false;
                }

                return (
                    <div>
                        {(ajax_var.token != "" && (<div>
                            <div style={{'textAlign': 'center'}}><Icon icon="twitter"/> {__('Click To Tweet')}</div>
                            {(props.isSelected && (
                                <div>
                                    <form onSubmit={handleSubmit.bind(this)}>
                                        <TextareaControl
                                            label={__('Message to be tweeted')}
                                            help={getRemaining() + __(' characters remaining')}
                                            value={props.attributes.tweetText}
                                            onChange={updateContent.bind(this, 'tweetText')}
                                            onKeyUp={getRemaining}
                                            className="msgToTweet"
                                            required={true}
                                            disabled={props.attributes.generated}
                                        />
                                        <TextareaControl
                                            label={__('Message to be displayed in blog post')}
                                            value={props.attributes.displayText}
                                            onChange={updateContent.bind(this, 'displayText')}
                                            required={true}
                                        />
                                        {!props.attributes.generated && (<ToggleControl
                                            label={__('Include Twitter username')}
                                            checked={props.attributes.userName}
                                            onChange={updateContent.bind(this, 'userName')}
                                        />)}
                                        {!props.attributes.generated && (<ToggleControl
                                            label={__('Include a link back to the blog post')}
                                            checked={props.attributes.postLink}
                                            onChange={updateContent.bind(this, 'postLink')}
                                            disabled={props.attributes.generated}
                                        />)}
                                        {props.attributes.boxType == 3 && (
                                            <MediaUploadCheck>
                                                <MediaUpload
                                                    onSelect={(media) => {
                                                        props.setAttributes({
                                                            imageBoxMediaID: String(media.id),
                                                            imageBoxMediaUrl: media.url
                                                        })
                                                    }}
                                                    allowedTypes={['image']}
                                                    value={props.attributes.imageBoxMediaID}
                                                    render={({open}) => (
                                                        <div>
                                                            {!props.attributes.generated && (
                                                                <div>
                                                                    <Button onClick={open} isDefault={true}>
                                                                        <Icon icon="upload"/> {__('Upload image')}
                                                                    </Button>
                                                                    {props.attributes.submit === true && boxImageError == true && (
                                                                        <div
                                                                            className="uploadError error">{__('Upload an image')}</div>)}
                                                                </div>)}
                                                        </div>
                                                    )}
                                                />
                                                {props.attributes.imageBoxMediaUrl != "" && (
                                                    <img src={props.attributes.imageBoxMediaUrl}
                                                         className="uploadPreview"/>)}
                                            </MediaUploadCheck>
                                        )}
                                        {props.attributes.boxType == 4 && (<TextControl
                                            label={__('Author Name')}
                                            value={props.attributes.authorName}
                                            onChange={updateContent.bind(this, 'authorName')}
                                            required={true}
                                        />)}
                                        {props.attributes.boxType == 4 && (
                                            <MediaUploadCheck>
                                                <MediaUpload
                                                    onSelect={(media) => {
                                                        props.setAttributes({
                                                            authorMediaID: String(media.id),
                                                            authorMediaUrl: media.url
                                                        })
                                                    }}
                                                    allowedTypes={['image']}
                                                    value={props.attributes.authorMediaID}
                                                    render={({open}) => (
                                                        <div>
                                                            <div>
                                                                <Button onClick={open} isDefault={true}>
                                                                    <Icon icon="upload"/> {__('Upload author image')}
                                                                </Button>
                                                                {props.attributes.submit === true && authorImageError == true && (
                                                                    <div
                                                                        className="uploadError error">{__('Upload an author image')}</div>)}
                                                            </div>
                                                        </div>
                                                    )}
                                                />
                                                {props.attributes.authorMediaUrl != "" && (
                                                    <img src={props.attributes.authorMediaUrl}
                                                         className="uploadPreview"/>)}
                                            </MediaUploadCheck>
                                        )}

                                        {!props.attributes.generated && (
                                            <div>
                                                <Button isDefault={true} type="submit">
                                                    {(props.attributes.submitProcessing && (<Spinner/>)) || (__('Add'))}
                                                </Button>
                                            </div>)}

                                    </form>
                                    <InspectorControls>
                                        {!props.attributes.generated && (<PanelBody
                                            title={__('Click To Tweet Setting')}
                                            initialOpen={true}
                                        >
                                            <PanelRow>
                                                <RadioControl
                                                    label="Design Type"
                                                    options={[
                                                        {label: 'Box Design', value: '1'},
                                                        {label: 'Hint Design', value: '2'},
                                                        {label: 'Image Box Design', value: '3'},
                                                        {label: 'Author Box Design', value: '4'},
                                                    ]}
                                                    selected={props.attributes.boxType}
                                                    onChange={updateContent.bind(this, 'boxType')}
                                                />
                                            </PanelRow>

                                        </PanelBody>)}

                                        {props.attributes.boxType == 1 && getBoxDesigns()}
                                        {props.attributes.boxType == 3 && getImageDesigns()}
                                        {props.attributes.boxType == 2 && getHintDesigns()}
                                        {props.attributes.boxType == 4 && getAuthorBoxDesigns()}
                                    </InspectorControls>
                                </div>
                            )) || (
                                <div>
                                    {props.attributes.generated && (
                                        <ServerSideRender
                                            block="ctt/block"
                                            attributes={props.attributes}
                                        />
                                    )}
                                </div>
                            )}
                        </div>)) || (
                            <p className="check-token">
                                {__('You need to sign in with Twitter to connect to ClickToTweet.com.')}
                                <a href={ajax_var.admin_url + "options-general.php?page=ctt"}
                                   target="_parent">{__('Click here')}</a> {__('to sign in.')}
                            </p>
                        )}
                    </div>
                );
            },
        });
    }
}

new cttBlock();