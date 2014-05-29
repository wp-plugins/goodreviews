=== GoodReviews ===

Contributors:      jhanbackjr
Plugin Name:       GoodReviews
Plugin URI:        http://www.timetides.com/goodreviews-plugin-wordpress
Tags:              goodreads,readers,reviews,stars,isbn
Author URI:        http://www.timetides.com
Author:            James R. Hanback, Jr.
Donate link: 	   http://www.timetides.com
Requires at least: 3.3 
Tested up to:      3.9.1
Stable tag:        1.1.2

Display Goodreads.com reviews for ISBNs or IDs you specify on any page or post.

== Description ==

The GoodReviews plugin displays information about a specific title from Goodreads, including reader reviews. This plugin was developed mainly for authors or booksellers who want to showcase Goodreads information about specific titles on their Wordpress sites. You must obtain a Goodreads API developer key in order to use this plugin. You can obain an API key by following the instructions at goodreads.com/api. This plugin was developed by a third party who is not affiliated with Goodreads.

Features:

* Allows use of a shortcode to display Goodreads reviews and book information for a specific ISBN or Goodreads.com ID in any page or post.
* Returns book information in divs and reviews in an iframe that can be custom styled or use the default styles.

== Installation ==

This section describes how to install the plugin and get it working.

1. Obtain an API developer key from goodreads.com/api
2. If you have a previous version of GoodReviews installed, deactivate and delete it from the '/wp-content/plugins/' directory
3. Upload the GoodReviews folder to the '/wp-content/plugins/' directory
4. Activate GoodReviews by using the 'Plugins' menu
5. Under the Wordpress 'Settings' menu, click GoodReviews and configure the appropriate settings
6. Add the [goodreviews isbn="&lt;book ISBN&gt;"] shortcode, where &lt;book ISBN&gt; is the International Standard Book Number (ISBN) of the book associated with the reviews you want to display. You can use either an ISBN-10 or an ISBN-13. Do not include dashes in the ISBN. If your title does not have an ISBN, you can use grid="&lt;Goodreads ID&gt;" in place of the ISBN parameter, where &lt;Goodreads ID&gt; is the ID number assigned to your title by Goodreads.

== Frequently Asked Questions ==

= Why would I want to use this plugin? =

GoodReviews serves a very specific requirement. It was primarily developed to enable an author or a bookseller to display Goodreads title information and reader reviews on a Wordpress site. Goodreads is a social network for book lovers that enables members to rate and review books.

= What if my title does not have an ISBN? =

As of version 1.0.4 of Goodreviews, you can use the Goodreads ID instead of the ISBN to retrieve title information from Goodreads. Replace the <code>isbn</code> parameter in the shortcode with <code>grid</code> and use the Goodreads ID number instead of an ISBN.

= How do I customize the height and width of the book information and reviews pane =

GoodReviews offers two ways to control the height and width of its elements. You can either create custom Cascading Style Sheets (CSS) in your theme, or you can specify height, width, and border values in the shortcode. To use the shortcode method, include one or all of the following parameters in the shortcode:

* height="&lt;some value&gt;"
* width="&lt;some value&gt;"
* border="&lt;on | off&gt;"

For example, to display the book information and reviews in 500x500 elements with a bordered iframe reviews element, you could issue the following shortcode:

[goodreviews isbn="0000000000000" height="500" width="500" border="On"]

= How do I turn off the default CSS for this plugin? =

As of version 1.1.0, you can completely disable the default styles by providing a valid URL to an alternate stylesheet in the Alternate Stylesheet URL field on the GoodReviews Settings page. If you have previously altered your theme CSS to modify GoodReviews CSS elements for a previous version of GoodReviews, you should provide a URL to a blank stylesheet file in the Alternate Stylesheet URL field.

In versions 1.0.5 and earlier, you can disable the default styles per-shortcode by issuing the <code>grstyle="off"</code> parameter within the shortcode. However, the grstyles parameter does not disable the default CSS in the Reviews element.

