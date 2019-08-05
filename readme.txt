=== Amazon Associates Link Builder ===
Contributors: amazonlinkbuilder
Tags: Amazon, Affiliate, Associates, Amazon Associates, Amazon Associate, Product Advertising API, Amazon API, Amazon Link, Amazon Ad, Amazon Affiliate, eCommerce
Requires at least: 3.0.1
Tested up to: 5.0.3
Stable tag: 1.9.3
Requires PHP: 5.4.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The official plugin from the Amazon Associates Program.

== Description ==
= About Amazon Associates Program =
The Amazon Associates Program is one of the original affiliate marketing programs. Available in geographies across the globe, the Amazon Associates Program has been partnering with content creators to help them monetize their passions since 1996. To learn more about the Amazon Associates Program, please click [here](https://affiliate-program.amazon.com/).

= About Amazon Associates Link Builder =
Link Builder is the official free Amazon Associates Program plugin for WordPress. The plugin enables you to search for products in the Amazon catalog, access real-time price and availability information, and easily create links in your posts to products on Amazon.com. You will be able to generate text links, create custom ad units, or take advantage of out-of-the-box widgets that we've designed and included with the plugin.

= Note =
* You must review and accept the Amazon Associates Link Builder [Conditions of Use](https://s3.amazonaws.com/aalb-public-resources/documents/AssociatesLinkBuilder-ConditionsOfUse-2017-01-17.pdf).
* The plugin is currently in beta form. We intend to regularly add new features and enhancements throughout the beta period and beyond, and welcome your feedback and input.

== Installation ==

= Pre-requisites =
__Requires PHP Version:__ 5.4.0 or higher
<br />

__Requires WordPress Version:__ 3.0.1 or higher
<br />

__Become an Associate__ <br />
To become an Associate, create an Amazon Associates account using URL for your country: [Brazil](https://associados.amazon.com.br/), [Canada](https://associates.amazon.ca/), [China](https://associates.amazon.cn/), [France](https://partenaires.amazon.fr/), [Germany](http://partnernet.amazon.de/), [India](https://affiliate-program.amazon.in/), [Italy](https://programma-affiliazione.amazon.it/), [Japan](https://affiliate.amazon.co.jp/), [Mexico](https://afiliados.amazon.com.mx/), [Spain](https://afiliados.amazon.es/), [United Kingdom](https://affiliate-program.amazon.co.uk/), [United States](http://affiliate-program.amazon.com/)
<br />
Your Associate ID works only in the country in which you register. If you'd like to be an Associate in more than one country, please register separately for each country.
<br />

__Sign up for Product Advertising API__ <br />
Sign up for the Amazon Product Advertising API by following the instructions listed [here](http://docs.aws.amazon.com/AWSECommerceService/latest/DG/CHAP_GettingStarted.html). The Amazon Product Advertising API is a popular e-commerce service, powering Amazon-integrated experiences around the world, serving tens of thousands of applications and more than 1 billion API requests every day. It exposes a web-service, which allows Associates to programmatically search and look up items in the Amazon product catalog. The Link Builder plugin integrates the Product Advertising API, allowing you to access Amazon.com product catalog data without requiring additional software development.

= Installing =
To install the Amazon Associates Link Builder plugin: <br />
1. Log in to your WordPress dashboard, navigate to the Plugins menu and click Add New. <br />
2. In the search field type **Amazon Associates Link Builder** and click Search Plugins. You can install it by simply clicking **Install Now**.

= Updating =
Automatic updates should work like a charm. That said, it's always good practice to back up your templates just in case.

= User Guide =
You can review the **About** section under the Associates Link Builder menu bar or [Link Builder User Guide](https://s3.amazonaws.com/aalb-public-resources/documents/AssociatesLinkBuilder-UserGuide.pdf) for more information on getting started and key features of the plugin.

= Configure plugin for first use =
Use the Associates Link Builder->Settings screen to configure the plugin. <br />
1. Set Access Key ID and Secret Access Key in the Settings section. These credentials are used to invoke requests to the Amazon Product Advertising API for fetching information on an item. <br />
2. Set default Associate ID. Associate ID is used to monitor traffic and sales from your links to Amazon. You can also define a list of valid Associate IDs (store ids or tracking ids). You should create a new tracking ID in your Amazon Associates account for using it as Associate ID in the plugin. <br />
3. Set the default Amazon marketplace based on the Amazon Associates Program for which you are registered (for example, if you've signed up for the Amazon Associates Program in UK, then your default marketplace selection should be UK) and select an appropriate template for rendering your ads.

<br />
That's it! You're all set to start adding Amazon affiliate links to your posts using the Amazon Associates Link Builder plugin!

= Support =
If you get stuck, or have any questions, you can ask for help in the [Amazon Associates Link Builder plugin forum](https://wordpress.org/support/plugin/amazon-link-builder).

== Screenshots ==
1. Amazon Associates Link Builder settings console
2. Search for products in Amazon catalog directly from the WordPress toolbar while creating a new post or a page
3. Select the products you would like to advertise
4. Add products from multiple marketplaces for geo targeting feature
5. Product Carousel Template: Stylishly designed and responsive ad unit that automatically adapts for different device types and screen resolutions that can be placed within or at the end of your content
6. Product Ad Template: A variation of the product carousel template for advertising one product at a time
7. Product Grid Template: Another variation of the product carousel template that can be used to display a grid of products alongside your content
8. Price Link Template: Dynamic hyperlink containing the current price offered for buying the item on Amazon
9. Create custom ad templates native to your site' styling
10. Gutenberg editor interface
11. Amazon Associates Link Builder block under Widgets category
12. Amazon Associates Link Builder search block
13. Search for products in Amazon catalog from the Gutenberg block while creating a new post or a page
14. Select the products you would like to advertise
15. (Optional) Mouse over on change block type
16. (Optional) Transform to Shortcode
17. (Optional) Transformed to Shortcode

== Frequently Asked Questions ==

= Where can I find more information on the Amazon Associates Program? =
You can find more information on the Amazon Associates Program at [Amazon Associates Help](https://affiliate-program.amazon.com/help/node).

= How does the plugin work? =
The plugin makes real-time calls to the Amazon Product Advertising API to search for, and look up information on, items in Amazon.com's product catalog. Security credentials provided in the Settings console are used for signing API requests. The plugin also contains an in-build caching mechanism to cache the API results for optimizing the API usage and reducing the loading time of the link content.

= Can I track the performance of the ad units created using the plugin? =
Yes. You can track the performance of the ad unit by placement or section of your website by using tracking ids. You are recommended to use different tracking ids for different ad templates. This will allow you to track orders and earnings information for each type of ad using the reports section in Associates Central. You can create tracking ids [here](https://affiliate-program.amazon.com/home/account/tag/manage).

= What information is shared with Amazon? =
Information we learn from Amazon Associates Link Builder users helps us to evaluate performance of the plugin, troubleshoot technical issues, and generally improve the plugin. We only capture information on impressions, clicks, and sales for Amazon affiliate links in accordance with the [Amazon.com Privacy Notice](https://www.amazon.com/gp/help/customer/display.html?nodeId=468496).

= Where can I find support? =
You can review the **About** section under the Associates Link Builder menu bar or [Link Builder User Guide](https://s3.amazonaws.com/aalb-public-resources/documents/AssociatesLinkBuilder-UserGuide.pdf) for more information on getting started and key features of the plugin. If you get stuck, or have any questions, you can ask for help in the [Amazon Associates Link Builder Plugin Forum](https://wordpress.org/support/plugin/amazon-associates-link-builder).

= How can I add links to different Amazon sites in my blog? =
You can search products using keywords in any supported country, but you have to join the Associates Program in those countries separately to be able to do this. For example - If you are a blogger in UK interested in generating links to Amazon.com (US) site, then you will first need to join the Amazon Associates Program in the US to be able to search for products on Amazon.com site. Now, you can add links for products from both US and UK in a single shortcode that will render ad depending upon visitor's country under geo-targeted links feature.

= How can I use this plugin to remove rel="noreferrer" from affiliate links in my site? =
You can remove rel="noreferrer" from links to Amazon sites in all posts by selecting the appropriate checkbox in the settings page. This will not remove rel="noopener", if present. This feature only affects links to Amazon sites. This change is reversible and as soon as you deselect the checkbox on settings page, the pages will be restored to the original content.

= Are Amazon Product Advertising API credentials required to use the feature to remove rel="noreferrer" for Amazon Affiliate Links from all posts? =
Amazon Product Advertising API credentials are not required to use the feature to remove rel="noreferrer" for Amazon Affiliate Links from all posts.

= What is geo-targeted links feature? =
This feature enables Amazon Associates to create content customized for and better monetize their international traffic.

= How will the geo-targeting feature benefit me? =
The geo-targeting feature lets you monetize any traffic from other countries that was harder to monetize earlier. In accordance with our Operating Agreement, attribution to amazon links placed on your site does not hold if the end user has to switch to a different country specific website to complete his/her purchase. Similarly, if you send users to the correct country website, but with an incorrect country’s tracking id (not valid in that locale), then you will not get paid the affiliate fees for that sale. Many associates use complex home-grown systems, or unreliable 3rd party systems to overcome these limitations. These systems might have trust, reliability and pricing implications on your business. The geo-targeting feature in the WordPress plugin aims to reduce this complexity and give you more control on your monetization of global traffic.

= How do I use the geo-targeting feature in the plugin? =
As a part of the global settings in the plugin, you can configure which countries you want to work with, and the respective tracking id(s). You will also need to provide a set of PA-API credentials, which are connected to each of your respective country associate accounts. You will only need to do this once. As you create content, you will see a tabbed pane which lets you select product sets for each country that you have configured in the plugin. You should pick your default country as the first product set configured. The corresponding short code that is generated will have all the information required to render ads appropriately. When a user visits your site, we use the user’s location to determine which of the product sets to display to the user, and the corresponding ads are rendered. If the user is from a location that you haven’t configured, then your default product set is displayed to the user.

= What will happen if the customer is coming from a country for which I have not configured the shortcode in geo-targeting feature? =
The customer will be shown ads from the first country added in shortcode.

= How can I see my reporting for geo-targeting feature? =
You can see your earnings and other reports from the respective country Associates Portal under "Amazon Associates Link Builder" section.

== Changelog ==

= 1.9.3 - August 05, 2019 =
* Fix : Malformed URL for tracking IDs starting with numbers in v1.9.2.

= 1.9.2 - February 08, 2019 =
* Fix : Clicks not appearing for v1.9.1.

= 1.9.1 - February 06, 2019 =
* Fix : v1.9.0 not running for few associates.

= 1.9.0 - January 29, 2019 =
* Added Product Advertising API (PA-API) response caching feature. This feature will prevent PA-API throttle by caching PA-API response and updating the cache asynchronously.
* Added support for Gutenberg editor.

= 1.8.0 - July 25, 2018 =
* Added support for Australia (AU) marketplace.
* Fix: SearchBox not disabled in case no tracking-ids have been entered.
* Fix: Plugin is not deactivated gracefully for PHP 5.2.
* Fix: Wrong error message shown when marketplaces lists is not returned by PA-API.

= 1.7.0 - April 2, 2018 =
* Added support for wider character set sent by Product Advertising API(PA-API). More details about change on PA-API can be found [here](https://forums.aws.amazon.com/ann.jspa?annID=5543).

= 1.6.0 - March 14, 2018 =
* Added Impressions data in reports shown on respective associates website. This will work only for ads rendered after upgrade to this release.

= 1.5.4 - January 26, 2018 =
* Fix: In case of failed call to Product-Advertising API, Fatal error thrown.

= 1.5.3 - January 24, 2018 =
* Fix: Plugin failed with a FATAL error on activation/update for users on PHP version below 5.4.0, instead of the expected graceful de-activation.
* Fix: Search button not working for few associates.
* Fix: Loading CSS & JS of jquery-ui only on pages required by plugin.
* Fix: Multi-tab not working for few associates.
* Fix: `Error in maxmind_db_manager:get_db:::WP_ERROR: Forbidden` in logs that prevents IP2country database downloads.

= 1.5.2 - December 2, 2017 =
* Fix: v1.5.1 not running for few associates.

= 1.5.1 - December 1, 2017 =
* Fix: v1.5.0 not running for few associates.
* Minimum supported PHP version changed to 5.4.0 from 5.3.0.

= 1.5.0 - November 29, 2017 =
* Geo-targeted links that enables Amazon Associates to create content customized for and better monetize their international traffic. More details [here](https://amazon-affiliate.eu/en/about-the-programme/amazon-associates-link-builder/).
* Fix: amazon_link shortcode will also work with ProductLink template if text attribute is added separately.

= 1.4.13 - November 10, 2017 =
* Fix: Tracking Ids section is not visible on settings page for few associates.
* Fix: Save Changes button on settings page remains disabled for few associates.

= 1.4.12 - October 31, 2017 =
* **Revamped Settings Page:** Tracking IDs are now associated with Marketplaces in the new settings page. If you are upgrading from a previous version of the plugin, all existing tracking ids have been associated with your default marketplace. Please review and configure available marketplaces and tracking ids from the revamped settings page.
* Add shortcode popup now respects available marketplaces and tracking id associations configured in the revamped settings page.

= 1.4.11 - October 16, 2017 =
* Plugin is now available in Spanish, French and Italian languages.

= 1.4.10 - September 13, 2017 =
* Fix: Customize theme option is not working for few users after v1.4.8.
* Fix: Page alignment distorted for some users after v1.4.9.

= 1.4.9 - September 4, 2017 =
* Putting strings under translation function for Internationalization.
* Fix: Product Grid not aligned properly on iOS devices.
* Fix: Product Ad breaks the text on the right side on few mobile devices.

= 1.4.8 - August 10, 2017 =
* Improvement in error display message on product search.
* Fix: HandleBar template not being loaded for a few users on product search.


= 1.4.7 - July 27, 2017 =
* Fix: `Warning: DOMDocument::loadHTML(): Empty string supplied as input.`
* Fix: Ad would not be rendered even if one ASIN has expired for any multi-ASIN shortcode.
* Fix: Trailing and leading spaces in ProductLink & PriceLink Templates. This fix applies to the default templates only, and you may have to re-create any custom template based off the ProductLink and PriceLink templates to benefit from this change.
* Fix: No Timeout for Product Search.

= 1.4.6 - June 21, 2017 =
* Fix: Amazon logo appears too big in some editors.
* Fix: Plugin doesn't work in some editors.
* Fix: Custom template creation error message not correct.

= 1.4.5 - June 13, 2017 =
* Prime branding changes.
* UI Change: Settings page revamped.
* Remove rel="noreferrer" for Amazon Affiliate Links from all posts by clicking the checkbox in settings.
* Fix: Naming conflict in function name

= 1.4.4 - May 25, 2017 =
* Default Templates provided by the plugin will now render correctly on mobile devices. Your existing posts with default templates will reflect these changes after update. This does not affect custom templates and they will continue to render as before.
* UI Change: Template, Country and Associate ID select boxes are now at the top of the 'Add Shortcode' dialog box to better reflect the order of operations.
* PriceLink, ProductAd & ProductLink templates will now allow only a single product to be added. This does not affect existing posts. To add more than one product to the single product templates, create a custom template by cloning the original one.
* A shortcode can now contain only one instance of a specific product. This does not affect existing posts.
* Fix: After plugin update, changes not being reflected due to browser caching.

= 1.4.3 - April 19, 2017 =
* Fix: Add shortcode button not clickable for some users after version 1.4.2.

= 1.4.2 - March 30, 2017 =
* Changes to ensure minimum PHP version requirements are met before plugin activation.
* Fix: Amazon search text box and button not showing for editors like Site Origin Page Builder and Advanced Custom Fields.

= 1.4.1 - February 23, 2017 =
* Updated error messages to provide more detailed information on how to troubleshoot errors
* Fix: Amazon search button not working in text mode.
* Fix: Amazon logo size with other plugin editors
* Fix: Problem with shortcode creation modal dialog hanging in some cases.
* Fix: General fixes to remove PHP notices.

= 1.4 - January 18, 2017 =
* Conditions of Use have been updated with a worldwide version.  Please review the updated [Conditions of Use](https://s3.amazonaws.com/aalb-public-resources/documents/AssociatesLinkBuilder-ConditionsOfUse-2017-01-17.pdf) for the terms which are applicable to your use.
* Enhancement: Now, you can create text links to Amazon products using amazon_textlink short code. Check [reference guide](https://s3.amazonaws.com/aalb-public-resources/documents/AssociatesLinkBuilder-Guide-HowToCreateTextLinks.pdf) for more details.
* Fix: Display Amazon [buy box](https://www.amazon.com/gp/help/customer/display.html?nodeId=200401830) price.
* General improvements to reduce the loading time of the links/ ad units created via the plugin.
* Updated [plugin user guide](https://s3.amazonaws.com/aalb-public-resources/documents/AssociatesLinkBuilder-UserGuide.pdf).

= 1.3.2 - December 30, 2016 =
* If you are coming from any version other than v1.3 and are using custom templates, be sure to backup your custom templates before updating the plugin by following [these instructions](https://s3.amazonaws.com/aalb-public-resources/documents/AssociatesLinkBuilder-Guide-HowToBackupCustomTemplates.pdf). If you have not created any custom template or if you are coming from v1.3, you can simply update the plugin using the update now link.
* Fix: Problems with rendering ads when using custom templates.

= 1.3 - December 27, 2016 =
* If you have created custom templates, be sure to take a backup of your templates before updating the plugin by following [these instructions](https://s3.amazonaws.com/aalb-public-resources/documents/AssociatesLinkBuilder-Guide-HowToBackupCustomTemplates.pdf). If you have not created any custom template, you can simply update the plugin using the update now link.
* Fix: Preserve custom templates with plugin updates. You will no longer need to back up your custom templates when updating the plugin in future.
* Fix: Update tracking pixel to display:none.
* Fix: Remove ! from PriceLink template.
* Updated [plugin user guide](https://s3.amazonaws.com/aalb-public-resources/documents/AssociatesLinkBuilder-UserGuide.pdf).


= 1.2 - December 19, 2016 =
* If you have created custom templates, be sure to take a backup of your templates before updating the plugin by following [these instructions](https://s3.amazonaws.com/aalb-public-resources/documents/AssociatesLinkBuilder-Guide-HowToBackupCustomTemplates.pdf). If you have not created any custom template, you can simply update the plugin using the update now link.
* Fix: Array dereference issue with PHP version 5.3. You will no longer get PHP syntax issues while activating the plugin.
* Administration of the plugin over a secured network (HTTPS) is recommended and not required.
* Updated [plugin user guide](https://s3.amazonaws.com/aalb-public-resources/documents/AssociatesLinkBuilder-UserGuide.pdf).

= 1.1 - December 13, 2016 =
* Fix: Rendering of search results in Apple Safari browser.

= 1.0 - December 6, 2016 =
* Plugin released for beta testing

== Upgrade Notice ==

= 1.9.3 - August 05, 2019 =
* Fix : Malformed URL for tracking IDs starting with numbers in v1.9.2.

= 1.9.2 =
* Fix : Clicks not appearing for v1.9.1.

= 1.9.1 =
* Fixed issues in v1.9.0 that was not compatible with PHP version 5.5

= 1.9.0 =
* Added Product Advertising API (PA-API) response caching feature. This feature will prevent PA-API throttle by caching PA-API response and updating the cache asynchronously. Added support for Gutenberg editor.

= 1.8.0 =
* Added Support for Australia (AU) marketplace and minor bug-fixes.

= 1.7.0 =
* Added support for wider character set sent by Product Advertising API(PA-API). More details about change on PA-API can be found [here](https://forums.aws.amazon.com/ann.jspa?annID=5543).

= 1.6.0 =
* Added Impressions data in reports shown on respective associates website for ads rendered after upgrade to this release.

= 1.5.4 =
* Fix: In case of failed call to Product-Advertising API, Fatal error thrown.

= 1.5.3 =
Fixed Fatal error for users on PHP version <5.4.0 along with search-button, multi-tab issues faced by few associates.

= 1.5.2 =
Fixed issues in v1.5.1 that were not handled and returned PHP fatal error.

= 1.5.1 =
Fixed issues in v1.5.0 that was incompatible with few environments and changed minimum supported PHP version to 5.4.0.

= 1.5.0 =
Geo-targeted links that enables Amazon Associates to create content customized for and better monetize their international traffic & fix to allow using Product Links with amazon_link shortcode.

= 1.4.13 =
Fix for non working tracking-ids section and Save Changes button for few associates on settings page.

= 1.4.12 =
Tracking IDs are associated with marketplaces in the revamped settings page and add shortcode popup.

= 1.4.11 =
Plugin is now available in Spanish, French and Italian languages.

= 1.4.10 =
Fix for customize theme issue and distorted page alignment for few users.

= 1.4.9 =
Putting strings under translation function for Internationalization & fix for mobile compatibility issues with few default templates.

= 1.4.8 =
Improvement in error display message on product search, fix for the issue of handleBar template not being loaded for a few users on product search and other minor bug fixes.

= 1.4.7 =
Fix for the issue of trailing spaces in a few deafult templates, failed rendering due to expired ASIN and other minor bug fixes.

= 1.4.6 =
Fix for the issue of big Amazon logo and plugin unusable in some editors.

= 1.4.5 =
Prime branding changes, feature to remove rel="noreferrer" from Amazon Affiliate links.

= 1.4.4 =
This update improves mobile rendering of default templates and some bug fixes.

= 1.4.3 =
This update fixes the issue of add shortcode button not clickable for some users after v1.4.2.

= 1.4.2 =
This update restricts plugin activation to supported PHP versions, and fixes bug of plugin not working with other editors.

= 1.4.1 =
This update includes minor bug fixes.

= 1.4 =
This update includes general performance improvements, and support for text links.

= 1.3.2 =
This update fixes the issue with improper rendering of ads using custom templates. If you are coming from v1.3, you don't need to take a backup of your templates before updating to v1.3.2.

= 1.3 =
This update includes a few minor fixes. In particular, you will no longer need to back up your custom templates when updating the plugin in future.
