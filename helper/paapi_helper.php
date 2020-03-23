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
namespace AmazonAssociatesLinkBuilder\helper;

use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;
use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\constants\Paapi_Constants;
use AmazonAssociatesLinkBuilder\constants\HTTP_Constants;
use AmazonAssociatesLinkBuilder\constants\Plugin_Urls;
use AmazonAssociatesLinkBuilder\configuration\Config_Loader;

/**
 * Helper class for Paapi
 *
 * @since      1.0.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/lib/php/Paapi
 */
class Paapi_Helper {

    private $config_loader;

    public function __construct() {
        $this->config_loader = new Config_Loader();
    }

    /**
     * PA-API error messages to display in case of request errors
     *
     * @param string $error code Error code of the request.
     *
     * @return string PA-API error message.
     */
    function get_error_message( $error ) {
        switch ( $error ) {
            case HTTP_Constants::BAD_REQUEST:
                /* translators: 1: URL of Associate sign-up page  2: _blank  3:URL of adding secondary user page  4: _blank */
                return '<h4>' . sprintf( __( "Your AWS Access Key Id is not registered as an Amazon Associate. Please verify that you are <a href=%1s target=%2s>registered as an Amazon Associate</a> in respective locale and you added the email address registered for the Product Advertising API as a <a href=%3s target=%4s>secondary email address in your Amazon Associates account</a>.", 'amazon-associates-link-builder' ), Plugin_Urls::ASSOCIATE_SIGN_UP_URL, Plugin_Urls::NEW_PAGE_TARGET, Plugin_Urls::ADDING_SECONDARY_USER_AC_URL, Plugin_Urls::NEW_PAGE_TARGET ) . '</h4>';
            case HTTP_Constants::FORBIDDEN:
                /* translators: 1: URL of PA-API sign-up page  2: _blank */
                return '<h4>' . sprintf( __( "Your AccessKey Id is not registered for Product Advertising API. Please sign up for Product Advertising API by <a href=%1s target=%2s>following these guidelines</a>.", 'amazon-associates-link-builder' ), Paapi_Constants::SIGN_UP_URL, Plugin_Urls::NEW_PAGE_TARGET ) . '</h4>';
            case HTTP_Constants::REQUEST_URI_TOO_LONG:
                return '<h4>' . sprintf( __( "Your AccessKey Id is not registered for Product Advertising API. Please sign up for Product Advertising API by <a href=%1s target=%2s>following these guidelines</a>.", 'amazon-associates-link-builder' ), Paapi_Constants::SIGN_UP_URL, Plugin_Urls::NEW_PAGE_TARGET ) . '</h4>';
            case HTTP_Constants::INTERNAL_SERVER_ERROR:
                return '<h4>' . esc_html__( "Internal server error", 'amazon-associates-link-builder' ) . '</h4>';
            case HTTP_Constants::THROTTLE:
                /* translators: 1: URL of PA-API efficiency guidelines page  2: _blank */
                return '<h4>' . sprintf( __( "You are submitting requests too quickly. Please retry your requests at a slower rate. For more information, see <a href=%1s target=%2s>Efficiency Guidelines</a>.", 'amazon-associates-link-builder' ), Paapi_Constants::EFFICIENCY_GUIDELINES_URL, Plugin_Urls::NEW_PAGE_TARGET ) . '</h4>';
            case HTTP_Constants::TIME_OUT:
                /* translators: %s: Email-id of the support */
                return '<h4>' . sprintf( __( "Request timed out. Try again after some time. Please check you network and firewall settings. If the error still persists, write to us at %s.", 'amazon-associates-link-builder' ), Plugin_Constants::SUPPORT_EMAIL_ID ) . '</h4>';
            default:
                /**
                 * <h4> tag ensures that the message is treated as HTML element in jQuery.find in aalb_admin.js.
                 * Otherwise due to the error message string's characters like "!,:,etc", string is parsed as
                 * if it contains partial css classes and later given syntax error
                 */
                return '<h4>' . $error . '</h4>';
        }
    }

