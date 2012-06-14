=== GoodReviews ===

Contributors:      jhanbackjr
Plugin Name:       GoodReviews
Plugin URI:        http://www.timetides.com/goodreviews-plugin-wordpress
Tags:              goodreads,readers,reviews,stars,isbn
Author URI:        http://www.timetides.com
Author:            James R. Hanback, Jr.
Donate link: 	   http://www.timetides.com
Requires at least: 3.3 
Tested up to:      3.4
Stable tag:        trunk

Display Goodreads.com reviews for ISBNs you specify on any page or post.

== Description ==

The GoodReviews plugin displays information about a specific title from Goodreads, including reader reviews. This plugin was developed mainly for authors or booksellers who want to showcase Goodreads information about specific titles on their Wordpress sites. You must obtain a Goodreads API developer key in order to use this plugin. You can obain an API key by following the instructions at goodreads.com/api. This plugin was developed by a third party who is not affiliated with Goodreads.

Features:

* Allows use of a shortcode to display Goodreads reviews and book information for a specific ISBN in any page or post.
* Returns book information in divs and reviews in an iframe that can be custom styled or use the default styles.

== Installation ==

This section describes how to install the plugin and get it working.

1. Obtain an API developer key from goodreads.com/api
2. If you have a previous version of GoodReviews installed, deactivate and delete it from the '/wp-content/plugins/' directory
3. Upload the GoodReviews folder to the '/wp-content/plugins/' directory
4. Activate GoodReviews by using the 'Plugins' menu
5. Under the Wordpress 'Settings' menu, click GoodReviews and configure the appropriate settings
6. Add the [goodreviews isbn="&lt;book ISBN&gt;"] shortcode, where &lt;book ISBN&gt; is the International Standard Book Number (ISBN) of the book associated with the reviews you want to display. You can use either an ISBN-10 or an ISBN-13. Do not include dashes in the ISBN.

== Frequently Asked Questions ==

= Why would I want to use this plugin? =

GoodReviews serves a very specific requirement. It was primarily developed to enable an author or a bookseller to display Goodreads title information and reader reviews on a Wordpress site. Goodreads is a social network for book lovers that enables members to rate and review books.

= How do I customize the height and width of the book information and reviews pane =

GoodReviews offers two ways to control the height and width of its elements. You can either create custom Cascading Style Sheets (CSS) in your theme, or you can specify height, width, and border values in the shortcode. To use the shortcode method, include one or all of the following parameters in the shortcode:

* height="&lt;some value&gt;"
* width="&lt;some value&gt;"
* border="&lt;on | off&gt;"

For example, to display the book information and reviews in 500x500 elements with a bordered iframe reviews element, you could issue the following shortcode:

[goodreviews isbn="0000000000000" height="500" width="500" border="On"]

Alternatively, you can use your own CSS to customize the book information elements. To override the default style sheets, issue the shortcode with the <code>grstyles</code> parameter set to <code>off</code>. For example:

[goodreviews isbn="0000000000000" grstyles="off"]

After you have disabled the default CSS, you can style the goodreviews elements yourself by adding the appropriate CSS code to your theme.

= How do I customize the CSS for the reviews iframe? =

At this time, there is no easy way to customize the CSS for the reviews iframe. Goodreads' feed includes the CSS for the reviews frame inline. I recommend you keep their default CSS for the reviews frame and simply adjust the height and width of it by using the shortcode.

= How do I make the cover image bigger or smaller, or turn it off completely? =

Issue the shortcode with the <code>cover</code> parameter to select the Goodreads large cover image, small cover image, or no cover image at all. For example, to use the large cover image (which is the default), you would issue the following shortcode:

[goodreviews isbn="0000000000000" cover="large"]

To use the small cover image, you would issue the following shortcode:

[goodreviews isbn="0000000000000" cover="small"]

To turn off the cover completely, you would issue the following shortcode:

[goodreviews isbn="0000000000000" cover="off"]

Please be aware that, at this time, turning off the cover only works if you're using the default CSS supplied with GoodReviews.

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

Ensure that you enter the shortcode in HTML mode, not VISUAL mode.

== Upgrade Notice ==

= 1.0.3 =
Upgrade to fix a DIV issue that might cause conflicts with your theme or other plugins.

= 1.0.2 =
Upgrade to fix an issue that preventing "Buy This Book" links from functioning properly.

= 1.0.1 =
Upgrade to fix a potential developer API key issue that will prevent GoodReviews from working.

= 1.0 =
This is the first version of the plugin

== Screenshots ==

1. The settings page
2. A look at the plugin in action

== Changelog ==

= 1.0.3 =
* Fixed a DIV closing element that was creating problems with the reviews frame. Thanks, Conq and Baden!

= 1.0.2 =
* Fixed an issue that was preventing some "Buy This Book" links from functioning properly.

= 1.0.1 =
* Fixed an API key issue that could prevent GoodReviews from display information from Goodreads.
* Updated the readme.

= 1.0 =
* Initial release
