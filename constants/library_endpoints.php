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

namespace AmazonAssociatesLinkBuilder\constants;

/**
 * Class for Holding  Endpoints for third-party libraries hosted at our CDN or third-party URLs.
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/constants
 */

class Library_Endpoints {
    //External Scripts
    const HANDLEBARS_JS = 'https://d8fd03967nrad.cloudfront.net/libs/handlebars.js/4.0.5/handlebars.min.js';
    const CODEMIRROR_JS = 'https://d8fd03967nrad.cloudfront.net/libs/codemirror/5.13.2/codemirror.min.js';
    const CODEMIRROR_MODE_XML_JS = 'https://d8fd03967nrad.cloudfront.net/libs/codemirror/5.13.2/mode/xml/xml.min.js';
    const CODEMIRROR_MODE_CSS_JS = 'https://d8fd03967nrad.cloudfront.net/libs/codemirror/5.13.2/mode/css/css.min.js';

    //External Styles
    const CODEMIRROR_CSS = 'https://d8fd03967nrad.cloudfront.net/libs/codemirror/5.13.2/codemirror.min.css';
    const FONT_AWESOME_CSS = 'https://d8fd03967nrad.cloudfront.net/libs/font-awesome/4.5.0/css/font-awesome.min.css';
    const JQUERY_UI_CSS = 'https://d8fd03967nrad.cloudfront.net/libs/jQueryUI/1.12.1/themes/ui-lightness/jquery-ui.css';

    //Maxmind GeoLite2Country DB Download URL
    const GEOLITE_COUNTRY_DB_DOWNLOAD_URL = 'https://d8fd03967nrad.cloudfront.net/libs/geoip/database/GeoLite2-Country.mmdb.gz';
    const GEOLITE_DB_DOWNLOAD_URL_FROM_MAXMIND_SERVER = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.mmdb.gz';
    const GEOLITE_COUNTRY_DB_DETAILS_URL = 'https://dev.maxmind.com/geoip/geoip2/geolite2/';

    //Tracking API Endpoint
    const TRACKING_API_ENDPOINT = 'https://rx5hfxbp45.execute-api.us-east-1.amazonaws.com/aalb/';
    const TRACKING_API_SOURCE_TOOL_QUERY_PARAM = 'source-tool=aalb';
    const TRACKING_API_ACCESS_KEY_QUERY_PARAM = 'aws-access-key-id=';
}