    /**
     * Get marketplace endpoint for marketplace abbreviation
     *
     * @since 1.8.0
     *
     * @param string $marketplace_abbr Marketplace Abbreviation from shortcode
     *
     * @return string $marketplace_endpoint Marketplace endpoint
     */
    private function get_marketplace_endpoint( $marketplace_abbr ) {
        $aalb_marketplace_names = $this->config_loader->fetch_marketplaces();
        $marketplace_endpoint = array_search( $marketplace_abbr, $aalb_marketplace_names );

        return $marketplace_endpoint;
    }

    /**
     * Returns default store_id for given marketplace.
     *
     * @param $marketplace
     *
     * @return string store_id for given marketplace
     */
    public function get_store_id_for_marketplace( $marketplace ) {
        try{
            $store_ids_list = json_decode( get_option( Db_Constants::STORE_IDS ));
            $store_ids = $store_ids_list->{$marketplace};
            $store_id = $store_ids[0];
        } catch( \Exception $e ){
            error_log("No store_id found for marketplace {$marketplace}");
            $store_id = get_option( Db_Constants::DEFAULT_STORE_ID );
        }
        return $store_id;
    }

    /**
     * Returns the item lookup response for the requested asins
     *
     * @param string $asins_array     Array of asins.
     * @param string $marketplace_url Marketplace to search the products.
     * @param string $store_id        Associate tag.
     *
     * @return string Response for item lookup.
     */
    public function get_item_lookup_response( $asins_array, $marketplace_url, $store_id ) {
        $access_key_id = openssl_decrypt( base64_decode( get_option( Db_Constants::AWS_ACCESS_KEY ) ), Plugin_Constants::ENCRYPTION_ALGORITHM, Plugin_Constants::ENCRYPTION_KEY, 0, Plugin_Constants::ENCRYPTION_IV );
        $secret_key = openssl_decrypt( base64_decode( get_option( Db_Constants::AWS_SECRET_KEY ) ), Plugin_Constants::ENCRYPTION_ALGORITHM, Plugin_Constants::ENCRYPTION_KEY, 0, Plugin_Constants::ENCRYPTION_IV );
        $marketplace = strtolower( end( explode( ".", $marketplace_url ) ) );
        //usleep(1000000);
        $getItemRequest = new GetItemsRequest();
        $getItemRequest->PartnerType = "Associates";
        $getItemRequest->PartnerTag = $store_id;
        $getItemRequest->Marketplace = "www.amazon.$marketplace";
        $getItemRequest->ItemIds = $asins_array;
        $getItemRequest->Resources = ["Images.Primary.Small","Images.Primary.Medium","Images.Primary.Large","ItemInfo.ProductInfo","ItemInfo.Title","Offers.Listings.Availability.Message","Offers.Listings.Availability.Type","Offers.Listings.Condition","Offers.Listings.DeliveryInfo.IsAmazonFulfilled","Offers.Listings.DeliveryInfo.IsFreeShippingEligible","Offers.Listings.DeliveryInfo.IsPrimeEligible","Offers.Listings.DeliveryInfo.ShippingCharges","Offers.Listings.MerchantInfo","Offers.Listings.Price","Offers.Listings.ProgramEligibility.IsPrimeExclusive","Offers.Listings.ProgramEligibility.IsPrimePantry","Offers.Listings.Promotions","Offers.Listings.SavingBasis","Offers.Summaries.HighestPrice","Offers.Summaries.LowestPrice","Offers.Summaries.OfferCount"];
        $getItemRequest->Merchant = "All";
        $host = "webservices.amazon.$marketplace";
        $path = "/paapi5/getitems";
        $payload = json_encode( $getItemRequest );
        $awsv4 = new AwsV4( $access_key_id, $secret_key );
        $awsv4->setRegionName( "eu-west-1" );
        $awsv4->setServiceName( "ProductAdvertisingAPI" );
        $awsv4->setPath( $path );
        $awsv4->setPayload( $payload );
        $awsv4->setRequestMethod( "POST" );
        $awsv4->addHeader( 'content-encoding', 'amz-1.0' );
        $awsv4->addHeader( 'content-type', 'application/json; charset=utf-8' );
        $awsv4->addHeader( 'host', $host );
        $awsv4->addHeader( 'x-amz-target', 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.GetItems' );
        $headers = $awsv4->getHeaders();
        return wp_remote_post( Paapi_Constants::TRANSFER_PROTOCOL . $host . $path, array(
            'body' => $payload,
            'timeout' => Paapi_Constants::REQUEST_TIMEOUT,
            'headers' => $headers
        ) );
    }

    /**
     * Returns the item search response for the requested keywords
     *
     * @param string $keywords        Search keywords of the products.
     * @param string $marketplace_url Marketplace to search the products.
     * @param string $store_id        Associate tag.
     *
     * @return string Response for item search.
     */
    public function get_item_search_response( $keywords, $marketplace_url, $store_id ) {
        $access_key_id = openssl_decrypt( base64_decode( get_option( Db_Constants::AWS_ACCESS_KEY ) ), Plugin_Constants::ENCRYPTION_ALGORITHM, Plugin_Constants::ENCRYPTION_KEY, 0, Plugin_Constants::ENCRYPTION_IV );
        $secret_key = openssl_decrypt( base64_decode( get_option( Db_Constants::AWS_SECRET_KEY ) ), Plugin_Constants::ENCRYPTION_ALGORITHM, Plugin_Constants::ENCRYPTION_KEY, 0, Plugin_Constants::ENCRYPTION_IV );
        $marketplace = strtolower( end( explode( ".", $marketplace_url ) ) );
        //usleep(1000000);
        $searchItemRequest = new SearchItemsRequest();
        $searchItemRequest->PartnerType = "Associates";
        $searchItemRequest->PartnerTag = $store_id;
        $searchItemRequest->Marketplace = "www.amazon.$marketplace";
        $searchItemRequest->Keywords = $keywords;
        $searchItemRequest->SearchIndex = "All";
        $searchItemRequest->Resources = ["Images.Primary.Small","Images.Primary.Medium","Images.Primary.Large","ItemInfo.ProductInfo","ItemInfo.Title","Offers.Listings.Availability.Message","Offers.Listings.Availability.Type","Offers.Listings.Condition","Offers.Listings.DeliveryInfo.IsAmazonFulfilled","Offers.Listings.DeliveryInfo.IsFreeShippingEligible","Offers.Listings.DeliveryInfo.IsPrimeEligible","Offers.Listings.DeliveryInfo.ShippingCharges","Offers.Listings.MerchantInfo","Offers.Listings.Price","Offers.Listings.ProgramEligibility.IsPrimeExclusive","Offers.Listings.ProgramEligibility.IsPrimePantry","Offers.Listings.Promotions","Offers.Listings.SavingBasis","Offers.Summaries.HighestPrice","Offers.Summaries.LowestPrice","Offers.Summaries.OfferCount"];
        $searchItemRequest->Merchant = "All";
        $host = "webservices.amazon.$marketplace";
        $path = "/paapi5/searchitems";
        $payload = json_encode( $searchItemRequest );
        $awsv4 = new AwsV4( $access_key_id, $secret_key );
        $awsv4->setRegionName( "eu-west-1" );
        $awsv4->setServiceName( "ProductAdvertisingAPI" );
        $awsv4->setPath( $path );
        $awsv4->setPayload( $payload );
        $awsv4->setRequestMethod( "POST" );
        $awsv4->addHeader( 'content-encoding', 'amz-1.0' );
        $awsv4->addHeader( 'content-type', 'application/json; charset=utf-8' );
        $awsv4->addHeader( 'host', $host );
        $awsv4->addHeader( 'x-amz-target', 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.SearchItems' );
        $headers = $awsv4->getHeaders();
        return wp_remote_post( Paapi_Constants::TRANSFER_PROTOCOL . $host . $path, array(
            'body' => $payload,
            'timeout' => Paapi_Constants::REQUEST_TIMEOUT,
            'headers' => $headers
        ) );
    }
    
}

class SearchItemsRequest {
    public $PartnerType;
    public $PartnerTag;
    public $Marketplace;
    public $Keywords;
    public $SearchIndex;
    public $Resources;
    public $Merchant;
}

class GetItemsRequest {
    public $PartnerType;
    public $PartnerTag;
    public $Marketplace;
    public $ItemIds;
    public $Resources;
    public $Merchant;
}

class AwsV4 {
    private $accessKeyID = null;
    private $secretAccessKey = null;
    private $path = null;
    private $regionName = null;
    private $serviceName = null;
    private $httpMethodName = null;
    private $queryParametes = array();
    private $awsHeaders = array();
    private $payload = "";

    private $HMACAlgorithm = "AWS4-HMAC-SHA256";
    private $aws4Request = "aws4_request";
    private $strSignedHeader = null;
    private $xAmzDate = null;
    private $currentDate = null;

    public function __construct($accessKeyID, $secretAccessKey) {
        $this->accessKeyID = $accessKeyID;
        $this->secretAccessKey = $secretAccessKey;
        $this->xAmzDate = $this->getTimeStamp();
        $this->currentDate = $this->getDate();
    }

    function setPath($path) {
        $this->path = $path;
    }

    function setServiceName($serviceName) {
        $this->serviceName = $serviceName;
    }

    function setRegionName($regionName) {
        $this->regionName = $regionName;
    }

    function setPayload($payload) {
        $this->payload = $payload;
    }

    function setRequestMethod($method) {
        $this->httpMethodName = $method;
    }

    function addHeader($headerName, $headerValue) {
        $this->awsHeaders [$headerName] = $headerValue;
    }

    private function prepareCanonicalRequest() {
        $canonicalURL = "";
        $canonicalURL .= $this->httpMethodName . "\n";
        $canonicalURL .= $this->path . "\n" . "\n";
        $signedHeaders = '';
        foreach ( $this->awsHeaders as $key => $value ) {
            $signedHeaders .= $key . ";";
            $canonicalURL .= $key . ":" . $value . "\n";
        }
        $canonicalURL .= "\n";
        $this->strSignedHeader = substr( $signedHeaders, 0, - 1 );
        $canonicalURL .= $this->strSignedHeader . "\n";
        $canonicalURL .= $this->generateHex( $this->payload );
        return $canonicalURL;
    }

    private function prepareStringToSign($canonicalURL) {
        $stringToSign = '';
        $stringToSign .= $this->HMACAlgorithm . "\n";
        $stringToSign .= $this->xAmzDate . "\n";
        $stringToSign .= $this->currentDate . "/" . $this->regionName . "/" . $this->serviceName . "/" . $this->aws4Request . "\n";
        $stringToSign .= $this->generateHex( $canonicalURL );
        return $stringToSign;
    }

    private function calculateSignature($stringToSign) {
        $signatureKey = $this->getSignatureKey( $this->secretAccessKey, $this->currentDate, $this->regionName, $this->serviceName );
        $signature = hash_hmac( "sha256", $stringToSign, $signatureKey, true );
        $strHexSignature = strtolower( bin2hex( $signature ) );
        return $strHexSignature;
    }

    public function getHeaders() {
        $this->awsHeaders ['x-amz-date'] = $this->xAmzDate;
        ksort( $this->awsHeaders );
        $canonicalURL = $this->prepareCanonicalRequest();
        $stringToSign = $this->prepareStringToSign( $canonicalURL );
        $signature = $this->calculateSignature( $stringToSign );
        if ($signature) {
            $this->awsHeaders ['Authorization'] = $this->buildAuthorizationString( $signature );
            return $this->awsHeaders;
        }
    }

    private function buildAuthorizationString($strSignature) {
        return $this->HMACAlgorithm . " " . "Credential=" . $this->accessKeyID . "/" . $this->getDate() . "/" . $this->regionName . "/" . $this->serviceName . "/" . $this->aws4Request . "," . "SignedHeaders=" . $this->strSignedHeader . "," . "Signature=" . $strSignature;
    }

    private function generateHex($data) {
        return strtolower( bin2hex( hash( "sha256", $data, true ) ) );
    }

    private function getSignatureKey($key, $date, $regionName, $serviceName) {
        $kSecret = "AWS4" . $key;
        $kDate = hash_hmac( "sha256", $date, $kSecret, true );
        $kRegion = hash_hmac( "sha256", $regionName, $kDate, true );
        $kService = hash_hmac( "sha256", $serviceName, $kRegion, true );
        $kSigning = hash_hmac( "sha256", $this->aws4Request, $kService, true );

        return $kSigning;
    }

    private function getTimeStamp() {
        return gmdate( "Ymd\THis\Z" );
    }

    private function getDate() {
        return gmdate( "Ymd" );
    }
}

?>