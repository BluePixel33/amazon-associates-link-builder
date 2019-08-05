<?php

/*
Copyright 2016-2018 Amazon.com, Inc. or its affiliates. All Rights Reserved.

Licensed under the GNU General Public License as published by the Free Software Foundation,
Version 2.0 (the "License"). You may not use this file except in compliance with the License.
A copy of the License is located in the "license" file accompanying this file.

This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
either express or implied. See the License for the specific language governing permissions
and limitations under the License.
*/
namespace AmazonAssociatesLinkBuilder\view\sidebar_partials;
include 'admin_ui_common.php';
include 'credentials_locale_row.php';

use AmazonAssociatesLinkBuilder\constants\Library_Endpoints;
use AmazonAssociatesLinkBuilder\constants\Paapi_Constants;
use AmazonAssociatesLinkBuilder\helper\Credentials_Helper;
use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;
use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\constants\Plugin_Urls;

$cred_helper = new Credentials_Helper();
$cred_helper->handle_error_notices();
?>
<!-- ToDO: 1. Convert table to div. 2. Put complete code under handlebars as currently store-id settings loads 1 second after page load-->
<!-- ToDO: 3. Include JSHint 4. See how can we leverage any of the libraries from Angular,React or VueJS -->

<!--start: confirmation modal for Remove marketplace-->
<div id="aalb-remove-marketplace-confirmation-container">
    <div><?php esc_html_e( "Are you sure you want to remove this marketplace?", 'amazon-associates-link-builder' ) ?></div>
    <br />
    <div>
        <button id="aalb-remove-yes-button" class="aalb-btn aalb-btn-primary"><?php esc_html_e( "YES", 'amazon-associates-link-builder' ) ?></button>
        <button id="aalb-remove-no-button" class="aalb-btn aalb-btn-primary"><?php esc_html_e( "NO", 'amazon-associates-link-builder' ) ?></button>
    </div>
    <input type="hidden" id="aalb-marketplace-to-remove" />
</div> <!--end: confirmation modal for Remove marketplace-->

