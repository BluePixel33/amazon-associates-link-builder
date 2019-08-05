/*
 Copyright 2016-2018 Amazon.com, Inc. or its affiliates. All Rights Reserved.

 Licensed under the GNU General Public License as published by the Free Software Foundation,
 Version 2.0 (the "License"). You may not use this file except in compliance with the License.
 A copy of the License is located in the "license" file accompanying this file.

 This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
 either express or implied. See the License for the specific language governing permissions
 and limitations under the License.
 */
//ToDo: Use something webpack(https://webpack.js.org/) to leverage use of ES6
var aalb_credentials_object = (function( $ ) {
    var SELECT_MARKETPLACE_DROPDOWN_VALUE = "no-selection";
    //ToDO: Pass this separator from backend to maintain consistency
    var STORE_ID_SEPARATOR = ',';

    //Contains the TableRowHTMLElement of the marketplace to be removed
    var marketplace_row_to_remove = "";
    var default_marketplace = aalb_cred_data.default_marketplace_value;
    var marketplace_list = $.parseJSON( aalb_cred_data.marketplace_list );
    var marketplace_row_context = {
        "marketplace_list"                : marketplace_list,
        "set_as_default_marketplace_label": aalb_cred_strings.set_as_default_marketplace_label,
        "remove_marketplace_label"        : aalb_cred_strings.remove_marketplace_label,
        "default_marketplace_label"       : aalb_cred_strings.default_marketplace_label,
        "select_marketplace_label"        : aalb_cred_strings.select_marketplace_label,
        "tracking_id_placeholder"         : aalb_cred_strings.tracking_id_placeholder,
        "default_marketplace_value"       : aalb_cred_data.default_marketplace_value,
        "marketplace"                     : "",
        "tracking_ids"                    : ""
    };

    var store_ids_settings_context = {
        "marketplace_row_context"          : marketplace_row_context,
        "store_ids_for_marketplaces"       : get_marketplaces_to_store_id_json(),
        "old_store_id_db_key"              : aalb_cred_data.old_store_id_db_key,
        "new_store_id_db_key"              : aalb_cred_data.new_store_id_db_key,
        "default_store_id_db_key"          : aalb_cred_data.default_store_id_db_key,
        "default_marketplace_db_key"       : aalb_cred_data.default_marketplace_db_key,
        "marketplace_settings_info_message": aalb_cred_strings.marketplace_settings_info_message,
        "tracking_id_settings_info_message": aalb_cred_strings.tracking_id_settings_info_message,
        "tracking_id_fieldset_label"       : aalb_cred_strings.tracking_id_fieldset_label,
        "add_a_marketplace_label"          : aalb_cred_strings.add_a_marketplace_label
    };
    $( function() {
        if( marketplace_list == "" ) {
            show_dismissable_error_message( aalb_cred_strings.marketplace_list_empty_error );
        }
        load_store_id_settings_section();

        $( '#aalb-terms-checkbox' ).click( function() {
            if( $( this ).is( ':checked' ) ) {
                $( "#submit" ).removeAttr( 'disabled' );
            } else {
                $( '#submit' ).attr( 'disabled', 'disabled' );
            }
        } );

        //Dynamic binding of "Remove marketplace" with click event using delegated event
        $( '#aalb-store-ids-settings' ).on( 'click', '.aalb-remove-marketplace', function() {
            marketplace_row_to_remove = $( this ).closest( '.aalb-marketplace-row' );
            if( is_marketplace_row_removal_allowed( marketplace_row_to_remove.find( 'select' ).val() ) ) {
                //ToDO: Below modal box size is overriden by the amp.js provided by "Accelerated mobile pages" plugin
                tb_show( aalb_cred_strings.remove_marketplace_confirmation, '#TB_inline?width=350&height=85&inlineId=aalb-remove-marketplace-confirmation-container', false );
            } else {
                show_dismissable_error_message( aalb_cred_strings.remove_last_marketplace_error );
                marketplace_row_to_remove = "";
            }
        } );

        //Bind click event with positive confirmation to remove marketplace
        $( '#aalb-remove-yes-button' ).on( 'click', function() {
            var marketplace_being_removed = marketplace_row_to_remove.find( 'select' ).val();
            marketplace_row_to_remove.remove();
            marketplace_row_to_remove = "";
            //Marketplace is removed first to handle case if the first row contains default marketplace
            if( marketplace_being_removed === default_marketplace ) {
                make_a_marketplace_default( $( '.aalb-marketplace-row:first' ).find( 'select' ).val() );
            }
            window.tb_remove();
        } );

        //Bind click event with negative confirmation to remove marketplace
        $( '#aalb-remove-no-button' ).on( 'click', function() {
            marketplace_row_to_remove = "";
            window.tb_remove();
        } );

        //Dynamic binding of "Default marketplace" with click event using delegated event
        $( '#aalb-store-ids-settings' ).on( 'click', '.aalb-default-marketplace a', function() {
            var marketplace_clicked = $( this ).closest( '.aalb-marketplace-row' ).find( 'select' ).val();
            //To handle if the marketplace clicked has not been still set by customer
            if( marketplace_clicked !== null ) {
                make_a_marketplace_default( marketplace_clicked );
            }
        } );

        //Bind click event with "Add marketplace" anchor tag
        $( '#aalb-add-new-marketplace' ).on( 'click', function() {
            var locale_row_hbs = $( "#aalb-marketplace-row-hbs" ).html();
            if( locale_row_hbs != null ) {
                var locale_row_template = Handlebars.compile( locale_row_hbs );
                var locale_row_html = locale_row_template( marketplace_row_context );
                $( ".aalb-add-new-marketplace" ).before( locale_row_html );
            }
        } );

        //Bind click event with Dismiss notice button
        $( '.aalb-settings-page' ).on( 'click', '.aalb-dismiss-notice', function() {
            $( this ).closest( '.aalb-cred-error' ).remove();
        } );

        //Bind focus event with dropdown of marketplaces
        $( "#aalb-store-ids-settings" ).on( 'focus', '.aalb-marketplace-row select', function() {
            $( this ).data( 'prev-val', $( this ).val() );
        } );

        //Bind change event with dropdown of marketplaces
        $( "#aalb-store-ids-settings" ).on( 'change', '.aalb-marketplace-row select', function() {
            var prev_marketplace = $( this ).data( 'prev-val' );
            var new_marketplace = $( this ).val();
            if( is_marketplace_not_set_in_any_other_row( new_marketplace ) ) {
                $( this ).val( new_marketplace );
                var is_default_marketplace_row_not_present = $( '.aalb-marketplace-row select' ).filter( function( index, marketplace_dropdown ) {
                    return $( marketplace_dropdown ).val() === default_marketplace;
                } ).length === 0;
                if( prev_marketplace === default_marketplace || default_marketplace === "" || is_default_marketplace_row_not_present ) {
                    default_marketplace = new_marketplace;
                    if( prev_marketplace === null || is_default_marketplace_row_not_present ) {
                        var default_marketplace_column = $( this ).closest( '.aalb-marketplace-row' ).find( '.aalb-default-marketplace' );
                        default_marketplace_column.empty();
                        default_marketplace_column.append( '<span>' + aalb_cred_strings.default_marketplace_label + '</span>' );
                    }
                }
            } else {
                //Null is returned for "Select Marketplace" as this option has been disabled
                $( this ).val( prev_marketplace || SELECT_MARKETPLACE_DROPDOWN_VALUE );
                show_dismissable_error_message( aalb_cred_strings.marketplace_exists_error );
            }
            $( this ).blur();
        } );

        //Sanitize store-id input on change
        $( "#aalb-store-ids-settings" ).on( 'change', '.aalb-marketplace-row input', function() {
            var store_ids_list = $( this ).val().trim().split( STORE_ID_SEPARATOR );
            //Removes empty store-id values from array
            var sanitized_store_ids_list = store_ids_list.map( function( store_id ) {
                return store_id.trim();
            } ).filter( function( store_id ) {
                return store_id !== "";
            } );
            $( this ).val( sanitized_store_ids_list.join( STORE_ID_SEPARATOR ) );
        } );

        $( '#aalb-credentials-form' ).submit( function( event ) {
            if( validate_tracking_ids() ) {
                //Backward compatibility logic to be removed in future
                backward_compatibility_logic();
                set_marketplace_store_id_mapping();
                $( '#' + aalb_cred_data.default_marketplace_db_key ).val( default_marketplace );
            } else {
                return false;
            }
        } );
    } );

    /**
     *  Checks if marketplace row removal is allowed
     *
     * @since 1.4.12
     *
     * @param String marketplace
     * @boolean is_marketplace_row_removal_allowed
     *
     */
    function is_marketplace_row_removal_allowed( marketplace ) {
        //Not selected marketplace removal is allowed
        if( marketplace === null ) {
            return true;
        }
        //Checks if there exist at least two dropdown with marketplace value set
        return $( '.aalb-marketplace-row select' ).filter( function( index, marketplace_dropdown ) {
            //Null is returned for "Select Marketplace" as this option has been disabled
            return $( marketplace_dropdown ).val() !== null;
        } ).length > 1;
    }

    /**
     * Loads the store-ids settings section using handlebars
     *
     * @since 1.4.12
     *
     */
    function load_store_id_settings_section() {
        var locale_row_partial = $( "#aalb-marketplace-row-hbs" ).html();
        if( locale_row_partial != null ) {
            //ToDO: below function of handlebars breaking flow on other pages so load this sciprt only if you are on settings page
            Handlebars.registerPartial( "aalb-marketplace-row-hbs", $( "#aalb-marketplace-row-hbs" ).html() );
            //https://stackoverflow.com/questions/8853396/logical-operator-in-a-handlebars-js-if-conditional
            Handlebars.registerHelper( 'ifCond', function( v1, v2, options ) {
                if( v1 === v2 ) {
                    return options.fn( this );
                }
                return options.inverse( this );
            } );
            var hbs_store_id_settings = $( "#aalb-hbs-store-id-settings" ).html();
            if( hbs_store_id_settings != null ) {
                var store_id_settings_template = Handlebars.compile( hbs_store_id_settings );
                var store_id_setings_html = store_id_settings_template( store_ids_settings_context );
                $( "#aalb-credentials-form" ).prepend( store_id_setings_html );
            }
        }
    }

    /**
     * Get the json for the marketplace-store id mapping to be passed in context
     *
     * @since 1.4.12
     *
     *
     */
    function get_marketplaces_to_store_id_json() {
        var marketplace_store_id_obj = $.parseJSON( aalb_cred_data.new_store_ids );
        var marketplace_store_id_array = [];
        for( var marketplace in marketplace_store_id_obj ) {
            if( marketplace_store_id_obj.hasOwnProperty( marketplace ) ) {
                marketplace_store_id_array.push( {
                    "marketplace" : marketplace,
                    "tracking_ids": marketplace_store_id_obj[ marketplace ].toString()
                } );
            }
        }
        return marketplace_store_id_array;
    }

    /**
     * Checks if store-ids entry is valid for all marketplaces
     *
     * @since 1.4.12
     *
     * @return bool is_store_id_data_valid
     */
    function validate_tracking_ids() {
        if( is_marketplace_not_set_in_any_dropdown() ) {
            show_dismissable_error_message( aalb_cred_strings.marketplace_not_set_error );
            return false;
        } else if( is_store_id_not_entered_for_any_marketplace() ) {
            show_dismissable_error_message( aalb_cred_strings.empty_store_id_error );
            return false;
        } else if( is_no_marketplace_row_added() ) {
            show_dismissable_error_message( aalb_cred_strings.no_marketplace_row_error );
            return false;
        }
        //Add more if validations like above if required
        return true;
    }

    /**
     * Checks if store-ids entry for any marketplace is empty
     *
     * @since 1.4.12
     *
     * @return bool is_marketplace_field_empty
     */
    function is_store_id_not_entered_for_any_marketplace() {
        return $( '.aalb-marketplace-row input' ).filter( function( index, store_id_input ) {
            return $( store_id_input ).val() === '';
        } ).length > 0;
    }

    /**
     * Checks if user has not added any row for marketplace
     *
     * @since 1.4.12
     *
     * @return bool is no marketplace row present
     */
    function is_no_marketplace_row_added() {
        return $( '.aalb-marketplace-row' ).length === 0;
    }

    /**
     * Checks if any local dropdown is left unset for one or more rows
     *
     * @since 1.4.12
     *
     * @return bool is marketplace not set in any dropdown
     */
    function is_marketplace_not_set_in_any_dropdown() {
        return $( '.aalb-marketplace-row select' ).filter( function( index, marketplace_dropdown ) {
            //Null is returned for "Select Marketplace" as this option has been disabled
            return $( marketplace_dropdown ).val() === null;
        } ).length > 0;
    }

    /**
     * Backward compatibility logic to prevent loss of store_ids in case someone switches to older version of plugin before 1.4.12
     *
     * To be deprecated in future
     *
     * @since 1.4.12
     *
     */
    function backward_compatibility_logic() {
        set_default_store_id();
        set_old_store_ids_key();
    }

    /**
     * Set the old store ids key
     *
     * @since 1.4.12
     *
     */
    function set_old_store_ids_key() {
        var store_id_list = [];
        //Append store ids for all marketplaces
        $( '.aalb-store-ids-for-marketplace' ).each( function() {
            store_id_list = store_id_list.concat( $( this ).val().split( STORE_ID_SEPARATOR ) );
        } );
        //Old store_ids(before 1.4.12) were separated by "\r\n"
        $( '#' + aalb_cred_data.old_store_id_db_key ).val( store_id_list.join( "\r\n" ) );
    }

    /**
     * Sets default store id
     *
     * @since 1.4.12
     *
     */
    function set_default_store_id() {
        //Need to maintain the overall default_store_Id to keep the item_look_up api code backward compatible
        var default_marketplace_row = get_row_containing_marketplace( default_marketplace );
        if( default_marketplace_row.length > 0 ) {
            var default_store_id = default_marketplace_row.find( 'input' ).val().split( STORE_ID_SEPARATOR )[ 0 ];
            $( '#' + aalb_cred_data.default_store_id_db_key ).val( default_store_id );
        }
    }

    /**
     * Finds the row containing the marketplace and returns the jQuery Object for that
     *
     * @since 1.4.12
     *
     * @return HTMLTableRowElement
     */
    function get_row_containing_marketplace( marketplace ) {
        return $( '.aalb-marketplace-row' ).filter( function( index, marketplace_row ) {
            return $( marketplace_row ).find( 'select' ).val() === marketplace;
        } );
    }

    /**
     * Sets the marketplace and store id mapping in the hidden input field to be passed on form submission
     *
     * @since 1.4.12
     *
     */
    function set_marketplace_store_id_mapping() {
        var tracking_id_json = {};
        $( '.aalb-marketplace-row' ).each( function() {
            tracking_id_json[ $( this ).find( 'select' ).val() ] = $( this ).find( 'input' ).val().split( "," );
        } );
        $( '#' + aalb_cred_data.new_store_id_db_key ).val( JSON.stringify( tracking_id_json ) );
    }

    /**
     * Shows the dismissible error messages
     *
     * @param String Error Message to display
     *
     * @since 1.4.12
     *
     */
    function show_dismissable_error_message( error_msg ) {
        $( '#aalb-credentials-form' ).before( '<div class="is-dismissible notice notice-error aalb-cred-error"><p>' + error_msg + '</p></div>' );
        $( '.aalb-cred-error:last' ).find( 'p' ).after( '<button type="button" class= "aalb-dismiss-notice notice-dismiss"></button>' );
        $( 'html,body' ).scrollTop( 0 );
    }

    /**
     * Checks if value is already set in some other row
     *
     * @param String marketplace
     *
     * @return bool is marketplace not set in any other row
     *
     * @since 1.4.12
     */
    function is_marketplace_not_set_in_any_other_row( marketplace ) {
        return $( '.aalb-marketplace-row select' ).filter( function( index, marketplace_dropdown ) {
            return $( marketplace_dropdown ).val() === marketplace;
        } ).length < 2;
    }

    /**
     * Make a marketplace default
     *
     * @param String Default marketplace
     *
     * @since 1.4.12
     *
     */
    function make_a_marketplace_default( new_default_marketplace ) {
        $( '.aalb-marketplace-row' ).each( function() {
            var marketplace = $( this ).find( 'select' ).val();
            var element = "";
            if( marketplace === default_marketplace ) {
                element = $( this ).find( '.aalb-default-marketplace' );
                element.find( 'span' ).remove();
                element.append( '<a href="#">' + aalb_cred_strings.set_as_default_marketplace_label + '</a>' );
            } else if( marketplace === new_default_marketplace ) {
                element = $( this ).find( '.aalb-default-marketplace' );
                element.find( 'a' ).remove();
                element.append( '<span>' + aalb_cred_strings.default_marketplace_label + '</span>' );
            }
        } );
        default_marketplace = new_default_marketplace;
    }

})( jQuery );