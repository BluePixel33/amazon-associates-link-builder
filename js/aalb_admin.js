/*
 Copyright 2016-2018 Amazon.com, Inc. or its affiliates. All Rights Reserved.

 Licensed under the GNU General Public License as published by the Free Software Foundation,
 Version 2.0 (the "License"). You may not use this file except in compliance with the License.
 A copy of the License is located in the "license" file accompanying this file.

 This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
 either express or implied. See the License for the specific language governing permissions
 and limitations under the License.
 */

//ToDo: Create the JSON in contexts in PHP instead of creating here and pass directly to Handlebars
//ToDO: Deep-dive to find out if event bubbling with single target is a better option and instead of having a common parent for event delgation with capturing

var aalb_admin_object = (function( $ ) {
    var SELECT_DROPDOWN_VALUE = "no-selection";
    var SINGLE_ASIN_TEMPLATE = {
        PriceLink  : 'true',
        ProductAd  : 'true',
        ProductLink: 'true'
    };
    var AALB_SHORTCODE_AMAZON_LINK = api_pref.AALB_SHORTCODE_AMAZON_LINK;
    var AALB_SHORTCODE_AMAZON_TEXT = api_pref.AALB_SHORTCODE_AMAZON_TEXT;
    var IS_PAAPI_CREDENTIALS_NOT_SET = api_pref.IS_PAAPI_CREDENTIALS_NOT_SET;
    var IS_STORE_ID_CREDENTIALS_NOT_SET = api_pref.IS_STORE_ID_CREDENTIALS_NOT_SET;
    var MAX_ALLOWED_ASINS_IN_SELECTION = 10;
    var ENTER_KEY_CODE = 13;
    var tb_remove = "";
    var template;
    var link_id = "";
    var marketplace_store_id_mapping = $.parseJSON( api_pref.marketplace_store_id_map );
    var default_marketplace = api_pref.default_marketplace;
    var default_store_id_list = (marketplace_store_id_mapping && marketplace_store_id_mapping[ default_marketplace ]) ? marketplace_store_id_mapping[ default_marketplace ] : [];
    var default_store_id = (default_store_id_list.length !== 0) ? default_store_id_list[ 0 ] : "";
    var tabs = "";
    //tab_counter will be appended to the new tab's id(#aalb_tab2) and will be incremented in code on every new tab addition. 1 is already assigned to defaxlt marketplace tab
    var tab_counter = 2;
    var marketplace_pop_up_json = [];
    var keyword_for_search = "";
    var gb_props;

    var meta_box_tab_context = {
        "searchbox_placeholder"               : aalb_strings.searchbox_placeholder,
        "search_button_label"                 : aalb_strings.search_button_label,
        "associate_id_label"                  : aalb_strings.associate_id_label,
        "select_associate_id_label"           : aalb_strings.select_associate_id_label,
        "marketplace_label"                   : aalb_strings.marketplace_label,
        "search_keyword_label"                : aalb_strings.search_keyword_label,
        "select_marketplace_label"            : aalb_strings.select_marketplace_label,
        "selected_products_list_label"        : aalb_strings.selected_products_list_label,
        "click_to_select_products_label"      : aalb_strings.click_to_select_products_label,
        "text_shown_during_shortcode_creation": aalb_strings.text_shown_during_shortcode_creation,
        "marketplace_help_content"            : aalb_strings.marketplace_help_content,
        "tracking_id_help_content"            : aalb_strings.tracking_id_help_content,
        "searched_products_box_placeholder"   : aalb_strings.searched_products_box_placeholder,
        "selected_products_box_placeholder"   : aalb_strings.selected_products_box_placeholder,
        "marketplace_list"                    : marketplace_store_id_mapping ? Object.keys( marketplace_store_id_mapping ) : "",
        "default_marketplace"                 : default_marketplace,
        "default_store_id_list"               : default_store_id_list,
        "default_store_id"                    : default_store_id
    };

    var search_pop_up_context = {
        "meta_box_tab_context"      : meta_box_tab_context,
        "add_shortcode_button_label": aalb_strings.add_shortcode_button_label,
        "ad_template_label"         : aalb_strings.ad_template_label,
        "templates_help_content"    : aalb_strings.templates_help_content,
        "templates_list"            : $.parseJSON( api_pref.templates_list ),
        "default_template"          : api_pref.default_template
    };

    var admin_pop_up_content_context = {
        "text_shown_during_search" : aalb_strings.text_shown_during_search,
        "check_more_on_amazon_text": aalb_strings.check_more_on_amazon_text
    };

    $( function() {
        //Load the search result template
        var hbs_admin_items_search_source = $( "#aalb-hbs-admin-items-search" ).html();
        if( hbs_admin_items_search_source != null ) {
            template = Handlebars.compile( hbs_admin_items_search_source );
        }

        //Resize thickbox on window resize
        $( window ).on( 'resize', resize_thickbox );

        //Storing the tb_remove function of Thickbox.js
        var old_tb_remove = window.tb_remove;
        //Custom tb_remove function
        tb_remove = function() {
            reset_add_short_button_and_error_warnings();
            //call actual tb_remove
            old_tb_remove();
            //Emptying the array
            marketplace_pop_up_json = [];
            tab_counter = 2;
        };

        /**
         * Bind template change using delegated events so that the binding remains when complete pop-up is removed(childs of #aalb-admin-pop-up) & added again
         *
         **/
        $( '#aalb-admin-pop-up' ).on( 'change', '#aalb_template_names_list', function() {
            var aalb_add_short_code_button = $( '#aalb-add-shortcode-button' );
            //checking for user selected template and number of products selected by user
            if( does_any_marketplace_contains_multiple_asin() && SINGLE_ASIN_TEMPLATE[ get_selected_template() ] ) {
                $( '#aalb-add-template-asin-error' ).text( aalb_strings.template_asin_error );
                aalb_add_short_code_button.prop( 'disabled', true );
            } else {
                aalb_add_short_code_button.prop( 'disabled', false );
                $( '#aalb-add-template-asin-error' ).text( '' );
            }
        } );

        //Bind focus event with dropdown of marketplaces
        $( "#aalb-admin-pop-up" ).on( 'focus', '.aalb-marketplace-names-list', function() {
            $( this ).data( 'prev-val', $( this ).val() );
        } );

        /**
         * To fill the store-ids as per markeplace in Associate Id section on changing marketplace
         **/
        $( '#aalb-admin-pop-up' ).on( 'change', '.aalb-marketplace-names-list', function() {
            var prev_marketplace = $( this ).data( 'prev-val' );
            var new_marketplace = $( this ).val();
            if( !marketplace_pop_up_json[ new_marketplace ] ) {
                var pop_up_container = $( this ).closest( '.aalb-pop-up-container' );
                var store_id_dropdown = pop_up_container.find( '.aalb-admin-popup-store-id' );
                reset_store_id_list( store_id_dropdown, marketplace_store_id_mapping[ new_marketplace ] );
                if( prev_marketplace !== null ) {
                    delete marketplace_pop_up_json[ prev_marketplace ];
                    pop_up_container.find( '.aalb-selected-item' ).remove();
                } else {
                    add_close_button( pop_up_container );
                    add_tab();
                }
                add_entry_in_marketplace_json( new_marketplace, marketplace_store_id_mapping[ new_marketplace ][ 0 ] );
                change_header_of_tab( pop_up_container, new_marketplace );
                admin_popup_search_items( pop_up_container );
            } else {
                $( this ).val( prev_marketplace || SELECT_DROPDOWN_VALUE );
            }
            $( this ).blur();
        } );

        // Close icon: removing the tab on click
        $( '#aalb-admin-pop-up' ).on( "click", "span.ui-icon-close", function() {
            var marketplace = $( this ).siblings( "a" ).text();
            var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
            $( "#" + panelId ).remove();

            delete marketplace_pop_up_json[ marketplace ];
            tabs.tabs( "refresh" );
        } );

        //Binding on change event of store-id list
        $( '#aalb-admin-pop-up' ).on( 'change', '.aalb-admin-popup-store-id', function() {
            var pop_up_container = $( this ).closest( '.aalb-pop-up-container' );
            marketplace_pop_up_json[ pop_up_container.find( '.aalb-marketplace-names-list' ).val() ].store_id = $( this ).val();
        } );

        //Binding click event with Search button in search pop-up
        $( '#aalb-admin-pop-up' ).on( 'click', '.aalb-admin-popup-search-button', function() {
            admin_popup_search_items( $( this ).closest( '.aalb-pop-up-container' ) );
        } );

        //Binding click event with ASIN removal from selcted item in search pop-up
        $( '#aalb-admin-pop-up' ).on( 'click', '.aalb-selected-item', function() {
            var aalb_selected_box = $( this ).closest( '.aalb-selected' );
            remove_asin( this );
            if( aalb_selected_box.find( '.aalb-selected-item' ).length === 0 ) {
                aalb_selected_box.find( '.aalb-admin-popup-placeholder' ).show();
            }
        } );

        //Binding click event with ASIN addition from search item in search pop-up
        $( '#aalb-admin-pop-up' ).on( "click", '.aalb-admin-item-search-items-item', function() {
            var data_asin = $( this ).attr( "data-asin" );
            var marketplace = $( this ).closest( '.aalb-pop-up-container' ).find( '.aalb-marketplace-names-list' ).val();
            if( !validate_asin_addition( data_asin, marketplace ) ) {
                return;
            }
            marketplace_pop_up_json[ marketplace ].selected_asin.push( data_asin );
            var aalb_selected_box = $( this ).closest( '.aalb-pop-up-container' ).find( '.aalb-selected' );
            aalb_selected_box.find( '.aalb-admin-popup-placeholder' ).hide();
            aalb_selected_box.append( create_selected_asin_html( data_asin, this ) );
        } );

        //Binding Enter event with Search button in editor search box
        $( '#aalb-admin-pop-up' ).on( 'keypress', '.aalb-admin-popup-input-search', function( event ) {
            if( event.keyCode === ENTER_KEY_CODE ) {
                event.preventDefault();
                admin_popup_search_items( $( this ).closest( '.aalb-pop-up-container' ) );
            }
        } );

        if( IS_PAAPI_CREDENTIALS_NOT_SET ) {
            disable_editor_search( aalb_strings.paapi_credentials_not_set );
        } else if( IS_STORE_ID_CREDENTIALS_NOT_SET ) {
            disable_editor_search( aalb_strings.store_id_credentials_not_set );
        }
    } );

    /**
     * onKeyPress event handler for editor seach box
     *
     * @param HTML_DOM_EVENT  event OnKeyPress event
     * @param HTMLElement caller_element caller of this function
     *
     * @since 1.5.3
     */
    function editor_searchbox_keypress_event_handler( event, caller_element ) {
        if( event.keyCode === ENTER_KEY_CODE ) {
            event.preventDefault();
            admin_show_create_shortcode_popup( $( caller_element ).siblings( '.aalb-admin-button-create-amazon-shortcode' ) );
        }
    }

    /**
     * onKeyPress event handler for editor search box for gutenberg editor.
     *
     * @param HTML_DOM_EVENT  event OnKeyPress event
     * @param HTMLElement caller_element caller of this function
     *
     * @since 1.9.0
     */
    function gutenberg_editor_onkeypress(event, props) {
        if (event.keyCode === ENTER_KEY_CODE) {
            event.preventDefault();
            admin_show_create_shortcode_popup_gutenberg(props);
        }
    }

    /**
     * Returns elements not present in second array but in first
     *
     * @param Array arr1
     * @param Array arr2
     *
     * @since 1.5.0
     *
     * @return Array difference between two arrays
     */
    function get_diff_between_two_arrays( arr1, arr2 ) {
        return arr1.filter( function( a ) {
            return arr2.indexOf( a ) == -1;
        } );
    }

    /**
     * Resets marketplace drop down with the values provided in new_store_id_list
     *
     * @param HTMLElement marketplace_dropdown
     * @param Array marketplace_list
     * @param String selected_marketplace
     *
     * @since 1.5.0
     */
    function reset_marketplace_dropwdown( marketplace_dropdown, new_marketplace_list, selected_marketplace ) {
        marketplace_dropdown.empty();
        marketplace_dropdown.append( '<option value="' + SELECT_DROPDOWN_VALUE + '" disabled="disabled">' + meta_box_tab_context.select_marketplace_label + '</option>' );
        if( selected_marketplace ) {
            marketplace_dropdown.append( '<option>' + selected_marketplace + '</option>' );
        }
        $.each( new_marketplace_list, function( key, marketplace ) {
            marketplace_dropdown.append( '<option>' + marketplace + '</option>' );
        } );
        marketplace_dropdown.val( selected_marketplace || SELECT_DROPDOWN_VALUE );
    }

    /**
     * Adds a close button to the tab
     *
     * @param jQueryObject pop_up_container The pop up container in which all content in a tab resides
     *
     * @since 1.5.0
     *
     */
    function add_close_button( pop_up_container ) {
        var close_icon = "<span class='ui-icon ui-icon-close' role='presentation'></span>";
        $( '#aalb-tabs ul li' ).last().append( close_icon );
    }

    /**
     * Change header of tab with marketplace name
     *
     * @param jQueryObject pop_up_container The pop up container in which all content in a tab resides
     * @param String marketplace
     *
     * @since 1.5.0
     *
     */
    function change_header_of_tab( pop_up_container, marketplace ) {
        var url = '#' + pop_up_container.parent().closest( 'div' ).attr( "id" );
        //Why double Quotes & single quote around url :https://stackoverflow.com/questions/31197452/syntax-error-unrecognized-expression-for-href/31197472
        $( 'a[href="' + url + '"]' ).text( marketplace );
    }

    /**
     * Checks if any marketplace contains more than one asin selected
     *
     * @return bool does any marketplace contains multiple asins
     *
     * @since 1.5.0
     */
    function does_any_marketplace_contains_multiple_asin() {
        return Object.keys( marketplace_pop_up_json ).some( function( marketplace ) {
            return marketplace_pop_up_json[ marketplace ].selected_asin.length > 1;
        } );
    }

    /**
     * Checks if any marketplace exists for which no asin is selected
     *
     * @param String marketplace
     *
     * @return Array marketplaces list for which no asin is selected
     *
     * @since 1.5.0
     */
    function get_marketplaces_containing_no_asin_selected() {
        return Object.keys( marketplace_pop_up_json ).filter( function( marketplace ) {
            return marketplace_pop_up_json[ marketplace ].selected_asin.length === 0;
        } );
    }

    /**
     * Creates HTML for the selected ASIN from search results
     *
     * @param String data_asin ASIN to be added
     * @param jQueryObject element The search item clicked in search results to be added to selected ASIN list
     *
     * @return HTMLElement HTML of selected ASIN
     *
     * @since 1.5.0
     */
    function create_selected_asin_html( data_asin, element ) {
        var productImage = $( element ).find( "img" ).attr( "src" );
        var productTitle = $( element ).find( "div.aalb-admin-item-search-items-item-title" ).text();
        var productPrice = $( element ).find( "div.aalb-admin-item-search-items-item-price" ).text();
        //ToDO: See if handlebars can be leveraged here like in credentials.js
        var selectedAsinHTML = '<div class="aalb-selected-item"';
        selectedAsinHTML += ' data-asin="' + data_asin + '">';
        selectedAsinHTML += '<div class="aalb-selected-item-img-wrap"><span class="aalb-selected-item-close">&times;</span>';
        selectedAsinHTML += '<img class="aalb-selected-item-img" src="' + productImage + '"></img></div>';
        selectedAsinHTML += '<div class="aalb-selected-item-title"><h3>' + productTitle + '</h3>';
        selectedAsinHTML += '<p class="aalb-selected-item-price">' + productPrice + '<br></p></div>';
        return selectedAsinHTML;
    }

    /**
     * Add json object with marketplace as key
     *
     * @param String marketplace
     * @param String store_id
     *
     *
     * @since 1.5.0
     */
    function add_entry_in_marketplace_json( marketplace, store_id ) {
        marketplace_pop_up_json[ marketplace ] = {
            "store_id"     : store_id,
            "selected_asin": []
        };
    }

    /**
     * Resets store-id drop down with the values provided in new_store_id_list
     *
     * @param HTMLElement store_id_dropdown
     * @param Array new_store_id_list
     *
     * @since 1.5.0
     */
    function reset_store_id_list( store_id_dropdown, new_store_id_list ) {
        store_id_dropdown.empty();
        store_id_dropdown.append( '<option value="' + SELECT_DROPDOWN_VALUE + '" disabled="disabled">' + meta_box_tab_context.select_associate_id_label + '</option>' );
        $.each( new_store_id_list, function( key, store_id ) {
            store_id_dropdown.append( '<option>' + store_id + '</option>' );
        } );
        store_id_dropdown.val( new_store_id_list[ 0 ] );
    }

    /**
     * Insert Loading search results spinner and content
     *
     * @param jQueryObject pop_up_container The pop up container in which all content in a tab resides
     *
     * @since 1.5.0
     */
    function insert_search_loading_box( pop_up_container ) {
        delete_stale_pop_up_content( pop_up_container );
        var admin_pop_up_content_hbs = $( "#aalb-admin-pop-up-content-hbs" ).html();
        if( admin_pop_up_content_hbs != null ) {
            var admin_pop_up_content_template = Handlebars.compile( admin_pop_up_content_hbs );
            var admin_pop_up_content_html = admin_pop_up_content_template( admin_pop_up_content_context );
            pop_up_container.find( ".aalb-admin-popup-search-result .aalb-admin-popup-placeholder" ).remove();
            pop_up_container.find( ".aalb-admin-popup-search-result" ).append( admin_pop_up_content_html );
        }
    }

    /**
     * Delete existing content in pop-up container
     *
     * @param jQueryObject pop_up_container The pop up container in which all content in a tab resides
     *
     * @since 1.5.0
     */
    function delete_stale_pop_up_content( pop_up_container ) {
        var pop_up_content = pop_up_container.find( '.aalb-admin-popup-content' );
        if( pop_up_content.length !== 0 ) {
            pop_up_content.remove();
        }
    }

    /**
     * Adds a new jQuery tab
     *
     * @since 1.5.0
     */
    function add_tab() {
        var id = "aalb_tab" + tab_counter++;
        var tab_template = "<li><a href=#" + id + ">" + aalb_strings.pop_up_new_tab_label + "</a></li>";
        tabs.find( ".ui-tabs-nav" ).append( tab_template );
        tabs.append( "<div id='" + id + "'>" );
        tabs.tabs( "refresh" );

        var aalb_meta_box_tab_hbs = $( "#aalb-metabox-tab-hbs" ).html();
        if( aalb_meta_box_tab_hbs != null ) {
            var aalb_meta_box_template = Handlebars.compile( aalb_meta_box_tab_hbs );
            var aalb_meta_box_html = aalb_meta_box_template( meta_box_tab_context );
            $( 'div#aalb-tabs' + ' #' + id ).append( aalb_meta_box_html );
        }
        $( '#' + id + ' .aalb-marketplace-names-list' ).val( SELECT_DROPDOWN_VALUE );
        $( '#' + id + ' .aalb-admin-popup-store-id' ).empty();
        $( '#' + id + ' .aalb-admin-popup-store-id' ).append( '<option>' + meta_box_tab_context.select_associate_id_label + '</option>' );
        $( '#' + id + ' .aalb-marketplace-names-list' ).val( SELECT_DROPDOWN_VALUE );
        $( '#' + id + ' .aalb-admin-popup-input-search' ).val( keyword_for_search );
    }

    /**
     * Load search pop-up box
     *
     * @since 1.4.12
     */
    function load_search_pop_up() {
        var aalb_meta_box_tab_partial = $( "#aalb-metabox-tab-hbs" ).html();
        if( aalb_meta_box_tab_partial != null ) {
            Handlebars.registerPartial( "aalb-metabox-tab-hbs", aalb_meta_box_tab_partial );

            Handlebars.registerHelper( 'selected', function( current_option, selected_option ) {
                return (current_option === selected_option) ? 'selected' : '';
            } );
            var aalb_search_pop_up_hbs = $( "#aalb-search-pop-up-hbs" ).html();
            if( aalb_search_pop_up_hbs != null ) {
                var aalb_search_pop_up_template = Handlebars.compile( aalb_search_pop_up_hbs );
                var aalb_search_pop_up_html = aalb_search_pop_up_template( search_pop_up_context );
                $( "#aalb-admin-pop-up" ).prepend( aalb_search_pop_up_html );
                load_jQuery_tabs();
            }
        }
    }

    /**
     * Load JQuery tabs
     *
     * @since 1.5.0
     */
    function load_jQuery_tabs() {
        tabs = $( "#aalb-tabs" ).tabs();
        tabs.css( {
            'overflow': 'auto'
        } );
        $( '#aalb-tabs' ).removeClass( 'ui-widget-content' );
        //Binding the event here as this tab is created dynamically on every click of search button from editor
        $( "#aalb-tabs" ).tabs( {
            activate: function( event, ui ) {
               //Below fetches the id of active tab & find marketplace dropdown element in that active tab
                var maketplace_dropdown = $( '#' + ui.newPanel.attr( 'id' ) ).find( '.aalb-marketplace-names-list' );
                var not_set_marketplace = get_diff_between_two_arrays( Object.keys( marketplace_store_id_mapping ), Object.keys( marketplace_pop_up_json ) );
                reset_marketplace_dropwdown( maketplace_dropdown, not_set_marketplace, maketplace_dropdown.val() );
            }
        } );
    }

    /**
     * Resizing thickbox on change in window dimensions
     * Setting a max width and height of 1280x800 px for readability and to lessen distortion
     */
    function resize_thickbox() {
        var tb_width = Math.min( 1280, 0.6 * $( window ).width() );
        var tb_height = Math.min( 800, 0.9 * $( window ).height() );
        $( document ).find( '#TB_ajaxContent' ).width( tb_width - 35 ).height( tb_height - 90 );
        $( document ).find( '#TB_window' ).width( tb_width ).height( tb_height );
        $( document ).find( '#TB_window' ).css( { marginLeft: '-' + tb_width / 2 + 'px', top: tb_height / 12 } );
        $( document ).find( '#TB_window' ).removeClass();
    }

    /**
     * Display pop up thickbox
     *
     * @param HTMLElement search_button  reference to the clicked button element to get to the keyword of interest.
     *
     * @since 1.4.3 added param search_button
     */
    function admin_show_create_shortcode_popup( search_button ) {
        var editor_selected_text = get_selected_text_from_editor();

        var editor_search_box_input = $( search_button ).siblings( ".aalb-admin-input-search" );

        var search_keywords = editor_selected_text || editor_search_box_input.val();
        if( search_keywords ) {
            keyword_for_search = search_keywords;
            $( '#aalb-search-pop-up' ).remove();
            tab_counter = 2;
            load_search_pop_up();
            if( editor_selected_text ) {
                //Make ProductLink template as a default choice of template when some text is selected.
                $( "#aalb_template_names_list" ).val( 'ProductLink' );
            }

            var pop_up_container = $( '#aalb-tabs' ).find( '.aalb-pop-up-container' );
            add_tab();
            insert_search_loading_box( pop_up_container );
            add_entry_in_marketplace_json( default_marketplace, default_store_id );
            tb_show( aalb_strings.add_aalb_shortcode, '#TB_inline?inlineId=aalb-admin-popup-container', false );
            resize_thickbox();

            // Getting the ItemSearch results
            admin_get_item_search_items( search_keywords, pop_up_container );

            //Setting search input of shortcode popup with search keyword.
            $( ".aalb-admin-popup-input-search" ).attr( 'value', search_keywords );

            //Setting editor search input with search keyword.
            editor_search_box_input.attr( 'value', search_keywords );

        } else {
            alert( aalb_strings.empty_product_search_bar );
            editor_search_box_input.focus();
        }
    }

    /**
     * Display pop up thickbox in gutenberg editor.
     * @param props - Gutenberg props.
     */
    function admin_show_create_shortcode_popup_gutenberg(props) {
        if (props && props.attributes.searchKeyword) {
            gb_props = props;
            keyword_for_search = props.attributes.searchKeyword;
            $('#aalb-search-pop-up').remove();
            tab_counter = 2;
            load_search_pop_up();

            var pop_up_container = $('#aalb-tabs').find('.aalb-pop-up-container');
            add_tab();
            insert_search_loading_box(pop_up_container);
            add_entry_in_marketplace_json(default_marketplace, default_store_id);
            tb_show(aalb_strings.add_aalb_shortcode, '#TB_inline?inlineId=aalb-admin-popup-container', false);
            resize_thickbox();
            // Getting the Itemsearch results
            admin_get_item_search_items(keyword_for_search, pop_up_container, props);
            //Setting search input of shortcode popup with search keyword.
            $(".aalb-admin-popup-input-search").attr('value', keyword_for_search);

        } else {
            alert(aalb_strings.empty_product_search_bar);
        }
    }

    /**
     * Search items from within the thickbox
     *
     * @param jQueryObject pop_up_container The pop up container in which all content in a tab resides
     *
     */
    function admin_popup_search_items( pop_up_container ) {
        var keywords = $( pop_up_container ).find( '.aalb-admin-popup-input-search' ).val();
        if( keywords ) {
            insert_search_loading_box( pop_up_container );
            // Getting the ItemSearch results
            admin_get_item_search_items( keywords, pop_up_container );
            pop_up_container.find( ".aalb-admin-popup-input-search" ).attr( 'value', keywords );
        } else {
            alert( aalb_strings.empty_product_search_bar );
            $( ".aalb-admin-popup-input-search" ).focus();
        }
    }

    /**
     * Search items for the keywords and display it in the pop up thickbox
     *
     * @param String keywords Items to search for.
     * @param jQueryObject pop_up_container The pop up container in which all content in a tab resides
     */
    function admin_get_item_search_items( keywords, pop_up_container ) {
        $.ajax( {
            url    : api_pref.ajax_url,
            type   : 'GET',
            data   : {
                "action"           : api_pref.action,
                "item_search_nonce": api_pref.item_search_nonce,
                "keywords"         : keywords,
                "marketplace"      : $( pop_up_container ).find( '.aalb-marketplace-names-list' ).val(),
                "store_id"         : $( pop_up_container ).find( '.aalb-admin-popup-store-id' ).val()
            },
            success: function( xml ) {
                var items_xml = $( xml ).find( "Item" );
                if( items_xml.length > 0 ) {
                    var items = [];
                    var i = 0;
                    items_xml.each( function() {
                        //selecting maximum of max_search_result_items elements
                        if( i < api_pref.max_search_result_items ) {
                            var item = {};
                            item.asin = $( this ).find( "ASIN" ).text();
                            item.title = $( this ).find( "Title" ).text();
                            item.image = $( this ).find( "LargeImage" ).first().find( "URL" ).text();
                            item.price = $( this ).find( "LowestNewPrice" ).find( "FormattedPrice" ).text();
                            items.push( item );
                        }
                        i++;
                    } );

                    var html = template( items );
                    $( pop_up_container ).find( ".aalb-admin-item-search-items" ).append( html );
                    $( pop_up_container ).find( ".aalb-admin-popup-more-results" ).attr( 'href', $( xml ).find( "MoreSearchResultsUrl" ).text() );
                    $( pop_up_container ).find( ".aalb-admin-item-search-loading" ).slideUp( "slow" );
                    $( pop_up_container ).find( ".aalb-admin-item-search" ).fadeIn( "slow" );
                } else {
                    var errors_xml = $( xml ).find( "Error" );
                    if( errors_xml.length > 0 ) {
                        var htmlerror = "";
                        errors_xml.each( function() {
                            htmlerror += $( this ).find( "Message" ).text() + "<br>";
                        } );
                        $( pop_up_container ).find( ".aalb-admin-item-search-loading" ).html( htmlerror );
                    } else {
                        $( pop_up_container ).find( ".aalb-admin-item-search-loading" ).html( xml );
                    }
                }
            },
            error  : function( request, status ) {
                if( status === "timeout" ) {
                    $( pop_up_container ).find( ".aalb-admin-item-search-loading" ).html( aalb_strings.paapi_request_timeout_error );
                } else {
                    $( pop_up_container ).find( ".aalb-admin-item-search-loading" ).html( "An Error Occurred : " + status );
                }
            },
            timeout: api_pref.WORDPRESS_REQUEST_TIMEOUT
        } );

        $( "#aalb-add-shortcode-button" ).unbind().click( function() {
            var selected = get_selected_text_from_editor();
            var non_asin_selected_marketplaces = get_marketplaces_containing_no_asin_selected();
            if( non_asin_selected_marketplaces.length === 0 ) {
                if( selected ) {
                    /* If there was some text selected in the wordpress post editor. Implies amazon_textlink */
                    if( does_any_marketplace_contains_multiple_asin() ) {
                        alert( aalb_strings.short_code_create_failure );
                    } else {
                        $( "#aalb-add-shortcode-alert" ).fadeTo( "fast", 1 );
                        add_shortcode( AALB_SHORTCODE_AMAZON_TEXT );
                    }
                } else {
                    $( "#aalb-add-shortcode-alert" ).fadeTo( "fast", 1 );
                    add_shortcode( AALB_SHORTCODE_AMAZON_LINK );
                }
            } else {
                alert( aalb_strings.no_asin_selected_error + non_asin_selected_marketplaces.toString() );
            }
        } );
    }

    /**
     * Adds the given shortcode to the editor
     *
     * @param String Shortcode type to be added
     */
    function add_shortcode( shortcodeName ) {
        var shortcodeJson;
        var selectedAsins = get_selected_asins();
        var selectedTemplate = get_selected_template();
        var selectedStore = get_selected_store();
        var selectedMarketplace = get_selected_marketplace();

        if( shortcodeName === AALB_SHORTCODE_AMAZON_LINK ) {
            shortcodeJson = {
                "name"  : AALB_SHORTCODE_AMAZON_LINK,
                "params": {
                    "asins"      : selectedAsins,
                    "template"   : selectedTemplate,
                    "store"      : selectedStore,
                    "marketplace": selectedMarketplace
                }
            };
        } else if( shortcodeName === AALB_SHORTCODE_AMAZON_TEXT ) {
            shortcodeJson = {
                "name"  : AALB_SHORTCODE_AMAZON_TEXT,
                "params": {
                    "asin"       : selectedAsins,
                    "text"       : get_selected_text_from_editor(),
                    "template"   : selectedTemplate,
                    "store"      : selectedStore,
                    "marketplace": selectedMarketplace
                }
            };
        } else {
            console.log( "Invalid Shortcode provided!" );
            return;
        }
        get_link_id( shortcodeJson );
    }

    /**
     * Handler function when the Add Shortcode button is clicked
     * and link id is retrieved.
     *
     * @param Object shortcodeJson  Object describing the shortcode
     */
    function add_shortcode_click_handler( shortcodeJson ) {
        is_editor_gutenberg() ? create_shortcode_in_gb(shortcodeJson) : create_shortcode(shortcodeJson);
        tb_remove();
    }

    /**
     * Builds shortcode from given JSON
     *
     * @param Object shortcodeJson  Object describing the shortcode
     *
     * @return String returns the Shortcode String
     */
    function buildShortcode( shortcodeJson ) {
        var shortcodeParamsString = "";
        var shortcodeParam = "";
        for( shortcodeParam in shortcodeJson.params ) {
            if( shortcodeJson.params.hasOwnProperty( shortcodeParam ) ) {
                shortcodeParamsString += " " + shortcodeParam + "='" + shortcodeJson.params[ shortcodeParam ] + "'";
            }
        }

        var shortcodeString = "[" + shortcodeJson.name + shortcodeParamsString + "]";
        return shortcodeString;
    }

    /**
     * Get unique link id whenever add shortcode button is clicked
     *
     * @param Object shortcodeJson  Object describing the shortcode
     */
    function get_link_id( shortcodeJson ) {
        $.post( api_pref.ajax_url, {
            "action": "get_link_code", "shortcode_name": shortcodeJson.name, "shortcode_params": shortcodeJson.params
        } ).success( function( data ) {
            link_id = data;
        } ).fail( function() {
            link_id = "";
        } ).always( function() {
            shortcodeJson.params.link_id = link_id;
            $( "#aalb-add-shortcode-alert" ).fadeTo( "slow", 0 );
            add_shortcode_click_handler( shortcodeJson );
        } );
    }

    /**
     * Add the shortcode to the display editor
     *
     * @param Object shortcodeJson  Object describing the shortcode
     */
    function create_shortcode( shortcodeJson ) {
        send_to_editor( buildShortcode( shortcodeJson ) );
    }

    /**
     * Add shortcode attribute in gutenberg block attribute.
     * @param shortcodeJson
     */
    function create_shortcode_in_gb(shortcodeJson) {
        shortCoeValue = buildShortcode(shortcodeJson);
        gb_props.setAttributes({shortCodeContent: shortCoeValue});
    }

    /**
     * Gets the selected Asins
     *
     * @return String Selected Asins
     */
    function get_selected_asins() {
        //Map first creates an Array of ASINs(every array element contains comma separated asins of one marketplace) & later separate these array elements by join
        return Object.values( marketplace_pop_up_json ).map( function( marketplace ) {
            return marketplace.selected_asin.toString();
        } ).join( '|' );
    }

    /**
     * Get the selected Template style
     *
     * @return String Selected Template style
     */
    function get_selected_template() {
        return $( '#aalb_template_names_list' ).val();
    }

    /**
     * Get the selected associate tag
     *
     * @return String Selected Associate tag
     */
    function get_selected_store() {
        return Object.values( marketplace_pop_up_json ).map( function( marketplace ) {
            return marketplace.store_id;
        } ).join( '|' );
    }

    /**
     * Get the selected marketplace
     *
     * @return String Selected Marketplace bar spearted list
     */
    function get_selected_marketplace() {
        return Object.keys( marketplace_pop_up_json ).join( '|' );
    }

    /**
     * Get selected text from the editor.
     *
     * @return String Selected text from the wordpress post editor.
     */
    function get_selected_text_from_editor() {
        if( tinyMCE.activeEditor ) {
            return tinyMCE.activeEditor.selection.getContent( { format: "text" } );
        } else {
            return null;
        }
    }

    /**
     * To check the validity of ASIN based on different actions
     *
     * @param String Asin ASIN of Product selected by Admin
     * @param String marketplace Marketplace for which this ASIN is being added
     *
     * @return bool true
     *
     * @since 1.5.0
     **/
    function validate_asin_addition( asin, marketplace ) {
        if( marketplace_pop_up_json[ marketplace ].selected_asin.indexOf( asin ) != -1 || marketplace_pop_up_json[ marketplace ].selected_asin.length === MAX_ALLOWED_ASINS_IN_SELECTION ) {
            return false;
        }

        var selected_template = get_selected_template();
        if( SINGLE_ASIN_TEMPLATE[ selected_template ] && marketplace_pop_up_json[ marketplace ].selected_asin.length === 1 ) {
            $( '#aalb-add-template-asin-error' ).text( aalb_strings.template_asin_error );
            $( '#aalb-add-shortcode-button' ).prop( 'disabled', true );
        }
        return true;
        // reset_add_short_button_and_error_warnings();
    }

    /**
     * To remove ASIN element from list
     *
     * @param element HTMLDivElement
     **/
    function remove_asin( element ) {
        var removed_product_asin = element.getAttribute( 'data-asin' );
        var marketplace = $( element ).closest( '.aalb-pop-up-container' ).find( '.aalb-marketplace-names-list' ).val();
        $( element ).remove();
        marketplace_pop_up_json[ marketplace ].selected_asin.splice( marketplace_pop_up_json[ marketplace ].selected_asin.indexOf( removed_product_asin ), 1 );
        if( SINGLE_ASIN_TEMPLATE[ get_selected_template() ] && !does_any_marketplace_contains_multiple_asin() ) {
            reset_add_short_button_and_error_warnings();
        }
    }

    /**
     * To enable add short code button and remove  template asin error
     **/
    function reset_add_short_button_and_error_warnings() {
        var add_short_code_button = $( '#aalb-add-shortcode-button' );
        add_short_code_button.prop( 'disabled', false );
        $( '#aalb-add-template-asin-error' ).text( '' );
    }

    /**
     * To disable editor search for AALB plugin along with message
     *
     * @param String error_msg Error message
     *
     * @since 1.4.12
     *
     **/
    function disable_editor_search( error_msg ) {
        $( ".aalb-admin-button-create-amazon-shortcode" ).addClass( 'aalb-admin-button-create-amazon-shortcode-disabled' );
        $( ".aalb-admin-input-search" ).prop( 'disabled', true );
        var admin_searchbox_tooltip = $( '.aalb-admin-editor-tooltip' );
        admin_searchbox_tooltip.html( error_msg );
        admin_searchbox_tooltip.addClass( 'aalb-admin-searchbox-tooltip-text' );
        admin_searchbox_tooltip.removeClass( 'aalb-admin-hide-display' );
    }

    /**
     * Function to check whether Gutenberg is activated and the current editor is set to load Gutenberg.
     * gb_props will not be set if editor is not gutenberg.
     */
    function is_editor_gutenberg() {
        return (gb_props != null);
    }

    return {
        admin_show_create_shortcode_popup                   : admin_show_create_shortcode_popup,
        editor_searchbox_keypress_event_handler             : editor_searchbox_keypress_event_handler,

        // Callbacks for gutenberg editor.
        admin_show_create_shortcode_popup_gutenberg         : admin_show_create_shortcode_popup_gutenberg,
        gutenberg_editor_onkeypress                         : gutenberg_editor_onkeypress


    };

})( jQuery );
