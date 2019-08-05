/*
 Copyright 2016-2018 Amazon.com, Inc. or its affiliates. All Rights Reserved.

 Licensed under the GNU General Public License as published by the Free Software Foundation,
 Version 2.0 (the "License"). You may not use this file except in compliance with the License.
 A copy of the License is located in the "license" file accompanying this file.

 This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
 either express or implied. See the License for the specific language governing permissions
 and limitations under the License.
 */

var codeMirrorHtml = CodeMirror.fromTextArea( document.getElementById( 'aalb_template_template_html_box' ), {
    lineNumbers: true,
    value: "",
    mode: "xml"
} );

var codeMirrorCss = CodeMirror.fromTextArea( document.getElementById( 'aalb_template_template_css_box' ), {
    lineNumbers: true,
    value: "",
    mode: "css"
} );

jQuery.ajaxSetup( {
    //Disable caching of AJAX responses
    cache: false
} );

/**
 * Trigger onChange event of the template select dropdown on page load
 */
jQuery( document ).ready( function() {
    if ( jQuery( '#aalb_template_list_select' ).val() != 'new' ) {
        jQuery( "#aalb_template_list_select" ).trigger( "change" );
    }
} );

/**
 * Set the JS and CSS of the particular template in the respective text area
 *
 * @param HTML DOM OBJECT element Selected Template name
 */
function aalb_template_select_template_onchange( element ) {
    if ( element.value == 'new' ) {
        codeMirrorCss.setValue( '' );
        codeMirrorHtml.setValue( '' );
        set_template_read_only( false );
        jQuery( '#aalb_template_name' ).removeAttr( "readonly" );
        jQuery( '#aalb_template_name' ).val( '' );
        jQuery( '#submit_remove' ).attr( 'disabled', 'disabled' );
        jQuery( '#clone_template' ).attr( 'disabled', 'disabled' );
    } else {
        jQuery( '#clone_template' ).removeAttr( 'disabled' );
        jQuery( '#aalb_template_name' ).val( element.value );
        jQuery( '#aalb_template_name' ).attr( "readonly", "readonly" );
        //Make the Amazon Default templates as Read-Only
        if ( wp_opt.aalb_default_templates.split( "," ).indexOf( element.value ) >= 0 ) {
            jQuery( '#submit_remove' ).attr( 'disabled', 'disabled' );
            set_template_read_only( true );
            base = wp_opt.plugin_url + "template/" + element.value;
            jQuery.get( base + ".css", function( data ) {
                codeMirrorCss.setValue( data );
            } );
            jQuery.get( base + ".mustache", function( data ) {
                codeMirrorHtml.setValue( data );
            } );
        } else {
            jQuery( '#submit_remove' ).removeAttr( 'disabled' );
            set_template_read_only( false );
            base = wp_opt.upload_url + element.value;
            jQuery.post( wp_opt.ajax_url, {
                "action": "get_custom_template_content",
                "css": base + ".css",
                "mustache": base + ".mustache"
            }, function( json ) {
                codeMirrorCss.setValue( json.css );
                codeMirrorHtml.setValue( json.mustache );
            }, "json" );
        }
    }
}

/**
 * Clones an existing template into a new one.
 *
 */
function clone_existing_template() {
    var templateNameToClone = jQuery( '#aalb_template_list_select' ).val();
    jQuery( '#aalb_template_list_select' ).val( 'new' );
    jQuery( '#aalb_template_name' ).removeAttr( "readonly" );
    jQuery( '#aalb_template_name' ).val( 'CopyOf-' + templateNameToClone );
    jQuery( '#clone_template' ).attr( 'disabled', 'disabled' );
    jQuery( '#submit_remove' ).attr( 'disabled', 'disabled' );
    //Add CSS Prefix for Amazon Default Templates to prevent style overlapping for clones
    if ( wp_opt.aalb_default_templates.split( "," ).indexOf( templateNameToClone ) >= 0 ) {
        set_template_read_only( false );
        var randomNumForPrefix = Math.floor( (Math.random() * 1000) + 1 );
        var prefixRegExObject = new RegExp( 'aalb-', "g" );
        var prefixReplaceValue = 'aalb-' + randomNumForPrefix + '-';
        codeMirrorHtml.setValue( codeMirrorHtml.getValue().replace( prefixRegExObject, prefixReplaceValue ) );
        codeMirrorCss.setValue( codeMirrorCss.getValue().replace( prefixRegExObject, prefixReplaceValue ) );
    }
}

/**
 * Sets the HTML and CSS boxes to read only if TRUE is passed.
 *
 */
function set_template_read_only( isReadOnly ) {
    codeMirrorHtml.setOption( "readOnly", isReadOnly );
    codeMirrorCss.setOption( "readOnly", isReadOnly );
}