<div class="wrap aalb-settings-page">
    <h2><?php esc_html_e( "Settings for Associates Link Builder", 'amazon-associates-link-builder' ) ?></h2>
    <br>
    <form id="aalb-credentials-form" method="post" action="options.php">
        <?php settings_fields( Db_Constants::CRED_CONFIG_GROUP );
        do_settings_sections( Db_Constants::CRED_CONFIG_GROUP ); ?>
        <script id="aalb-hbs-store-id-settings" type="text/x-handlebars-template">
            <fieldset class="aalb-settings-fieldset">
                <legend class="aalb-settings-legend"> {{tracking_id_fieldset_label}}</legend>
                <table id="aalb-store-ids-settings" class="widefat aalb-settings-table">
                    <tr>
                        <td colspan="4">
                            {{marketplace_settings_info_message}}
                            <br>
                            {{tracking_id_settings_info_message}}
                        </td>
                    </tr>
                    {{#each store_ids_for_marketplaces}}
                    {{> aalb-marketplace-row-hbs ../marketplace_row_context marketplace=this.marketplace tracking_ids=this.tracking_ids }}
                    {{/each}}
                    <tr class="aalb-add-new-marketplace">
                        <td colspan="4">
                            <a href="#" id="aalb-add-new-marketplace">{{add_a_marketplace_label}}</a>
                        </td>
                    </tr>
                </table>
                <!--Below fields needs to saved on form submission but don't require any user action so hidden-->
                <input type="hidden" id={{old_store_id_db_key}} name={{old_store_id_db_key}}>
                <input type="hidden" id={{new_store_id_db_key}} name={{new_store_id_db_key}}>
                <input type="hidden" id={{default_store_id_db_key}} name={{default_store_id_db_key}}>
                <input type="hidden" id={{default_marketplace_db_key}} name={{default_marketplace_db_key}}>
            </fieldset>
        </script>
        <br>
        <fieldset class="aalb-settings-fieldset">
            <legend class="aalb-settings-legend"><?php esc_html_e( "Site Wide Settings", 'amazon-associates-link-builder' ) ?></legend>
            <table class="widefat fixed aalb-settings-table">
                <tr>
                    <th scope="row" class="aalb-settings-identifier"><?php esc_html_e( "Default Template", 'amazon-associates-link-builder' ) ?></th>
                    <td class="aalb-settings-input-column">
                        <?php $default_template = get_option( Db_Constants::DEFAULT_TEMPLATE, Db_Constants::DEFAULT_TEMPLATE_NAME ); ?>
                        <select name=<?php echo Db_Constants::DEFAULT_TEMPLATE ?> class="aalb-settings-input-field">
                            <?php
                            $templates = get_option( Db_Constants::TEMPLATE_NAMES, $default_template );
                            foreach ( $templates as $template ) {
                                echo '<option value="' . $template . '"';
                                selected( $default_template, $template );
                                echo '>' . $template . '</option>\n';
                            }
                            ?>
                        </select>
                    </td>
                    <td><?php esc_html_e( "The ad template that will be used for rendering the ad if no template is specified in the short code.", 'amazon-associates-link-builder' ) ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="aalb-settings-identifier"><?php esc_html_e( "Downloads Folder", 'amazon-associates-link-builder' ) ?></th>
                    <td class="aalb-settings-input-column">
                        <input type="text" name=<?php echo Db_Constants::CUSTOM_UPLOAD_PATH ?> class="aalb-settings-input-field"
                            value="<?php echo get_option( Db_Constants::CUSTOM_UPLOAD_PATH ) ?>" />
                    </td>
                    <td>
                        <?php printf( __( "This folder will be used to save files downloaded by the plugin (e.g. <a href=%1s target=%2s>The MaxMind IP2Country Database</a>) for local use. Absolute path required. <br><span class=\"aalb-bold\"> Default value:</span> <code>wp_upload_dir()['basedir'] + '%3s'</code>", 'amazon-associates-link-builder' ), Library_Endpoints::GEOLITE_COUNTRY_DB_DETAILS_URL, Plugin_Urls::NEW_PAGE_TARGET, Plugin_Constants::UPLOADS_FOLDER ); ?>
                    </td>
                </tr>
                <tr>
                    <td scope="row" colspan="2" class="aalb-settings-input-column">
                        <input id=<?php echo Db_Constants::NO_REFERRER_DISABLED ?> type="checkbox" name=<?php echo Db_Constants::NO_REFERRER_DISABLED ?> value="true"<?php if ( get_option( Db_Constants::NO_REFERRER_DISABLED ) )
                            echo "checked='checked'"; ?> />
                        <label class="aalb-font-size-110" for="aalb_no_referrer_disabled">
                            <?php /* translators: %s: rel="noreferrer" attribute */
                            printf( esc_html__( "Remove %s for Amazon Affiliate Links from all posts", 'amazon-associates-link-builder' ), "rel=\"noreferrer\"" ); ?></label>
                    </td>
                    <td>
                        <?php /* translators: %s: rel="noreferrer" attribute */
                        printf( esc_html__( "Selecting the checkbox will remove %s from Amazon links on all posts till date. The action is reversible and you can revert by deselecting the checkbox", 'amazon-associates-link-builder' ), "rel=\"noreferrer\"" ); ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <br>

        <fieldset class="aalb-settings-fieldset">
            <legend class="aalb-settings-legend"><?php esc_html_e( "PA-API Credentials", 'amazon-associates-link-builder' ) ?></legend>
            <table class="widefat fixed aalb-settings-table">
                <tr>
                    <th scope="row" class="aalb-settings-identifier"><?php esc_html_e( "Access Key ID", 'amazon-associates-link-builder' ) ?></th>
                    <td class="aalb-settings-input-column">
                        <input type="text" name=<?php echo Db_Constants::AWS_ACCESS_KEY ?> class="aalb-settings-input-field"
                            value="<?php echo esc_attr( openssl_decrypt( base64_decode( get_option( Db_Constants::AWS_ACCESS_KEY ) ), Plugin_Constants::ENCRYPTION_ALGORITHM, Plugin_Constants::ENCRYPTION_KEY, 0, Plugin_Constants::ENCRYPTION_IV ) ); ?>" />
                    </td>
                    <td>
                        <?php /* translators: 1: URL of Getting Started page 2: _blank */
                        printf( __( "Your Access Key ID that you generated after signing up for the Amazon Product Advertising API. If you have not already signed up for the Amazon Product Advertising API, you can do so by following instructions listed <a href=%1s target=%2s>here</a>.", 'amazon-associates-link-builder' ), Plugin_Urls::GETTING_STARTED_URL, Plugin_Urls::NEW_PAGE_TARGET ) ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="aalb-settings-identifier"><?php esc_html_e( "Secret Access Key", 'amazon-associates-link-builder' ) ?></th>
                    <?php $secret_key = get_option( Db_Constants::AWS_SECRET_KEY );
                    if ( $secret_key ) {
                        $secret_key = Plugin_Constants::AWS_SECRET_KEY_MASK;
                    }
                    ?>
                    <td class="aalb-settings-input-column">
                        <input type="password" name=<?php echo Db_Constants::AWS_SECRET_KEY ?>
                        class="aalb-settings-input-field" value="<?php echo esc_attr( $secret_key ); ?>" autocomplete="off" />
                    </td>
                    <td>
                        <?php /* translators: 1: URL of managing PA-API acccount page  2: _blank */
                        printf( __( "A key that is used in conjunction with the Access Key ID to cryptographically sign an API request. To retrieve your Access Key ID or Secret Access Key, go to <a href=%1s target=%2s>Manage Your Account</a>. The plugin uses a default encryption key for encrypting the Secret Key. You can change the key using ENCRYPTION KEY parameter defined in /plugin_config.php.", 'amazon-associates-link-builder' ), Paapi_Constants::MANAGE_US_ACCOUNT_URL, Plugin_Urls::NEW_PAGE_TARGET ) ?>
                    </td>
                </tr>
            </table>
        </fieldset>

        <div class="aalb-terms-conditions">
            <input id="aalb-terms-checkbox" type="checkbox" name="demo-checkbox" value="1" />
            <label for="aalb-terms-checkbox">
                <?php /* translators: 1: URL of Condition of Use page 2: _blank */
                printf( __( "Check here to indicate that you have read and agree to the Amazon Associates Link Builder <a href=%1s target=%2s>Conditions of Use</a>.", 'amazon-associates-link-builder' ), Plugin_Urls::CONDITIONS_OF_USE_URL, Plugin_Urls::NEW_PAGE_TARGET ) ?></label>
        </div>
        <?php $aalb_submit_button_text = esc_html__( "Save Changes", 'amazon-associates-link-builder' );
        submit_button( $aalb_submit_button_text, 'primary', 'submit', true, array( 'disabled' => 'disabled' ) ); ?>
    </form>
</div>