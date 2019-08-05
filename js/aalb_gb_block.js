(function (blocks, editor, element) {
    var RichText = editor.RichText;

    var el = element.createElement;
    var ENTER_KEY_CODE = 13;
    const AMZN_ICON_URL = 'https://images-na.ssl-images-amazon.com/images/G/01/PAAPI/AmazonAssociatesLinkBuilder/amazon_icon._V506839993_.png';
    const AALB_GB_BLOCK_TITLE = 'Amazon Associates Link Builder';


    blocks.registerBlockType('amazon-associates-link-builder/aalb-gutenberg-block', {
        title: AALB_GB_BLOCK_TITLE,
        icon: el('img',
            {
                id: 'aalb-img',
                className: 'aalb-admin-searchbox-amzlogo',
                src: AMZN_ICON_URL
            }
        ),
        category: 'widgets',

        attributes: {
            shortCodeContent: {
                type: 'string',
            },
            searchKeyword: {
                type: 'string',
            },
        },

        edit: function (props) {

            var searchKeyword = props.attributes.searchKeyword;
            var shortCodeContent = props.attributes.shortCodeContent;
            var isSearchDisabled = api_pref.IS_PAAPI_CREDENTIALS_NOT_SET || api_pref.IS_STORE_ID_CREDENTIALS_NOT_SET;

            function onChangeContent(event) {
                props.setAttributes({searchKeyword: event.target.value});
            }

            function onChangeShortCodeContent(newContent) {
                props.setAttributes({shortCodeContent: newContent});
            }

            var onSearchClick = function (event) {
                aalb_admin_object.admin_show_create_shortcode_popup_gutenberg(props);
            };

            function getSearchButtonClassName() {
                return 'button aalb-admin-button-create-amazon-shortcode' + (isSearchDisabled ? ' aalb-admin-button-create-amazon-shortcode-disabled':'');
            }

            function getSpanClassName() {
                return isSearchDisabled ? 'aalb-admin-searchbox-tooltip-text' : 'aalb-admin-editor-tooltip aalb-admin-hide-display';
            }

            function getSpanErrorMsg() {
                if (api_pref.IS_PAAPI_CREDENTIALS_NOT_SET)
                    return aalb_strings.paapi_credentials_not_set;
                else if (api_pref.IS_STORE_ID_CREDENTIALS_NOT_SET)
                    return aalb_strings.store_id_credentials_not_set;
            }


            return (el('div', {className: 'aalb-admin-inline aalb-admin-searchbox'},

                    el('span',
                        {
                            className: getSpanClassName(),
                            children: el(RichText.Content,
                                {
                                    value: getSpanErrorMsg(),
                                }
                            )
                        }
                    ),

                    /**
                     * Amazon logo.
                     */
                    el('img',
                        {
                            className: 'aalb-admin-searchbox-amzlogo',
                            src: AMZN_ICON_URL
                        }
                    ),

                    /**
                     * Search box.
                     */
                    el('input',
                        {
                            type: 'text',
                            className: 'aalb-admin-input-search',
                            name: 'aalb-admin-input-search',
                            placeholder: aalb_strings.searchbox_placeholder,
                            onKeyPress: function () {
                                if (event.keyCode === ENTER_KEY_CODE) {
                                    aalb_admin_object.gutenberg_editor_onkeypress(event, props);
                                }
                            },
                            onChange: onChangeContent,
                            value: searchKeyword,
                            disabled: isSearchDisabled,
                        }
                    ),

                    /**
                     * Search button.
                     */
                    el('a',
                        {
                            className: getSearchButtonClassName(),
                            title: aalb_strings.add_aalb_shortcode,
                            onClick: onSearchClick
                        },
                        aalb_strings.search_button_label
                    ),

                    /**
                     * Shortcode text.
                     */
                    el(RichText,
                        {
                            tagName: 'p',
                            value: shortCodeContent,
                            onChange: onChangeShortCodeContent
                        }
                    )
                )
            );
        },
        save: function (props) {
            /**
             * Server side rendering is handled, so returning null.
             */
            return null;
        },

        transforms: {
            to: [
                /**
                 * Transform to shortcode block.
                 */
                {
                    type: 'block',
                    blocks: ['core/shortcode'],
                    transform: function (attributes) {
                        return wp.blocks.createBlock('core/shortcode', {
                            text: attributes.shortCodeContent,
                        });
                    },
                },
            ],
        },
    });

})(
    window.wp.blocks,
    window.wp.editor,
    window.wp.element
);