= How do I customize the CSS for this plugin? =

As of version 1.1.0, you can customize the look of the book info, buy info, and reviews elements by supplying the URL to an alternate stylesheet on the Settings page. You can also use the following shortcode parameters:

* grbackground - provide the hexadecimal code for the background color you want (do not include the # symbol)
* grtext - provide the hexadecimal code for the text color you want (do not include the # symbol)
* grstars - provide the hexadecimal code for the star color you want (do not include the # symbol)
* grlinks - provide the hexadecimal code for the text color you want (do not include the $ symbol)
* grheader - provide the text that you want to appear above the reviews frame

For example, issuing the following shortcode will style the GoodReviews elements with white text on black background along with blue stars and red links. The header above the reviews frame will be "My Reviews."

<code>[goodreviews isbn="0000000000000" grbackground="000" grtext="fff" grstars="00f" grlinks="f00" grheader="My Reviews"]</code>

= Can I control the number of reviews that the plugin returns? =

You can use the <code>grnumber</code> parameter to control the number of reviews that are displayed on each page of reviews. By default, 10 reviews per page are displayed. The following code would configure GoodReviews to return 20 reviews per page instead:

<code>[goodreviews isbn="0000000000000" grnumber="20"]</code>

= What if I don't want people to see 1-star reviews of my title? =

You can configure GoodReviews to return only the reviews that meet a minimum star rating. By default, the plugin returns all reviews, regardless of star rating. The following code would configure GoodReviews to return only reviews that have a 3-star rating or higher:

<code>[goodreviews isbn="0000000000000" grminimum="3"]</code>

= How do I make the cover image bigger or smaller, or turn it off completely? =

Issue the shortcode with the <code>cover</code> parameter to select the Goodreads large cover image, small cover image, or no cover image at all. For example, to use the large cover image (which is the default), you would issue the following shortcode:

[goodreviews isbn="0000000000000" cover="large"]

To use the small cover image, you would issue the following shortcode:

[goodreviews isbn="0000000000000" cover="small"]

To turn off the cover completely, you would issue the following shortcode:

[goodreviews isbn="0000000000000" cover="off"]

Please be aware that, at this time, turning off the cover only works if you're using the default CSS supplied with GoodReviews.

= Can I display my Goodreads.com author photo in the book information element? =

Yes, as of version 1.0.4. By default, the author photo is not displayed. You can enable the display of either the large version or the scaled version of your Goodreads author photo by issuing the shortcode with the <code>author</code> parameter. For example, to show the large version of your author photo, you could use the following shortcode:

[goodreviews isbn="0000000000000" author="large"]

To use the small version of your author photo, you could use the following shortcode:

[goodreviews isbn="0000000000000" author="small"]

= What if I don't want to display the book information element? =

You can turn off the book information element by issuing the shortcode with the <code>bookinfo</code> parameter set to <code>off</code>. For example:

[goodreviews isbn="0000000000000" bookinfo="off"]

= What if I don't want to display the book buying links? =

You can turn off the book buying links by issuing the shortcode with the <code>buyinfo</code> parameter set to <code>off</code>. For example:

[goodreviews isbn="0000000000000" buyinfo="off"]

= What if I don't want to display the reviews iframe? =

Because the main point of GoodReviews is to display reviews, no shortcode parameter yet exists to turn off the reviews iframe.

= Why is some information (such as the cover image, publisher information, or publication date) missing from the GoodReviews elements when it is visible on the Goodreads site? =

This is a mystery to me. The GoodReviews plugin uses what the Goodreads API returns for a given ISBN. Therefore, if the information is in the API feed, GoodReviews will display it. Unfortunately, sometimes the information for some titles/editions appears to be excluded from the API feed even if it is present on the Goodreads link for that title.

= Can I turn off the Goodreads credits at the bottom of each element? =

No, nor should you. Turning off the Goodreads credits is a violation of their API Terms and Conditions. 

= The shortcode doesn't seem to work. What should I do? =

Ensure that you enter the shortcode in TEXT/HTML mode, not VISUAL mode.

== Upgrade Notice ==

= 2.0.0 =
Upgrade to 2.0.0 to enable better Wordpress Settings API integration, better API throttling protection, new GoodReviews widgets, and the possibility of using responsive styles.

= 1.1.2 =
Upgrade to fix a bug in the default stylesheet that could affect link colors sitewide.

= 1.1.1 =
Upgrade to automatically remove clear space div when only the reviews pane is displayed and fix a stylesheet handling bug.

= 1.1.0 =
Major update. Upgrade to 1.1.0 to enable more granular control over the look of output. *WARNING* The grstyles parameter in previous versions of GoodReviews is no longer supported. See the FAQ for more information.

= 1.0.5 =
Upgrade to enable basic GoodReviews troubleshooting tools on the Settings page.

= 1.0.4 =
Upgrade to be able to retrieve titles by their Goodreads ID and to display author images.

= 1.0.3 =
Upgrade to fix a DIV issue that might cause conflicts with your theme or other plugins.

= 1.0.2 =
Upgrade to fix an issue that preventing "Buy This Book" links from functioning properly.

= 1.0.1 =
Upgrade to fix a potential developer API key issue that will prevent GoodReviews from working.

= 1.0 =
This is the first version of the plugin

== Screenshots ==

1. The Settings page
2. The plugin in action
3. The shortcode in a post

== Changelog ==

= 2.0.0 =
* Plugin has been completely rewritten to better integrate with the Wordpress Settings API.
* Added support for styling the output in a more responsive way.
* Added separate WordPress widgets for book buying links, book information, and book reviews. 
* Added support for WordPress localization (i18n).
* Added support for HTTP retries and an exponential backoff method of dealing with throttling problems.
* Added support for context-sensitive help on the Settings page.
* Added support for an uninstall process that removes all settings and plugin files.
* Added support for a shortcut parameter that disables the reviews pane.

= 1.1.2 =
* Fixed a default stylesheet bug that could cause links sitewide to display as HTML color #660.

= 1.1.1 =
* Fixed a stylesheet loading bug.
* Added code to remove clear space div element when only the reviews pane is displayed.
* Cleaned up some leftover pre-1.1.0 code.

= 1.1.0 =
* Replaced star images with text-based stars so that colors can be easily modified.
* Added a field to Settings to enable the use of an alternate stylesheet.
* Moved default stylesheet to an included file instead of echoing it from functions.
* Removed support for the grstyles shortcode parameter.
* Added the grstars parameter to enable changing the color of the review stars.
* Added the grlinks parameter to enable changing the color of the review links.
* Added the grheader parameter to enable changing the text of the header above the reviews iframe.
* Added the grbackground parameter to enable changing the color of the review background.
* Added the grtext parameter to enable changing the color of the review text.
* Added the grnumber parameter to allow configuration of the initial number of reviews that are returned.
* Added the grminimum parameter to allow the configuration of a minimum review rating requirement.

= 1.0.5 =
* Added a link to Settings on the Plugins page.
* Added basic PHP environment detection on the Settings page to assist in troubleshooting.

= 1.0.4 =
* Added the ability to retrieve a title by its Goodreads ID instead of ISBN.
* Added the ability to display an author's Goodreads photo beside the author's name.

= 1.0.3 =
* Fixed a DIV closing element that was creating problems with the reviews frame. Thanks, Conq and Baden!

= 1.0.2 =
* Fixed an issue that was preventing some "Buy This Book" links from functioning properly.

= 1.0.1 =
* Fixed an API key issue that could prevent GoodReviews from display information from Goodreads.
* Updated the readme.

= 1.0 =
* Initial release
