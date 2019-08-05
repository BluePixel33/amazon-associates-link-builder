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

use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;
use AmazonAssociatesLinkBuilder\constants\Plugin_Urls;

include 'admin_ui_common.php'; ?>
<div class="wrap">
    <h2><?php esc_html_e( "Associates Link Builder", 'amazon-associates-link-builder' ); ?></h2>
    <div class="card" style="max-width:100%;">
        <h2><?php esc_html_e( "About Amazon Associates Program", 'amazon-associates-link-builder' ); ?></h2>
        <p>
            <?php /* translators: 1: URL of affiliate website 2: _blank */
            printf( __( "The Amazon Associates Program is one of the original affiliate marketing programs. Available in geographies across the globe, the Amazon Associates Program has been partnering with content creators to help them monetize their passions since 1996. To learn more about the Amazon Associates Program, please click <a href=%1s target=%2s >here</a>.", 'amazon-associates-link-builder' ), Plugin_Urls::AFFILIATE_WEBSITE_URL, Plugin_Urls::NEW_PAGE_TARGET ); ?>
        </p>
        <h2><?php esc_html_e( "About Amazon Associates Link Builder", 'amazon-associates-link-builder' ); ?> </h2>
        <p>
            <?php esc_html_e( "Link Builder is the official free Amazon Associates Program plugin for WordPress. The plugin enables you to search for products in the Amazon catalog, access real-time price and availability information, and easily create links in your posts to products on Amazon.com. You will be able to generate text links, create custom ad units, or take advantage of out-of-the-box widgets that we’ve designed and included with the plugin.", 'amazon-associates-link-builder' ); ?>
        </p>
        <b><?php esc_html_e( "Note", 'amazon-associates-link-builder' ); ?> </b>
        <ul>
            <li>
                <?php /* translators: 1: URL of Condition of Use page 2: _blank */
                printf( __( "You must review and accept the Amazon Associates Link Builder <a href=%1s target=%2s>Conditions of Use.</a>", 'amazon-associates-link-builder' ), Plugin_Urls::CONDITIONS_OF_USE_URL, Plugin_Urls::NEW_PAGE_TARGET ); ?>
            </li>
            <li>
                <?php esc_html_e( "The plugin is currently in beta form. We intend to regularly add new features and enhancements throughout the beta period and beyond, and welcome your feedback and input.", 'amazon-associates-link-builder' ); ?>
            </li>
        </ul>
        <h2><?php esc_html_e( "Getting Started", 'amazon-associates-link-builder' ); ?></h2>
        <h3><?php esc_html_e( "Step 1 - Become an Associate", 'amazon-associates-link-builder' ); ?></h3>
        <p>
            <?php esc_html_e( "To become an Associate, create an Amazon Associates account using URL for your country:", 'amazon-associates-link-builder' ); ?>
        </p>
        <table border="0" cellpadding="10">
            <tr>
                <td><b><?php esc_html_e( "Australia", 'amazon-associates-link-builder' ); ?></b></td>
                <td>
                    <a href="https://affiliate-program.amazon.com.au/" target="_blank">https://affiliate-program.amazon.com.au/</a>
                </td>
            </tr>
            <tr>
                <td><b><?php esc_html_e( "Brazil", 'amazon-associates-link-builder' ); ?></b></td>
                <td>
                    <a href="https://associados.amazon.com.br/" target="_blank">https://associados.amazon.com.br/</a>
                </td>
            </tr>
            <tr>
                <td><b><?php esc_html_e( "Canada", 'amazon-associates-link-builder' ); ?></b></td>
                <td>
                    <a href="https://associates.amazon.ca/" target="_blank">https://associates.amazon.ca/</a>
                </td>
            </tr>
            <tr>
                <td><b><?php esc_html_e( "China", 'amazon-associates-link-builder' ); ?></b></td>
                <td>
                    <a href="https://associates.amazon.cn/" target="_blank">https://associates.amazon.cn/</a>
                </td>
            </tr>
            <tr>
                <td><b><?php esc_html_e( "France", 'amazon-associates-link-builder' ); ?></b></td>
                <td>
                    <a href="http://partenaires.amazon.fr/" target="_blank">http://partenaires.amazon.fr/</a>
                </td>
            </tr>
            <tr>
                <td><b><?php esc_html_e( "Germany", 'amazon-associates-link-builder' ); ?></b></td>
                <td>
                    <a href="https://partnernet.amazon.de/" target="_blank">https://partnernet.amazon.de/</a>
                </td>
            </tr>
            <tr>
                <td><b><?php esc_html_e( "India", 'amazon-associates-link-builder' ); ?></b></td>
                <td>
                    <a href="http://affiliate-program.amazon.in/" target="_blank">http://affiliate-program.amazon.in/</a>
                </td>
            </tr>
            <tr>
                <td><b><?php esc_html_e( "Italy", 'amazon-associates-link-builder' ); ?></b></td>
                <td>
                    <a href="https://programma-affiliazione.amazon.it/" target="_blank">https://programma-affiliazione.amazon.it/</a>
                </td>
            </tr>
            <tr>
                <td><b><?php esc_html_e( "Japan", 'amazon-associates-link-builder' ); ?></b></td>
                <td>
                    <a href="https://affiliate.amazon.co.jp/" target="_blank">https://affiliate.amazon.co.jp/</a>
                </td>
            </tr>
            <tr>
                <td><b><?php esc_html_e( "Mexico", 'amazon-associates-link-builder' ); ?></b></td>
                <td>
                    <a href="https://afiliados.amazon.com.mx/" target="_blank">https://afiliados.amazon.com.mx/</a>
                </td>
            </tr>
            <tr>
                <td><b><?php esc_html_e( "Spain", 'amazon-associates-link-builder' ); ?></b></td>
                <td>
                    <a href="https://afiliados.amazon.es/" target="_blank">https://afiliados.amazon.es/</a>
                </td>
            </tr>
            <tr>
                <td><b><?php esc_html_e( "United Kingdom", 'amazon-associates-link-builder' ); ?></b></td>
                <td>
                    <a href="https://affiliate-program.amazon.co.uk/" target="_blank">https://affiliate-program.amazon.co.uk/</a>
                </td>
            </tr>
            <tr>
                <td><b><?php esc_html_e( "United States", 'amazon-associates-link-builder' ); ?></b></td>
                <td>
                    <a href="https://affiliate-program.amazon.com/" target="_blank">https://affiliate-program.amazon.com/</a>
                </td>
            </tr>
        </table>
        <p>
            <?php esc_html_e( "Your Associate ID works only in the country in which you register. If you’d like to be an Associate in more than one country, please register separately for each country.", 'amazon-associates-link-builder' ); ?>
        </p>
        <h3><?php esc_html_e( "Step 2 - Sign up for the Amazon Product Advertising API", 'amazon-associates-link-builder' ); ?></h3>
        <p>
            <?php /* translators: 1: URL of Getting Started page 2: _blank */
            printf( __( "Sign up for the Amazon Product Advertising API by following the instructions listed <a href=%1s target=%2s>here</a>. The Amazon Product Advertising API is a popular e-commerce service, powering Amazon-integrated experiences around the world, serving tens of thousands of applications and more than 1 billion API requests every day. It exposes a web-service, which allows Associates to programmatically search and look up items in the Amazon product catalog. The Link Builder plugin integrates the Product Advertising API, allowing you to access Amazon.com product catalog data without requiring additional software development.", 'amazon-associates-link-builder' ), Plugin_Urls::GETTING_STARTED_URL, Plugin_Urls::NEW_PAGE_TARGET ); ?>
        </p>
        <h3><?php esc_html_e( "Step 3 - Configure plugin for first use", 'amazon-associates-link-builder' ); ?></h3>
        <p>
            <?php esc_html_e( "Use the Associates Link Builder->Settings screen to configure the plugin.", 'amazon-associates-link-builder' ); ?>
        </p>
        <ol>
            <li><?php esc_html_e( "Set Access Key ID and Secret Access Key in the Settings section. These credentials are used to invoke requests to the Amazon Product Advertising API for fetching information on an item.", 'amazon-associates-link-builder' ); ?>
            </li>
            <li><?php esc_html_e( "Set default Associate ID. Associate ID is used to monitor traffic and sales from your links to Amazon. You can also define a list of valid Associate IDs (store ids or tracking ids). You should create a new tracking ID in your Amazon Associates account for using it as Associate ID in the plugin.", 'amazon-associates-link-builder' ); ?>
            </li>
            <li><?php esc_html_e( "Set the default Amazon marketplace based on the Amazon Associates Program for which you are registered (for example, if you’ve signed up for the Amazon Associates Program in UK, then your default marketplace selection should be UK) and select an appropriate template for rendering your ads.", 'amazon-associates-link-builder' ); ?>
            </li>
        </ol>
        <p>
            <?php esc_html_e( "That's it! You’re all set to start adding Amazon affiliate links to your posts using the Amazon Associates Link Builder plugin!", 'amazon-associates-link-builder' ); ?>
        </p>
        <h2><?php esc_html_e( "User Guide", 'amazon-associates-link-builder' ); ?></h2>
        <p>
            <?php /* translators: 1: URL of Link Builder User Guide 2: _blank */
            printf( __( "Review <a href=%1s target=%2s >Link Builder User Guide</a> for more information on getting started and key features of the plugin.", 'amazon-associates-link-builder' ), Plugin_Urls::USER_GUIDE_URL, Plugin_Urls::NEW_PAGE_TARGET ); ?>
        </p>
        <h2><?php esc_html_e( "Support", 'amazon-associates-link-builder' ); ?></h2>
        <p>
            <?php /* translators: 1: URL of Plugin's Support Forum 2: _blank */
            printf( __( "If you get stuck, or have any questions, you can ask for help in the <a href=%1s target=%2s>Amazon Associates Link Builder plugin forum</a>.", 'amazon-associates-link-builder' ), Plugin_Urls::SUPPORT_FORUM_URL, Plugin_Urls::NEW_PAGE_TARGET ); ?>
        </p>
    </div>
</div>