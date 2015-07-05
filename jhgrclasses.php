<?php
/* jhgrclasses.php is part of the GoodReviews plugin for WordPress
 * 
 * This file is distributed as part of the GoodReviews plugin for WordPress
 * and is not intended to be used apart from that package. You can download
 * the entire GoodReviews plugin from the WordPress plugin repository at
 * http://wordpress.org/plugins/goodreviews/
 */

/* 
 * Copyright 2011-2014	James R. Hanback, Jr.  (email : james@jameshanback.com)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class jhgrRequirements
{
    public $jhgrTransient  = "jhgrT-";

    public function jhgrLoadLocal()
    {
        load_plugin_textdomain('goodreviews',false,basename(dirname(__FILE__)).'/lang');
    }
}

class jhgrWPOptions
{
    public $jhgrGoodreadsAPIKey = '';
    public $jhgrGoodreviewsCSS  = '';
    public $jhgrTermsAgreement  = '1';
    public $jhgrResponsiveStyle = '0';
    public $jhgrCacheExpire     = 12;
    public $jhgrClearCache      = 0;
    public $jhgrDefer           = 0;
    
    private function szActivate()
    {
        // Primarily applies for upgrades to 2.2.0 and later
        $szUser       = wp_get_current_user();
        $szMeta_type  = 'user';
        $szUser_id    = 0;
        $szMeta_value = '';
        $szDelete_all = true;

        if(! get_user_meta($szUser->ID, 'goodreviews_220')) {
            delete_option('goodreviews-getmethod');
            delete_metadata( $szMeta_type, $szUser_id, 'goodreviews_ignore_FileGetEnabled', $szMeta_value, $szDelete_all );
            delete_metadata( $szMeta_type, $szUser_id, 'goodreviews_ignore_CurlEnabled', $szMeta_value, $szDelete_all );
            delete_metadata( $szMeta_type, $szUser_id, 'goodreviews_ignore_CurlDisabled', $szMeta_value, $szDelete_all );
        }
        add_user_meta($jhgrUser->ID, 'goodreviews_220','true',true);
    }

    public function jhgrCleanCache()
    {
        global $wpdb;
        $jhgrDBquery = 'SELECT option_name FROM ' . $wpdb->options . ' WHERE option_name LIKE \'_transient_timeout_jhgrT-%\';';
        $jhgrCleanDB = $wpdb->get_col($jhgrDBquery);
        foreach ($jhgrCleanDB as $jhgrTransient) {
            $jhgrDBKey = str_replace('_transient_timeout_','',$jhgrTransient);
            delete_transient($jhgrDBKey);
        }
    }

    public function jhgrAddHelp($jhgrContextHelp)
    {
        $jhgrOverview     = '<p>' .
                          __('The GoodReviews plugin retrieves Goodreads.com reader reviews for books you choose and displays them in pages or posts on your WordPress blog by way of a WordPress shortcode.','goodreviews') .
                          '</p> <p>' .
                          __('You must have a Goodreads API Developer Key in order to use this plugin. Links to Goodreads.com information about this program are available on the GoodReviews Settings page.','goodreviews') .
                          '</p>';
        $jhgrSettingsUse  = '<p>' .
                          __('The following GoodReviews Settings fields are ','goodreviews') .
                          '<strong>' .
                          __('required','goodreviews') .
                          '</strong>:<ul><li><strong>' . __('Goodreads.com API Developer Key','goodreviews') . '</strong>: ' .
                          __('A key assigned to you by Goodreads.','goodreviews') .
                          '</li><li><strong>' . __('Display Goodreads.com Links','goodreviews') . '</strong>: ' .
                          __('Indicates that you agree to display Goodreads.com links on publicly accessible pages or posts on your WordPress site.','goodreviews') .
                          '</li></ul></p><p>' .
                          __('The following GoodReviews Settings are optional','goodreviews') .
                          ':<ul><li><strong>' . __('Custom CSS URL','goodreviews') . '</strong>: ' .
                          __('GoodReviews comes with a stock CSS file based on Goodreads.com styles. However, you can use your own CSS to style GoodReviews output. If you have created your own CSS file, put the URL to that file in this field. Otherwise, leave this field blank.','goodreviews') .
                          ':<li><strong>' . __('Use Responsive Style','goodreviews') . '</strong>: ' .
                          __('GoodReviews comes with a stock CSS file based on Goodreads.com styles. However, you can opt to use an alternate style designed for sites with responsive themes. Selecting this checkbox will cause GoodReviews to ignore any values you enter in the Custom CSS URL field.','goodreviews') .
                          '</li></ul></p>';
        $jhgrShortcodeUse = '<p>' .
                          __('Type the shortcode ','goodreviews') .
                          '<code>[goodreviews isbn="<i>isbn-number</i>"]</code>, ' .
                          __('where ','goodreviews') .
                          '<i>isbn</i> ' .
                          __('is the ISBN-10 or ISBN-13 of the book associated with the reviews you want to retrieve. The shortcode must be issued in text format in your page or post, not Visual format. Otherwise, the quotation marks inside the shortcode might be rendered incorrectly.','goodreviews') .
                          '</p><p>' .
                          __('You can also issue the GoodReviews shortcode with the following identifer instead of using an ISBN','goodreviews') .
                          ':<ul><li><code>grid</code>: ' .
                          __('Retrieves reviews by using the ID assigned by Goodreads.com.','goodreviews') .
                          '</li></ul></p><p>' .
                          __('You can also issue the GoodReviews shortcode with the following additional parameters','goodreviews') .
                          ':<ul><li><code>width</code>: ' .
                          __('Specifies the width of the reviews iframe, or of the containing element if the responsive option is enabled.','goodreviews') .
                          '</li><li><code>height</code>: ' .
                          __('Specifies the height of the reviews iframe, or of the containing element if the responsive option is enabled.','goodreviews') .
                          '</li><li><code>border</code>: ' .
                          __('When set to ','goodreviews') .
                          '<code>false</code>, ' .
                          __('disables the border that some browsers automatically add to iframes.','goodreviews') .
                          '</li><li><code>cover</code>: ' .
                          __('toggles book cover image display.','goodreviews') .
                          ' <code>large</code> ' .
                          __('displays the large cover,','goodreviews') .
                          ' <code>small</code> ' .
                          __('displays the small cover, and ','goodreviews') .
                          '<code>off</code> ' .
                          __('removes the cover from display.','goodreviews') .
                          ' <code>large</code> ' .
                          __('is default.','goodreviews').
                          '</li><li><code>author</code>:' .
                          __('toggles the author image display.','goodreviews') .
                          ' <code>large</code> ' . 
                          __('displays the large image,','goodreviews') .
                          ' <code>small</code> ' .
                          __('displays the small image, and','goodreviews') .
                          ' <code>off</code> ' .
                          __('removes the image from display.','goodreviews') .
                          ' <code>off</code> ' .
                          __('is default.','goodreviews') .
                          '</li><li><code>bookinfo</code>: '.
                          __('toggles book information element. ','goodreviews') .
                          '<code>on</code> ' .
                          __('displays the element.','goodreviews') .
                          ' <code>off</code> ' .
                          __('removes the element from display. ','goodreviews') .
                          '<code>on</code> ' .
                          __('is default.','goodreviews') .
                          '</li><li><code>buyinfo</code>: ' .
                          __('toggles book buying links elements. ','goodreviews') .
                          '<code>on</code> ' .
                          __('displays the element. ','goodreviews') .
                          '<code>off</code> ' .
                          __('removes the element from display.','goodreviews') .
                          ' <code>on</code> ' .
                          __('is default.','goodreviews') .
                          '</li><li><code>reviews</code>: ' .
                          __('toggles the reviews frame. ','goodreviews') .
                          '<code>on</code> ' .
                          __('displays the reviews frame. ','goodreviews') .
                          '<code>off</code> ' .
                          __('removes the reviews frame from display.','goodreviews') .
                          ' <code>on</code> ' .
                          __('is default.','goodreviews') .
                          '</li><li><code>grbackground</code>: ' .
                          __('defines the hexadecimal background color of the plugin elements. Do not include the "#" symbol.','goodreviews') .
                          '</li><li><code>grtext</code>: ' .
                          __('defines the hexadecimal text color of the plugin elements. Do not include the "#" symbol.','goodreviews') .
                          '</li><li><code>grstars</code>: ' .
                          __('defines the hexadecimal stars color of the plugin elements. Do not include the "#" symbol.','goodreviews') .
                          '</li><li><code>grlinks</code>: ' .
                          __('defines the hexadecimal link color of the plugin elements. Do not include the "#" symbol.','goodreviews') .
                          '</li><li><code>grnumber</code>: ' .
                          __('defines the number of reviews per page. Default is 10.','goodreviews') .
                          '</li><li><code>grminimum</code>: ' .
                          __('defines the minimum star rating that is required for a review to be displayed. By default, all reviews are displayed.','goodreviews') .                       
                          '</li></ul></p>';
        $jhgrTestsUse     = '<p>' .
                          __('After you have saved your GoodReviews Settings by clicking the ','goodreviews') .
                          '<strong>' .
                          __('Save Changes','goodreviews') .
                          '</strong> ' .
                          __('button, you can click the ','goodreviews') .
                          '<strong>' .
                          __('Tests','goodreviews') .
                          '</strong> ' .
                          __('tab to view some sample reviews frames based on your settings.','goodreviews') .
                          '</p><p>' . 
                          __('If you do not see sample Goodreads.com output on this tab, your GoodReviews settings might be incorrect.','goodreviews') .
                          '</p>';
        $jhgrWidgetsUse   = '<p>' .
                          __('GoodReviews comes with three widgets that can be accessed by using the ','goodreviews') .
                          '<strong>' .
                          __('Appearance &gt; Widgets','goodreviews') .
                          '</strong>' .
                          __(' admin menu: ','goodreviews') .
                          '<ul><li><strong>' .
                          __('About This Book: ','goodreviews') .
                          '</strong> ' .
                          __('Use this widget to display the average rating, description, and other general information about the title.','goodreviews') .
                          '</li><li><strong>' .
                          __('Buy This Book:','goodreviews') .
                          '</strong> ' .
                          __('Use this widget to display Goodreads.com links to bookstores where the title can be purchased.','goodreviews') .
                          '</li><li><strong>' .
                          __('Reviews From Goodreads','goodreviews') .
                          '</strong> ' .
                          __('Use this widget to display Goodreads.com reviews for a given title.</li>','goodreviews') .
                          '</li></ul></p><p><strong>' .
                          __('WARNING','goodreviews') .
                          '</strong>: ' .
                          __('It is not recommended to use both the GoodReviews widgets and the GoodReviews shortcode on the same site. The GoodReviews stylesheet applies to the shortcode and the widgets equally. Therefore, trying to use both the shortcode and the widgets might create strange appearance results on your site.','goodreviews') . 
                          '</p>';
        $jhgrPerfUse      = '<p>' .
                          __('By default, GoodReviews caches Goodreads calls for 12 hours to enhance site performance. ','goodreviews') .
                          __('You can adjust the amount of time GoodReviews caches this data by adjusting the ','goodreviews') .
                          '<strong>' .
                          __('Cache Expires In','goodreviews') .
                          '</strong> ' .
                          __('value to the number of hours you want the cached data to persist.','goodreviews') .
                          '</p><p>' . 
                          __('You can also choose to clear the existing cached data from the WordPress database. However, you should always back up your WordPress database before attempting to delete data in bulk.','goodreviews') .
                          '</p><p>' .
                          __('Please be aware that if you are using a caching plugin, such as W3 Total Cache, with object caching enabled, the Clear Cache option will not do anything. You will need to clear the object cache by using the caching plugin\'s clear cache feature.','goodreviews') .
                          '</p><p>' .
                          __('Select the ','goodreviews') .
                          '<strong>' .
                          __('Defer Until Footer','goodreviews') .
                          '</strong>' .
                          __(' field to ensure that the entire main page of your site loads before GoodReviews attempts to load the Goodreads reviews iframe.','goodreviews') .
                          '</p><p>' .
                          __('Please be aware that if you are using a caching plugin, such as W3 Total Cache, with object caching enabled, and/or the CloudFlare RocketLauncher script service, you might need to optimize your caching plugin\'s settings for this option to work properly. A good guide for configuring W3 Total Cache and CloudFlare together is','goodreviews') .
                          '<a href="https://www.besthostnews.com/guide-to-w3-total-cache-settings-with-cloudflare/" target="_blank">' .
                          __(' here','goodreviews') .
                          '</a></p>';
    
        $jhgrScreen   = get_current_screen();           
        $jhgrScreen->add_help_tab(array(
            'id'      => 'jhgrOverviewTab',
            'title'   => __('Overview','goodreviews'),
            'content' => $jhgrOverview,
        ));
        $jhgrScreen->add_help_tab(array(
            'id'      => 'jhgrSettingsUseTab',
            'title'   => __('Settings','goodreviews'),
            'content' => $jhgrSettingsUse,
        ));
        $jhgrScreen->add_help_tab(array(
            'id'      => 'jhgrShortcodeUseTab',
            'title'   => __('Shortcode','goodreviews'),
            'content' => $jhgrShortcodeUse,
        ));
        $jhgrScreen->add_help_tab(array(
            'id'      => 'jhgrTestsUseTab',
            'title'   => __('Tests Tab','goodreviews'),
            'content' => $jhgrTestsUse,
        ));
        $jhgrScreen->add_help_tab(array(
            'id'      => 'jhgrPerfUseTab',
            'title'   => __('Performance Tab','goodreviews'),
            'content' => $jhgrPerfUse,
        ));
        $jhgrScreen->add_help_tab(array(
            'id'      => 'jhgrWidgetsUseTab',
            'title'   => __('Widgets','goodreviews'),
            'content' => $jhgrWidgetsUse,
        ));
        return $jhgrContextHelp;
    }
    
    public function jhgrGetAPIKey()
    {
        $this->jhgrGoodreadsAPIKey = get_option('goodreviews-api-key','');
        return $this->jhgrGoodreadsAPIKey;
    }

    public function jhgrSetAPIKey($newval)
    {
        $this->jhgrGoodreadsAPIKey = sanitize_text_field($newval);
        return $this->jhgrGoodreadsAPIKey;
    }

    public function jhgrGetRetrieveMethod()
    {
        $this->jhgrRetrieveMethod = get_option('goodreviews-getmethod','');
        return $this->jhgrRetrieveMethod;
    }

    public function jhgrSetRetrieveMethod($newval)
    {
        // Upgrading from 1.x?
        $this->jhgrRetrieveMethod = (trim($newval)=='checked') ? 1 : trim($newval);
        return absint($this->jhgrRetrieveMethod);
    }
    
    public function jhgrGetAgreement()
    {
        $this->jhgrTermsAgreement = get_option('goodreviews-agree','');
        // Upgrading from 1.x?
        if($this->jhgrTermsAgreement=='checked')
        {
           $this->jhgrTermsAgreement = 1;
        }
        return $this->jhgrTermsAgreement;
    }

    public function jhgrSetAgreement($newval)
    {
        // Upgrading from 1.x?
        $this->jhgrTermsAgreement = (trim($newval)=='checked') ? 1 : trim($newval);
        return absint($this->jhgrTermsAgreement);
    }
    
    public function jhgrGetResponsive()
    {
        $this->jhgrResponsiveStyle = get_option('goodreviews-responsive-style','');
        return $this->jhgrResponsiveStyle;
    }

    public function jhgrSetResponsive($newval)
    {
        $this->jhgrResponsiveStyle = (trim($newval)=='checked') ? 1 : trim($newval);
        return absint($this->jhgrResponsiveStyle);
    }
    
    public function jhgrGetCustomCSS()
    {
        $this->jhgrGoodreviewsCSS = get_option('goodreviews-alt-style','');
        return $this->jhgrGoodreviewsCSS;
    }

    public function jhgrSetCustomCSS($newval)
    {
        $this->jhgrGoodReviewsCSS = esc_url($newval);
        return $this->jhgrGoodReviewsCSS;
    }
    
    public function jhgrSetCacheExpire($newval)
    {
        $this->jhgrCacheExpire = (! empty($newval)) ? trim($newval) : 12;
        return absint($this->jhgrCacheExpire);
    }
    
    public function jhgrGetCacheExpire()
    {
        $this->jhgrCacheExpire = get_option('goodrev-perform','12');
        return absint($this->jhgrCacheExpire);
    }
    
    public function jhgrSetClearCache($newval)
    {
        $this->jhgrClearCache = (! empty($newval)) ? trim($newval) : 0;
        return absint($this->jhgrClearCache);
    }
    
    public function jhgrGetClearCache()
    {
        $jhgrClear = get_option('goodrev-clearcache','0');
        update_option('goodrev-clearcache','0');
        return absint($jhgrClear);
    }
    
    public function jhgrSetDefer($newval)
    {
        $this->jhgrDefer = (trim($newval)=='checked') ? 1 : trim($newval);
        return absint($this->jhgrDefer);
    }
    
    public function jhgrGetDeferParse()
    {
        $this->jhgrDefer = get_option('goodrev-defer','');
        return $this->jhgrDefer;
    }

    public function jhgrRegisterSettings() 
    {
       register_setting('goodrev_options','goodreviews-api-key',array(&$this,'jhgrSetAPIKey'));
       register_setting('goodrev_options','goodreviews-getmethod',array(&$this,'jhgrSetRetrieveMethod'));
       register_setting('goodrev_options','goodreviews-agree',array(&$this,'jhgrSetAgreement'));
       register_setting('goodrev_options','goodreviews-alt-style',array(&$this,'jhgrSetCustomCSS'));
       register_setting('goodrev_options','goodreviews-responsive-style',array(&$this,'jhgrSetResponsive'));
       register_setting('goodrev_perform','goodrev-perform',array(&$this, 'jhgrSetCacheExpire'));
       register_setting('goodrev_perform','goodrev-clearcache',array(&$this, 'jhgrSetClearCache'));
       register_setting('goodrev_perform','goodrev-defer',array(&$this, 'jhgrSetDefer'));
    }
    
    public function jhgrGoodreadsAPIKeyField($args)
    {
        $jhgrField  = '<input id="goodreviews-api-key" name="goodreviews-api-key" type="text" value="' . $this->jhgrGetAPIKey() . '" /><br />';
        $jhgrField .= '<label for="goodreviews-api-key">' . sanitize_text_field($args[0]) . '</label>';
        echo $jhgrField;
    }
    
    public function jhgrRetrieveMethodField($args)
    {
        $jhgrField  = '<input type="checkbox" name="goodreviews-getmethod" id="goodreviews-getmethod" value="1" ' .
                      checked(1, $this->jhgrGetRetrieveMethod(), false) .
                      $this->jhgrGetRetrieveMethod() .
                      ' /><br />';
        $jhgrField .= '<label for="goodreviews-getmethod"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $jhgrField;
    }
    
    public function jhgrTermsAgreeField($args)
    {
        $jhgrField  = '<input type="checkbox" name="goodreviews-agree" id="goodreviews-agree" value="1" ' .
                      checked(1, $this->jhgrGetAgreement(), false) .
                      $this->jhgrGetAgreement() .
                      ' /><br />';
        $jhgrField .= '<label for="goodreviews-agree"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $jhgrField;
    }

    public function jhgrCustomCSSField($args)
    {
        $jhgrField  = '<input id="goodreviews-alt-style" name="goodreviews-alt-style" type="text" value="' . esc_url($this->jhgrGetCustomCSS()) . '" ';
        $jhgrField .= ($this->jhgrGetResponsive()=='1') ? 'disabled' : '';
        $jhgrField .= ' /><br />';
        $jhgrField .= '<label for="goodreviews-alt-style">' . sanitize_text_field($args[0]) . '</label>';
        echo $jhgrField;
    }
    
    public function jhgrResponsiveStyle($args)
    {
        $jhgrField  = '<input type="checkbox" name="goodreviews-responsive-style" id="goodreviews-responsive-style" value="1" ' .
                      checked(1, $this->jhgrGetResponsive(), false) .
                      $this->jhgrGetResponsive() .
                      ' /><br />';
        $jhgrField .= '<label for="goodreviews-responsive-style">' . sanitize_text_field($args[0]) . '</label>';
        echo $jhgrField;
    }
    
    public function jhgrGRTestField($args)
    {
        echo do_shortcode('[goodreviews isbn="1451627289" buyinfo="off" bookinfo="off" width="565" height="400"]');
    }
    
    public function jhgrCacheExpireField($args)
    {
	    $jhgrField = '<select id="goodrev-perform" name="goodrev-perform">';
	    for($x=1;$x<24;$x++)
	    {
	        $jhgrFieldSelected = ($this->jhgrGetCacheExpire()==$x) ? ' selected="selected"' : '';
	        $jhgrField .= '<option value="' .
	                    absint($x) .
	                    '"' .
	                    sanitize_text_field($jhgrFieldSelected) .
	                    '>' .
	                    absint($x) .
	                    '</option>';
	    }
	    $jhgrField .= '</select> Hours<br />';
        $jhgrField .= '<label for="goodrev-perform"> '  . sanitize_text_field($args[0]) . '</label>';
	    echo $jhgrField;
    }
    
    public function jhgrClearCacheField($args)
    {
        $jhgrField  = '<input type="checkbox" name="goodrev-clearcache" id="goodrev-clearcache" value="1" /><br />';
        $jhgrField .= '<label for="goodrev-clearcache"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $jhgrField;
    }
    
    public function jhgrDeferField($args)
    {
        $jhgrField  = '<input type="checkbox" name="goodrev-defer" id="goodrev-defer" value="1" ' .
        checked(1, $this->jhgrGetDeferParse(), false) .
        $this->jhgrGetDeferParse() .
        ' /><br />';
        $jhgrField .= '<label for="goodrev-defer"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $jhgrField;
    }
    
    public function jhgrGetOptionsForm()
    {
        add_settings_field(
            'goodreviews-goodreads-api-key',
            __('Goodreads.com<br />API Developer Key','goodreviews'),
            array(&$this, 'jhgrGoodreadsAPIKeyField'),
            'goodrev-options',
            'goodreviews_retrieval_section',
            array(
                __('Type or paste your Goodreads.com API Developer Key in this field.','goodreviews')
            )
        );
        
        add_settings_field(
            'goodreviews-custom-css',
            __('Custom CSS URL','goodreviews'),
            array(&$this, 'jhgrCustomCSSField'),
            'goodrev-options',
            'goodreviews_retrieval_section',
            array(
               __('If you have a customized CSS file for GoodReviews, type or paste the URL to it here. Otherwise, leave this field blank.','goodreviews')
            )
        );
        
        add_settings_field(
           'goodreviews-responsive-style',
           __('Use Responsive Style','goodreviews'),
           array(&$this, 'jhgrResponsiveStyle'),
           'goodrev-options',
           'goodreviews_retrieval_section',
           array(
              __('If your site uses a responsive theme, you can enable a default GoodReviews stylesheet that includes responsive elements. If you enable this option, any Custom CSS URL you have specified above will be ignored.','goodreviews')
           )
        );
        
        add_settings_field(
            'goodreviews-agree',
            __('Display Goodreads.com Links','goodreviews'),
            array(&$this, 'jhgrTermsAgreeField'),
            'goodrev-options',
            'goodreviews_retrieval_section',
            array(
                __('You MUST agree to allow GoodReviews to display Goodreads.com links on your WordPress posts or pages in order to use this plugin. If you do not select this checkbox, GoodReviews will not display reviews.','goodreviews')
            )
        );
        
        add_settings_field(
            'goodreviews-test-frame',
            __('Test Frame','goodreviews'),
            array(&$this, 'jhgrGRTestField'),
            'goodrev-tests',
            'goodreviews_test_section',
            array(
                __('GoodReviews Test Frame.','goodreviews')
            )
        );
        
        add_settings_field(
            'goodrev-perform',
            __('Cache Expires In','goodreviews'),
            array(&$this, 'jhgrCacheExpireField'),
            'goodrev-perform',
            'goodreviews_perform_section',
            array(
                __('The number of hours that should pass before cached Goodreads calls expire. Cannot be more than 23 hours. Default is 12.','goodreviews')
            )
        );
        
        add_settings_field(
            'goodrev-defer',
            __('Defer Until Footer','goodreviews'),
            array(&$this, 'jhgrDeferField'),
            'goodrev-perform',
            'goodreviews_perform_section',
            array(
                __('Loads GoodReviews data asynchronously for better site performance. If you use W3 Total Cache and/or CloudFlare, please know that you might need to adjust your W3TC/CloudFlare Performance settings for this option to work. See the Help menu for more information.','goodreviews')
            )
        );
        
        add_settings_field(
            'goodrev-clear-cache-field',
            __('Clear Cache','goodreviews'),
            array(&$this, 'jhgrClearCacheField'),
            'goodrev-perform',
            'goodreviews_perform_section',
            array(
                __('Clears GoodReviews transient data.','goodreviews')
            )
        );        
    }
        
    public function jhgrOptionsCallback()
    {
        $jhgr1      = __('In order to access customer review data from Goodreads.com, you must have a Goodreads API Developer Key. You can obtain an API Developer Key by visiting ','goodreviews');
        $jhgr2      = __('Goodreads','goodreviews');
        $jhgrFormat = '<p>%s<a href="https://www.goodreads.com/api" target="_blank">%s</a>.</p>';

        printf($jhgrFormat,$jhgr1,$jhgr2);
    }
    
    public function jhgrTestCallback()
    {
        $jhgr1      = __('If you have correctly configured GoodReviews, you should see an iframe below that contains Goodreads.com reviews for the Stephen King novel 11/22/63. The shortcode used to produce this test is ','goodreviews');
        $jhgr2      = __('. If you see reviews in the iframe below, GoodReviews is configured correctly and should work on your site. If you see no data or if you see an error displayed below, please double-check your configuration.','goodreviews');
        $jhgrFormat = '<p>%s<code>[goodreviews isbn="1451627289" buyinfo="off" bookinfo="off"]</code>%s</p>';

        printf($jhgrFormat,$jhgr1,$jhgr2);
    }
    
    public function jhgrPerformCallback()
    {
        $jhgr1      = __('WARNING!','goodreviews');
        $jhgr2      = __('You should make a backup of your WordPress database before attempting to use the <strong>Clear Cache</strong> option. The <strong>Clear Cache</strong> option attempts to delete data directly from the WordPress database and is therefore dangerous. Use the <strong>Clear Cache</strong> option with caution.','goodreviews');
        $jhgrFormat = '<p><h2>%s</h2></p><p>%s</p>';

        printf($jhgrFormat,$jhgr1,$jhgr2);
    }
    
    public function jhgrUsageCallback()
    {
        echo '<p><b>' . __('Shortcode','goodreviews') .'</b>: <code>[goodreviews isbn="<i>isbn</i>"]</code></p>';

        $jhgr1      = __('Insert the above shortcode into any page or post where you want Goodreads.com customer reviews to appear. Replace ','goodreviews');
        $jhgr2      = __(' with the International Standard Book Number (ISBN) to retrieve and display the reviews for that product.','goodreviews');
        $jhgr3      = __('For a more detailed and complete overview of how GoodReviews works, click the "Help" tab on the upper right of the GoodReviews settings page.','goodreviews');
        $jhgrFormat = '<p>%s<code><i>isbn</i></code>%s</p><p>%s</p>';

        printf($jhgrFormat,$jhgr1,$jhgr2,$jhgr3);
    }
    
    public function jhgrDonateLink($links,$file)
    {
        // Code based on codex.wordpress.org/Plugin_API/Filter_Reference/plugin_row_meta
        if(strpos($file, 'goodreviews.php') !== false)
        {
            $jhgrDonatelinks = array(
                               '<a href="http://www.timetides.com/donate" target="_blank">Donate</a>'
                               );
            $links = array_merge($links, $jhgrDonatelinks);
        }
        return $links;
    }
    
    public function jhgrOptionsLink($jhgrLink) 
    {
        $jhgrSettingsLink  = '<a href="' . esc_url(admin_url()) . 'admin.php?page=goodrev-options">' . __('Settings','goodreviews') . '</a> | ';
        $jhgrSettingsLink .= '<a href="' . esc_url(admin_url()) . 'admin.php?page=goodrev-tests">' . __('Test','goodreviews') . '</a>';
        array_unshift($jhgrLink,$jhgrSettingsLink);
        return $jhgrLink;
    }
    
    public function jhgrGetOptionsScreen() 
    {
        switch(get_admin_page_title())
        {
            case 'GoodReviews':
                 $_GET['tab'] = 'goodreviews_retrieval_section';
                 break;
            case 'Tests':
                 $_GET['tab'] = 'goodreviews_test_section';
                 break;
            case 'Performance':
                 $_GET['tab'] = 'goodreviews_perform_section';
                 break;
            case 'Usage':
                 $_GET['tab'] = 'goodreviews_usage_section';
                 break;
        }
        // Settings navigation tabs
        if( isset( $_GET[ 'tab' ] ) ) {
            $active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field($_GET[ 'tab' ]) : 'goodreviews_retrieval_section';
        }
        
        echo '<h2 class="nav-tab-wrapper"><a href="' . esc_url(admin_url()) .'admin.php?page=goodrev-options&tab=goodreviews_retrieval_section" class="nav-tab ';
        echo $active_tab == 'goodreviews_retrieval_section' ? 'nav-tab-active' : '';
        echo '">GoodReviews</a><a href="' . esc_url(admin_url()) .'admin.php?page=goodrev-tests&tab=goodreviews_test_section" class="nav-tab ';
        echo $active_tab == 'goodreviews_test_section' ? 'nav-tab-active' : '';
        echo '">Tests</a><a href="' . esc_url(admin_url()) .'admin.php?page=goodrev-perform&tab=goodreviews_perform_section" class="nav-tab ';
        echo $active_tab == 'goodreviews_perform_section' ? 'nav-tab-active' : '';
        echo '">Performance</a><a href="' . esc_url(admin_url()) .'admin.php?page=goodrev-usages&tab=goodreviews_usage_section" class="nav-tab ';
        echo $active_tab == 'goodreviews_usage_section' ? 'nav-tab-active' : '';
        echo '">Usage</a></h2>';
                
        // Settings sections

        add_settings_section(
            'goodreviews_retrieval_section',
            __('GoodReviews Settings','goodreviews'),
            array(&$this, 'jhgrOptionsCallback'),
            'goodrev-options'
        );
               
        add_settings_section(
            'goodreviews_test_section',
            __('GoodReviews Test Frame','goodreviews'),
            array(&$this, 'jhgrTestCallback'),
            'goodrev-tests'
        );
        
        add_settings_section(
            'goodreviews_perform_section',
            __('GoodReviews Performance','goodreviews'),
            array(&$this, 'jhgrPerformCallback'),
            'goodrev-perform'
        );
               
        add_settings_section(
            'goodreviews_usage_section',
            __('GoodReviews Usage','goodreviews'),
            array(&$this, 'jhgrUsageCallback'),
            'goodrev-usages'
        );
        
        // Create settings fields
        $this->jhgrGetOptionsForm();
        
        switch($active_tab)
        {
            case 'goodreviews_retrieval_section':
                 echo '<form method="post" action="options.php">';
                 settings_fields('goodrev_options');
                 do_settings_sections('goodrev-options');
                 echo get_submit_button();
                 echo '</form>';
                 break;
            case 'goodreviews_test_section':
                 do_settings_sections('goodrev-tests');
                 break;
            case 'goodreviews_perform_section':
                 echo '<form method="post" action="options.php">';
                 settings_fields('goodrev_perform');
                 do_settings_sections('goodrev-perform');
                 echo get_submit_button();
                 echo '</form>';
                 break;
            case 'goodreviews_usage_section':
                 do_settings_sections('goodrev-usages');
                 break;
        }
    }
    
    public function jhgrAddAdminPage() 
    {
        $jhgrOptionsPage = add_submenu_page('options-general.php','GoodReviews','GoodReviews','manage_options','goodrev-options',array(&$this, 'jhgrGetOptionsScreen'));
        $jhgrTestingPage = add_submenu_page('goodrev-options','Tests','Tests','manage_options','goodrev-tests',array(&$this, 'jhgrGetOptionsScreen'));
        $jhgrCachingPage = add_submenu_page('goodrev-options','Performance','Performance','manage_options','goodrev-perform',array(&$this, 'jhgrGetOptionsScreen'));
        $jhgrUsingPage   = add_submenu_page('goodrev-options','Usage','Usage','manage_options','goodrev-usages',array(&$this, 'jhgrGetOptionsScreen'));    
        add_action('load-' . $jhgrOptionsPage, array(&$this, 'jhgrAddHelp'));
        add_action('load-' . $jhgrTestingPage, array(&$this, 'jhgrAddHelp'));
        add_action('load-' . $jhgrCachingPage, array(&$this, 'jhgrAddHelp'));
        add_action('load-' . $jhgrUsingPage, array(&$this, 'jhgrAddHelp'));       
    }
    
    public function jhgrgetpagetype()
    {
        global $post;
        $jhgrgoodpage = FALSE;
        
        if(is_home() || is_front_page() || is_active_widget( false, false, 'goodreviews-buybook', true ) || is_active_widget( false, false, 'goodreviews-bookinfo', true ) || is_active_widget( false, false, 'goodreviews-reviews', true )) {
            $jhgrgoodpage = TRUE;
        } elseif (is_single() || is_page()) {
            if(has_shortcode($post->post_content,'goodreviews'))
            {
                $jhgrgoodpage = TRUE;
            }
        }
        
        return $jhgrgoodpage;
    }
    
    public function jhgrRequireStyles()
    {
        // Load responsive stylesheet if required and if shortcode is present
        // below code does NOT work with do_shortcode and requires WP 3.6 or later

        if($this->jhgrgetpagetype())
        {
            if(wp_style_is('goodrev-styles','enqueue'))
            { 
                wp_dequeue_style('goodrev-styles');
                wp_deregister_style('goodrev-styles');
            }

            if ((preg_match('/http/i',$this->jhgrGetCustomCSS()))&&($this->jhgrGetResponsive()!='1'))
            {
                $jhgrStylesheet = esc_url($this->jhgrGetCustomCSS());
            }
            else
            {
                $jhgrStylesheet = ($this->jhgrGetResponsive()!='1') ? plugins_url('goodreviews.css',__FILE__) : plugins_url('goodreviews-rs.css',__FILE__);
            }
        
            wp_register_style('goodrev-styles',$jhgrStylesheet);
            wp_enqueue_style('goodrev-styles');      
        
            // This requires WordPress 3.8 or later
            wp_enqueue_style( 'dashicons' );
        }
        return true;
    }
    
    public function jhgrShowNPNotices()
    {
        if($this->jhgrGetClearCache()=='1') 
        {
            add_settings_error( 'goodreviews-notices', 'goodrev-cache-cleared', __('Cache cleared', 'goodreviews'), 'updated' );
            $this->jhgrCleanCache();
        }
        settings_errors('goodreviews-notices');
    }
    
    public function jhgrQuicktag()
    {
        if(wp_script_is('quicktags'))
        {
        ?>
            <script type="text/javascript">
            QTags.addButton('eg_goodreviews','GRs','[goodreviews isbn="" width="" height="" buyinfo="off" bookinfo="off"]','','goodreviews','GoodReviews Shortcode');
            </script>
        <?php
        }
    }
}

class jhgrBuyBookWidget extends WP_Widget {

    
    function __construct() 
    {
	    $this->textdomain = 'goodreviews';
 
   		// This is where we add the style and script
    	add_action( 'load-widgets.php', array(&$this, 'jhgrLoadBuyBookWidget') );
    	
	    parent::__construct(
		    'goodreviews-buybook', 
		    __('Buy This Book',$this->textdomain),
		    array( 'classname' => 'goodreviews-buybook',
		           'description' => __( 'Goodreads.com Buy Book Links.',$this->textdomain), )
	    );
    }
 
    function jhgrLoadBuyBookWidget() {    
        wp_enqueue_style( 'wp-color-picker' );        
        wp_enqueue_script( 'wp-color-picker' );    
    }
 
    function widget($args, $instance) {
        extract( $args, EXTR_SKIP );
        $jhgrAtts = array();
        echo $before_widget;

		if( isset ($instance[ 'itemtype' ]) )
		{
		    if(empty($instance['itemtype']))
		    {
		        $instance['itemtype'] = 'isbn';
		    }
		    $jhgrAtts[strip_tags($instance['itemtype'])] = strip_tags($instance['itemvalue']);
		    $jhgrAtts["width"]         = ($instance['width']!=0 && isset($instance['width'])) ? absint($instance[ 'width' ]) : '';
		    $jhgrAtts["height"]        = ($instance['height']!=0 && isset($instance['height'])) ? absint($instance[ 'height' ]) : '';
		    $jhgrAtts["grbackground"]  = (isset($instance['grbackground'])) ? strip_tags($this->jhgrSanitizeHexColor($instance[ 'grbackground' ])) : '';
		    $jhgrAtts["grlinks"]       = (isset($instance['grlinks'])) ? strip_tags($this->jhgrSanitizeHexColor($instance[ 'grlinks' ])) : '';
		    $jhgrAtts["grstars"]       = (isset($instance['grstars'])) ? strip_tags($this->jhgrSanitizeHexColor($instance[ 'grstars' ])) : '';
		    $jhgrAtts["grtext"]        = (isset($instance['grtext'])) ? strip_tags($this->jhgrSanitizeHexColor($instance[ 'grtext' ])) : '';
		    $jhgrAtts["iswidget"]      = 'true';
		    $jhgrAtts["reviews"]       = 'off';
		    $jhgrAtts["bookinfo"]      = 'off';
		    
		    $jhgrshcd = new jhgrShortcode;
		    echo $jhgrshcd->jhgrParseShortcode($jhgrAtts);
		    unset($jhgrshcd);
		}

        echo $after_widget;
    }
 
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance = $new_instance;
        $instance['grbackground'] = $new_instance['grbackground'];
        return $instance;
    }
    
    function jhgrSanitizeHexColor($jhgrHexColor)
    {
        // Based on WP 4.0 alpha code
        $jhgrHexColor = ltrim($jhgrHexColor,'#');
        if(''===$jhgrHexColor)
            return '';
        if(preg_match('|^([A-Fa-f0-9]{3}){1,2}$|', $jhgrHexColor))
            return $jhgrHexColor;
        return null;
    }
 
    function form($instance) {
        $defaults = array(
            'itemvalue'    => '',
            'grbackground' => '#ffffff',
            'grlinks'      => '#000000',
            'itemtype'     => 'isbn',
            'width'        => '150',
            'height'       => '350'
        );
 
        // Merge the user-selected arguments with the defaults
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>
        <script>
            var elems = jQuery('#widgets-right .jhgr-color-picker, .inactive-sidebar .jhgr-color-picker');
            var widget_id = 'goodreviews-buybook';
            jQuery(document).ready(function($) {
                elems.wpColorPicker();
            }).ajaxComplete(function(e, xhr, settings) {
                if( settings.data.search('action=save-widget') != -1 && settings.data.search('id_base=' + widget_id) != -1 ) {  
                    elems.wpColorPicker();
                }
            });
        </script>
        <p>
            <span><?php _e( 'Book Identifier:', $this->textdomain ); ?></span><br />
            <select class="widefat" id="<?php echo $this->get_field_id( 'itemtype' ); ?>" name="<?php echo $this->get_field_name( 'itemtype' ); ?>">
                 <option value="isbn" <?php echo (esc_attr($instance['itemtype'])=='isbn') ? 'selected' : ''; ?>>ISBN</option>
                 <option value="grid" <?php echo (esc_attr($instance['itemtype'])=='grid') ? 'selected' : ''; ?>>Goodreads ID</option>
            </select><br />
            <span><?php _e( ' Identifer Value:', $this->textdomain ); ?></span><br />
            <input class="widefat" id="<?php echo $this->get_field_id( 'itemvalue' ); ?>" name="<?php echo $this->get_field_name( 'itemvalue' ); ?>" type="text" value="<?php echo esc_attr( $instance['itemvalue'] ); ?>" /><br />
            <span><?php _e( ' Width (in pixels):', $this->textdomain ); ?></span><br />
            <input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $instance['width'] ); ?>" /><br />
            <span><?php _e( ' Height (in pixels):', $this->textdomain ); ?></span><br />
            <input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $instance['height'] ); ?>" /><br />
            <span><?php _e( 'Background Color:', $this->textdomain ); ?></span><br />
            <input class="jhgr-color-picker" type="text" id="<?php echo $this->get_field_id( 'grbackground' ); ?>" name="<?php echo $this->get_field_name( 'grbackground' ); ?>" value="<?php echo esc_attr( $instance['grbackground'] ); ?>" /><br />                            
            <span><?php _e( 'Link Color:', $this->textdomain ); ?></span><br />
            <input class="jhgr-color-picker" type="text" id="<?php echo $this->get_field_id( 'grlinks' ); ?>" name="<?php echo $this->get_field_name( 'grlinks' ); ?>" value="<?php echo esc_attr( $instance['grlinks'] ); ?>" /><br />                         
        </p><?php
    }
}

class jhgrBookInfoWidget extends WP_Widget {
    var $textdomain;
 
     function __construct() 
    {
	    $this->textdomain = 'goodreviews';
 
   		// This is where we add the style and script
    	add_action( 'load-widgets.php', array(&$this, 'jhgrLoadBuyBookWidget') );
    	
	    parent::__construct(
		    'goodreviews-bookinfo', 
		    __('About This Book',$this->textdomain),
		    array( 'classname' => 'goodreviews-bookinfo',
		           'description' => __( 'Goodreads.com Book Information.',$this->textdomain), )
	    );
    }
 
    function jhgrLoadBuyBookWidget() {    
        wp_enqueue_style( 'wp-color-picker' );        
        wp_enqueue_script( 'wp-color-picker' );    
    }
 
    function widget($args, $instance) {
        extract( $args, EXTR_SKIP );
        $jhgrAtts = array();
        echo $before_widget;

		if( isset ($instance[ 'itemtype' ]) )
		{
		    if(empty($instance['itemtype']))
		    {
		        $instance['itemtype'] = 'isbn';
		    }
		    $jhgrAtts[strip_tags($instance['itemtype'])] = strip_tags($instance['itemvalue']);
		    $jhgrAtts["width"]         = ($instance['width']!=0 && isset($instance['width'])) ? absint($instance[ 'width' ]) : '';
		    $jhgrAtts["height"]        = ($instance['height']!=0 && isset($instance['height'])) ? absint($instance[ 'height' ]) : '';
		    $jhgrAtts["grbackground"]  = (isset($instance['grbackground'])) ? strip_tags($this->jhgrSanitizeHexColor($instance[ 'grbackground' ])) : '';
		    $jhgrAtts["grlinks"]       = (isset($instance['grlinks'])) ? strip_tags($this->jhgrSanitizeHexColor($instance[ 'grlinks' ])) : '';
		    $jhgrAtts["grstars"]       = (isset($instance['grstars'])) ? strip_tags($this->jhgrSanitizeHexColor($instance[ 'grstars' ])) : '';
		    $jhgrAtts["grtext"]        = (isset($instance['grtext'])) ? strip_tags($this->jhgrSanitizeHexColor($instance[ 'grtext' ])) : '';
            $jhgrAtts["cover"]         = (isset($instance['cover'])) ? strip_tags($instance['cover']) : '';
            $jhgrAtts["author"]        = (isset($instance['author'])) ? strip_tags($instance['author']) : '';
		    $jhgrAtts["iswidget"]      = 'true';
		    $jhgrAtts["buyinfo"]       = 'off';
		    $jhgrAtts["reviews"]       = 'off';
		    
		    $jhgrshcd = new jhgrShortcode;
		    echo $jhgrshcd->jhgrParseShortcode($jhgrAtts);
		    unset($jhgrshcd);
		}

        echo $after_widget;
    }
 
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance = $new_instance;
        $instance['grbackground'] = $new_instance['grbackground'];
        return $instance;
    }
    
    function jhgrSanitizeHexColor($jhgrHexColor)
    {
        // Based on WP 4.0 alpha code
        $jhgrHexColor = ltrim($jhgrHexColor,'#');
        if(''===$jhgrHexColor)
            return '';
        if(preg_match('|^([A-Fa-f0-9]{3}){1,2}$|', $jhgrHexColor))
            return $jhgrHexColor;
        return null;
    }
 
    function form($instance) {
        $defaults = array(
            'itemvalue'    => '',
            'grbackground' => '#ffffff',
            'grlinks'      => '#000000',
            'itemtype'     => 'isbn',
            'width'        => '350',
            'height'       => '500',
            'author'       => 'off',
            'grstars'      => '#1e73be',
            'cover'        => 'small'
        );
 
        // Merge the user-selected arguments with the defaults
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>
        <script>
            var elems = jQuery('#widgets-right .jhgr-color-picker, .inactive-sidebar .jhgr-color-picker');
            var widget_id = 'goodreviews-buybook';
            jQuery(document).ready(function($) {
                elems.wpColorPicker();
            }).ajaxComplete(function(e, xhr, settings) {
                if( settings.data.search('action=save-widget') != -1 && settings.data.search('id_base=' + widget_id) != -1 ) {  
                    elems.wpColorPicker();
                }
            });
        </script>
        <p>
            <span><?php _e( 'Book Identifier:', $this->textdomain ); ?></span><br />
            <select class="widefat" id="<?php echo $this->get_field_id( 'itemtype' ); ?>" name="<?php echo $this->get_field_name( 'itemtype' ); ?>">
                 <option value="isbn" <?php echo (esc_attr($instance['itemtype'])=='isbn') ? 'selected' : ''; ?>>ISBN</option>
                 <option value="grid" <?php echo (esc_attr($instance['itemtype'])=='grid') ? 'selected' : ''; ?>>Goodreads ID</option>
            </select><br />
            <span><?php _e( ' Identifer Value:', $this->textdomain ); ?></span><br />
            <input class="widefat" id="<?php echo $this->get_field_id( 'itemvalue' ); ?>" name="<?php echo $this->get_field_name( 'itemvalue' ); ?>" type="text" value="<?php echo esc_attr( $instance['itemvalue'] ); ?>" /><br />
            <span><?php _e( 'Cover Image:', $this->textdomain ); ?></span><br />
                <select class="widefat" id="<?php echo $this->get_field_id( 'cover' ); ?>" name="<?php echo $this->get_field_name( 'cover' ); ?>">
                 <option value="large" <?php echo (esc_attr($instance['cover'])=='large') ? 'selected' : ''; ?>>Large</option>
                 <option value="small" <?php echo (esc_attr($instance['cover'])=='small') ? 'selected' : ''; ?>>Small</option>
                 <option value="off" <?php echo (esc_attr($instance['cover'])=='off') ? 'selected' : ''; ?>>Off</option>
            </select><br />
            <span><?php _e( 'Author Image:', $this->textdomain ); ?></span><br />
                <select class="widefat" id="<?php echo $this->get_field_id( 'author' ); ?>" name="<?php echo $this->get_field_name( 'author' ); ?>">
                 <option value="large" <?php echo (esc_attr($instance['author'])=='large') ? 'selected' : ''; ?>>Large</option>
                 <option value="small" <?php echo (esc_attr($instance['author'])=='small') ? 'selected' : ''; ?>>Small</option>
                 <option value="off" <?php echo (esc_attr($instance['author'])=='off') ? 'selected' : ''; ?>>Off</option>
            </select><br />
            <span><?php _e( ' Width (in pixels):', $this->textdomain ); ?></span><br />
            <input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $instance['width'] ); ?>" /><br />
            <span><?php _e( ' Height (in pixels):', $this->textdomain ); ?></span><br />
            <input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $instance['height'] ); ?>" /><br />
            <span><?php _e( 'Background Color:', $this->textdomain ); ?></span><br />
            <input class="jhgr-color-picker" type="text" id="<?php echo $this->get_field_id( 'grbackground' ); ?>" name="<?php echo $this->get_field_name( 'grbackground' ); ?>" value="<?php echo esc_attr( $instance['grbackground'] ); ?>" /><br />                            
            <span><?php _e( 'Link Color:', $this->textdomain ); ?></span><br />
            <input class="jhgr-color-picker" type="text" id="<?php echo $this->get_field_id( 'grlinks' ); ?>" name="<?php echo $this->get_field_name( 'grlinks' ); ?>" value="<?php echo esc_attr( $instance['grlinks'] ); ?>" /><br />                         
            <span><?php _e( 'Text Color:', $this->textdomain ); ?></span><br />
            <input class="jhgr-color-picker" type="text" id="<?php echo $this->get_field_id( 'grtext' ); ?>" name="<?php echo $this->get_field_name( 'grtext' ); ?>" value="<?php echo esc_attr( $instance['grtext'] ); ?>" /><br />                         
            <span><?php _e( 'Star Rating Color:', $this->textdomain ); ?></span><br />
            <input class="jhgr-color-picker" type="text" id="<?php echo $this->get_field_id( 'grstars' ); ?>" name="<?php echo $this->get_field_name( 'grstars' ); ?>" value="<?php echo esc_attr( $instance['grstars'] ); ?>" /><br />                         
        </p><?php
    }
}

class jhgrReviewsWidget extends WP_Widget {
    var $textdomain;
 
     function __construct() 
    {
	    $this->textdomain = 'goodreviews';
 
   		// This is where we add the style and script
    	add_action( 'load-widgets.php', array(&$this, 'jhgrLoadBuyBookWidget') );
    	
	    parent::__construct(
		    'goodreviews-reviews', 
		    __('Reviews From Goodreads',$this->textdomain),
		    array( 'classname' => 'goodreviews-reviews',
		           'description' => __( 'Goodreads.com Book Reviews.',$this->textdomain), )
	    );
    }
 
    function jhgrLoadBuyBookWidget() {    
        wp_enqueue_style( 'wp-color-picker' );        
        wp_enqueue_script( 'wp-color-picker' );    
    }
 
    function widget($args, $instance) {
        extract( $args, EXTR_SKIP );
        $jhgrAtts = array();
        
        echo $before_widget;

		if( isset ($instance[ 'itemtype' ]) )
		{
		    if(empty($instance['itemtype']))
		    {
		        $instance['itemtype'] = 'isbn';
		    }
		    $jhgrAtts[strip_tags($instance['itemtype'])] = strip_tags($instance['itemvalue']);
		    $jhgrAtts["width"]         = ($instance['width']!=0 && isset($instance['width'])) ? absint($instance[ 'width' ]) : '';
		    $jhgrAtts["height"]        = ($instance['height']!=0 && isset($instance['height'])) ? absint($instance[ 'height' ]) : '';
		    $jhgrAtts["grbackground"]  = (isset($instance['grbackground'])) ? strip_tags($this->jhgrSanitizeHexColor($instance[ 'grbackground' ])) : '';
		    $jhgrAtts["grlinks"]       = (isset($instance['grlinks'])) ? strip_tags($this->jhgrSanitizeHexColor($instance[ 'grlinks' ])) : '';
		    $jhgrAtts["grstars"]       = (isset($instance['grstars'])) ? strip_tags($this->jhgrSanitizeHexColor($instance[ 'grstars' ])) : '';
		    $jhgrAtts["grtext"]        = (isset($instance['grtext'])) ? strip_tags($this->jhgrSanitizeHexColor($instance[ 'grtext' ])) : '';
		    $jhgrAtts["iswidget"]      = 'true';
		    $jhgrAtts["buyinfo"]       = 'off';
		    $jhgrAtts["bookinfo"]      = 'off';
		    
		    $jhgrshcd = new jhgrShortcode;
		    echo $jhgrshcd->jhgrParseShortcode($jhgrAtts);
		    unset($jhgrshcd);
		}

        echo $after_widget;
    }
 
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance = $new_instance;
        $instance['grbackground'] = $new_instance['grbackground'];
        return $instance;
    }
    
    function jhgrSanitizeHexColor($jhgrHexColor)
    {
        // Based on WP 4.0 alpha code
        $jhgrHexColor = ltrim($jhgrHexColor,'#');
        if(''===$jhgrHexColor)
            return '';
        if(preg_match('|^([A-Fa-f0-9]{3}){1,2}$|', $jhgrHexColor))
            return $jhgrHexColor;
        return null;
    }
 
    function form($instance) {
        $defaults = array(
            'itemvalue'    => '',
            'grbackground' => '#ffffff',
            'grlinks'      => '#000000',
            'itemtype'     => 'isbn',
            'width'        => '350',
            'height'       => '500',
            'grtext'       => '#000000',
            'grstars'      => '#000000'
        );
 
        // Merge the user-selected arguments with the defaults
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>
        <script>
            var elems = jQuery('#widgets-right .jhgr-color-picker, .inactive-sidebar .jhgr-color-picker');
            var widget_id = 'goodreviews-buybook';
            jQuery(document).ready(function($) {
                elems.wpColorPicker();
            }).ajaxComplete(function(e, xhr, settings) {
                if( settings.data.search('action=save-widget') != -1 && settings.data.search('id_base=' + widget_id) != -1 ) {  
                    elems.wpColorPicker();
                }
            });
        </script>
        <p>
            <span><?php _e( 'Book Identifier:', $this->textdomain ); ?></span><br />
            <select class="widefat" id="<?php echo $this->get_field_id( 'itemtype' ); ?>" name="<?php echo $this->get_field_name( 'itemtype' ); ?>">
                 <option value="isbn" <?php echo (esc_attr($instance['itemtype'])=='isbn') ? 'selected' : ''; ?>>ISBN</option>
                 <option value="grid" <?php echo (esc_attr($instance['itemtype'])=='grid') ? 'selected' : ''; ?>>Goodreads ID</option>
            </select><br />
            <span><?php _e( ' Identifer Value:', $this->textdomain ); ?></span><br />
            <input class="widefat" id="<?php echo $this->get_field_id( 'itemvalue' ); ?>" name="<?php echo $this->get_field_name( 'itemvalue' ); ?>" type="text" value="<?php echo esc_attr( $instance['itemvalue'] ); ?>" /><br />
            <span><?php _e( ' Width (in pixels):', $this->textdomain ); ?></span><br />
            <input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $instance['width'] ); ?>" /><br />
            <span><?php _e( ' Height (in pixels):', $this->textdomain ); ?></span><br />
            <input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $instance['height'] ); ?>" /><br />
            <span><?php _e( 'Background Color:', $this->textdomain ); ?></span><br />
            <input class="jhgr-color-picker" type="text" id="<?php echo $this->get_field_id( 'grbackground' ); ?>" name="<?php echo $this->get_field_name( 'grbackground' ); ?>" value="<?php echo esc_attr( $instance['grbackground'] ); ?>" /><br />                            
            <span><?php _e( 'Link Color:', $this->textdomain ); ?></span><br />
            <input class="jhgr-color-picker" type="text" id="<?php echo $this->get_field_id( 'grlinks' ); ?>" name="<?php echo $this->get_field_name( 'grlinks' ); ?>" value="<?php echo esc_attr( $instance['grlinks'] ); ?>" /><br />                         
            <span><?php _e( 'Text Color:', $this->textdomain ); ?></span><br />
            <input class="jhgr-color-picker" type="text" id="<?php echo $this->get_field_id( 'grtext' ); ?>" name="<?php echo $this->get_field_name( 'grtext' ); ?>" value="<?php echo esc_attr( $instance['grtext'] ); ?>" /><br />                         
            <span><?php _e( 'Star Rating Color:', $this->textdomain ); ?></span><br />
            <input class="jhgr-color-picker" type="text" id="<?php echo $this->get_field_id( 'grstars' ); ?>" name="<?php echo $this->get_field_name( 'grstars' ); ?>" value="<?php echo esc_attr( $instance['grstars'] ); ?>" /><br />                         
        </p><?php
    }
}

class jhgrShortcode
{
    public $jhgrGRWidget        = '';
    public $jhgrElementID       = '';
    
    public function jhgrIsSSL()
    {
        $jhgrSSL = (isset($_SERVER['HTTPS'])) || (is_ssl()) ? 'https://' : 'http://';
        return $jhgrSSL;
    }
    
    public function jhgrGetIDType($jhgrISBN,$jhgrGRID)
    {
        $jhgrItemType = '';
        
        if(! empty($jhgrGRID))  { $jhgrItemType = '/book/show?format=xml&id=' . $jhgrGRID; }
        if(! empty($jhgrISBN))  { $jhgrItemType = '/book/isbn?format=xml&isbn=' . $jhgrISBN; }
        
        return $jhgrItemType;
    }
    
    public function jhgrSanitizeHexColor($jhgrHexColor)
    {
        // Based on WP 4.0 alpha code
        $jhgrHexColor = ltrim($jhgrHexColor,'#');
        if(''===$jhgrHexColor)
            return '';
        if(preg_match('|^([A-Fa-f0-9]{3}){1,2}$|', $jhgrHexColor))
            return $jhgrHexColor;
        return null;
    }
    
    public function jhgrCallGoodreadsAPI($jhgrSCAtts)
    {
        $jhgrOpts      = new jhgrWPOptions;
        $jhgrRetries   = 0;
        $jhgrCCode    = "500";
        $jhgrCustomCSS = $jhgrOpts->jhgrGetCustomCSS();
        
        $jhgrURL  = $this->jhgrIsSSL() . 'www.goodreads.com' . $this->jhgrGetIDType($jhgrSCAtts["isbn"],$jhgrSCAtts["grid"]);
        $jhgrURL .= '&key=' . $jhgrOpts->jhgrGetAPIKey();
        $jhgrURL .= (isset($jhgrSCAtts["grminimum"])) ? '&min_rating=' . absint($jhgrSCAtts["grminimum"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["width"])) ? '&width=' . absint($jhgrSCAtts["width"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["height"])) ? '&height=' . absint($jhgrSCAtts["height"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["grlinks"])) ? '&links=' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grlinks"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["grstars"])) ? '&stars=' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grstars"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["grtext"])) ? '&text=' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grtext"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["grbackground"])) ? '&review_back=' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grbackground"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["grnumber"])) ? '&num_reviews=' . absint($jhgrSCAtts["grnumber"]) : '';
        $jhgrURL .= (! empty($jhgrCustomCSS)) ? '&stylesheet=' . esc_url($jhgrCustomCSS) : '';
        
        while(($jhgrRetries<5)&&(!preg_match("/200/",$jhgrCCode))) {
            usleep(500000*pow($jhgrRetries,2));
            $jhgrResponse = wp_remote_get(esc_url_raw($jhgrURL));
            $jhgrCCode = wp_remote_retrieve_response_code($jhgrResponse);
            if($jhgrCCode==200) 
            {
               $jhgrXML = wp_remote_retrieve_body($jhgrResponse);
            } else {
               $jhgrXML = '';
            }

            $jhgrRetries = $jhgrRetries + 1;
        }
        unset($jhgrOpts);

        return @simplexml_load_string($jhgrXML);
    }
    
    public function jhgrShowStars( $jhgrXML ) 
    {
        /* 
        This function is largely a copy of the WP 4.0 alpha wp_star_rating function.
        It has been placed here to avoid including the large template.php file at every
        page load. 
        */
        
        $args = array(
            'rating' => floatval($jhgrXML->book->average_rating),
            'type'   => 'rating',
            'number' => intval($jhgrXML->book->work->ratings_count),
        );
           
	    $defaults = array(
		    'rating' => 0,
		    'type' => 'rating',
		    'number' => 0,
	    );
	    $r = wp_parse_args( $args, $defaults );
	    extract( $r, EXTR_SKIP );

    	// Non-english decimal places when the $rating is coming from a string
	    $rating = str_replace( ',', '.', $rating );

    	// Convert Percentage to star rating, 0..5 in .5 increments
	    if ( 'percent' == $type ) {
		    $rating = round( $rating / 10, 0 ) / 2;
	    }

	    // Calculate the number of each type of star needed
	    $full_stars = floor( $rating );
	    $half_stars = ceil( $rating - $full_stars );
	    $empty_stars = 5 - $full_stars - $half_stars;

	    if ( $number ) {
		    /* translators: 1: The rating, 2: The number of ratings */
		    $title = _n( '%1$s rating based on %2$s rating (all editions)', '%1$s rating based on %2$s ratings (all editions)', $number );
		    $title = sprintf( $title, number_format_i18n( $rating, 1 ), number_format_i18n( $number ) );
	    } else {
		    /* translators: 1: The rating */
		    $title = sprintf( __( '%s rating' ), number_format_i18n( $rating, 1 ) );
	    }

	    $jhgrStars  = '<div class="star-rating" title="' . esc_attr( $title ) . '">';
	    $jhgrStars .= str_repeat( '<div class="star star-full"></div>', $full_stars );
	    $jhgrStars .= str_repeat( '<div class="star star-half"></div>', $half_stars );
 	    $jhgrStars .= str_repeat( '<div class="star star-empty"></div>', $empty_stars);
	    $jhgrStars .= '</div>';
	    $jhgrStars .= '<br>' . $title;	    
	    return $jhgrStars;
    }

    public function jhgrShowAuthors($jhgrSCAtts,$jhgrXML)
    {
        $jhgrAllAuthors = '';
        foreach ($jhgrXML->book->authors->author as $grauthor) {
            $jhgrAuthors = (strtolower($jhgrSCAtts["author"])=='small') ? '<div id="grauthorimage"><a href="'. esc_url($grauthor->link) . '"><img src="' . esc_url($grauthor->small_image_url) .'" /></a></div>' : '';
            $jhgrAuthors = (strtolower($jhgrSCAtts["author"])=='large') ? '<div id="grauthorimage"><a href="'. esc_url($grauthor->link) . '"><img src="' . esc_url($grauthor->image_url) . '" /></a></div>' : $jhgrAuthors;
            $jhgrAuthors .= '<div id="grauthorname"><a href="'. esc_url($grauthor->link) . '">'. $grauthor->name . '</a></div>';
            $jhgrAllAuthors .= $jhgrAuthors;
        }
        
        return $jhgrAllAuthors;
    }
    
    public function jhgrShowCover($jhgrSCAtts,$jhgrXML)
    {
        $jhgrBookOutput = '';
        if(strtolower($jhgrSCAtts["cover"])!='off')
        {
            $jhgrBookOutput .= '<div id="goodreviews-cover"';
            $jhgrBookOutput .= (strtolower($jhgrSCAtts["cover"])=='large') ? ' class="large"><img src="' . esc_url($jhgrXML->book->image_url) : '><img src="' . esc_url($jhgrXML->book->small_image_url);
            $jhgrBookOutput .= '" /></div>';
        }
        return $jhgrBookOutput;
    }

    public function jhgrShowBookInfo($jhgrSCAtts,$jhgrXML)
    {
    
        if(strtolower($jhgrSCAtts["bookinfo"]) != 'off')
        {
            $jhgrBookOutput  = '<div id="goodreviews-bookinfo">';
            $jhgrBookOutput .= '<label for="goodreviews-bookinfo" class="goodreviews-label">' . $jhgrXML->book->title . '</label>';
            $jhgrBookOutput .= '<div class="goodreviews-booklist">';
            $jhgrBookOutput .= $this->jhgrShowCover($jhgrSCAtts,$jhgrXML);
            $jhgrBookOutput .= '<div id="goodreviews-data">';
            $jhgrBookOutput .= '<b>';
            $jhgrBookOutput .= __('Average Rating','goodreads');
            $jhgrBookOutput .= ':</b><br>';
            $jhgrBookOutput .= $this->jhgrShowStars($jhgrXML);
            $jhgrBookOutput .= '<br /><br /><b>ISBN-10:</b> ' . $jhgrXML->book->isbn . '<br />' .
                               '<b>ISBN-13:</b> ' . $jhgrXML->book->isbn13 . '<br />' .
                               '<b>Goodreads:</b> <a href="' . esc_url($jhgrXML->book->link) . '">' . $jhgrXML->book->id . '</a><br /><br />' .
                               '<b>Author(s):</b> ';
            $jhgrBookOutput .= $this->jhgrShowAuthors($jhgrSCAtts,$jhgrXML);
            $jhgrBookOutput .= '<b>Publisher:</b> ' . $jhgrXML->book->publisher . '<br>' .
                               '<b>Published:</b> ' . $jhgrXML->book->publication_month . '/' . $jhgrXML->book->publication_day . '/' . $jhgrXML->book->publication_year . '<br><br>' .
                               $jhgrXML->book->description;
            $jhgrBookOutput .= '</div></div>';
            $jhgrBookOutput .= '<div class="goodreviews-creditl"><a href="http://www.goodreads.com">Information from Goodreads.com</a></div>';
            $jhgrBookOutput .= '</div>';
        }
        else
        {
            $jhgrBookOutput = '';
        }
        
        return $jhgrBookOutput;
    }
    
    public function jhgrShowBuyInfo($jhgrOnOff,$jhgrXML)
    {
        if (strtolower($jhgrOnOff) != 'off')
        {
            $jhgrBuyOutput  = '<div>';
            $jhgrBuyOutput .= '<label for "goodreviews-buybook" class="goodreviews-label">Buy This Book</label>';
            $jhgrBuyOutput .= '<div id="goodreviews-buybook">';
            $jhgrBuyOutput .= '<ul class="goodreviews-buylist">';

            foreach($jhgrXML->book->book_links->book_link as $buyme) {
                $jhgrBuyOutput .= '<li><a href="' . esc_url($buyme->link) . '?book_id=' . $jhgrXML->book->id . '" class="grbuy">' . $buyme->name . '</a></li>';
            }
      
            $jhgrBuyOutput .= '</ul>';
            $jhgrBuyOutput .= '</div>';
            $jhgrBuyOutput .= '<div class="goodreviews-credit"><a href="http://www.goodreads.com">Links from Goodreads.com</a></div>';
            $jhgrBuyOutput .= '</div>';
        }
        else
        {
            $jhgrBuyOutput =  '';
        } 
        
        return $jhgrBuyOutput;
    }
    
    public function jhgrShowReviews($jhgrSCAtts)
    {
        $jhgrOutput = '<div id="goodreviews-div">';
        $jhgrXML    = $this->jhgrCallGoodreadsAPI($jhgrSCAtts);
        
        $jhgrOutput .= $this->jhgrShowBookInfo($jhgrSCAtts,$jhgrXML);
        $jhgrOutput .= $this->jhgrShowBuyInfo($jhgrSCAtts["buyinfo"],$jhgrXML);
        $jhgrOutput .= ((strtolower($jhgrSCAtts["buyinfo"])!='off')||(strtolower($jhgrSCAtts["bookinfo"])!='off')) ? '<div class="goodreviews-clear">&nbsp;</div>' : '';
        if($jhgrSCAtts["reviews"]!='off')
        {
            $jhgrOutput .= preg_replace('~<style>.*?</style>~is','',$jhgrXML->book->reviews_widget, 1);
        }
        $jhgrOutput .= '</div>';
        return $jhgrOutput;
    }
    
    public function jhgrAddInlineStyles($jhgrSCAtts)
    {      
        $jhgrInlineStyles  = '<style type="text/css">';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["width"])) ? '#goodreviews-div, #goodreads-widget { width:' . absint($jhgrSCAtts["width"]) . 'px; } ' : '';       
        $jhgrInlineStyles .= (isset($jhgrSCAtts["height"])) ? '#the_iframe, #goodreviews-bookinfo, #goodreviews-data, #goodreviews-buybook  { height:' . absint($jhgrSCAtts["height"]) . 'px; } ' : '';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["grstars"])) ? '.star-rating .star { color:#' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grstars"]) . '; } ' : '';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["grlinks"])) ? '#goodreviews-div a,#goodreads-widget .gr_branding,.goodreviews-label { color:#' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grlinks"]) . '; } ' : '';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["grbackground"])) ? '#goodreviews-buybook,.goodreviews-booklist { background-color:#' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grbackground"]) . '; } ' : '';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["grtext"])) ? '.goodreviews-booklist { color:#' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grtext"]) . '; } ' : '';
        $jhgrInlineStyles .= '</style>';
        return $jhgrInlineStyles;
    }
    
    public function jhgrAddResponsiveInlineStyles($jhgrSCAtts)
    {
        $jhgrInlineStyles  = '<style type="text/css">';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["width"])) ? '#goodreviews-div, #goodreads-widget { width:100%; } ' : '';       
        $jhgrInlineStyles .= (isset($jhgrSCAtts["height"])) ? '#the_iframe, #goodreviews-bookinfo, #goodreviews-data, #goodreviews-buybook  { height:' . absint($jhgrSCAtts["height"]) . 'px; } ' : '';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["grstars"])) ? '.star-rating .star { color:#' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grstars"]) . '; } ' : '';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["grlinks"])) ? '#goodreviews-div a,#goodreads-widget .gr_branding,.goodreviews-label { color:#' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grlinks"]) . '; } ' : '';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["grbackground"])) ? '#goodreviews-buybook,.goodreviews-booklist { background-color:#' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grbackground"]) . '; } ' : '';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["grtext"])) ? '.goodreviews-booklist { color:#' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grtext"]) . '; } ' : '';
        $jhgrInlineStyles .= '</style>';
        return $jhgrInlineStyles;
    }
    
    public function jhgrTransientID($jhgrSCAtts)
    {
        $jhgrScreen     = (is_admin()) ? get_current_screen()->id : ''; 
        
        $jhgrTransient       = "jhgrT-";
        $jhgrTransientValues = '';
        
        if($jhgrScreen != 'admin_page_goodrev-tests')
        {
            $jhgrTransientValues  = implode('',$jhgrSCAtts);
            $jhgrTransient       .= wp_hash($jhgrTransientValues);
        } else {
            $jhgrTransient .= 'testpanel';
        }
        return $jhgrTransient; 
    }
    

public function jhgrDeferReviews()
{
    echo '<script type="text/javascript">' .
         '$jhgrQuery = jQuery.noConflict();' .
         ' $jhgrQuery(document).ready(function() {' .
         '    $jhgrQuery(\'#defergrreviews' . $this->jhgrElementID  . '\').append(\'' . str_replace('\'','\\\'',$this->jhgrGRWidget) . '\');' . 
         ' });' .
         '</script>';
}
    
public function jhgrParseShortcode($jhgrAtts)
{ 
        $jhgrOpts   = new jhgrWPOptions;
        $jhgrOutput = '';
        $jhgrTransientExpire = $jhgrOpts->jhgrGetCacheExpire();

        if($jhgrOpts->jhgrGetAgreement()==1)
        {
           $jhgrSCAtts = shortcode_atts( array(
                       'grid'         => '',
                       'isbn'         => '',
                       'border'       => 'off',
                       'width'        => '565',
                       'height'       => '400',
                       'bookinfo'     => 'on',
                       'buyinfo'      => 'on',
                       'cover'        => 'large',
                       'author'       => 'off',
                       'grstars'      => '000',
                       'grlinks'      => '660',
                       'grbackground' => 'fff',
                       'grtext'       => '382110',
                       'grnumber'     => '10',
                       'grminimum'    => '1',
                       'greditions'   => 'false',
                       'reviews'      => 'on',
                       'iswidget'     => 'false'
	         ), $jhgrAtts);
	         
	         $jhgrTransientID = $this->jhgrTransientID($jhgrSCAtts);
             $this->jhgrElementID   = '-' . $jhgrTransientID;

             if ((false === ($jhgrOut = get_transient($jhgrTransientID)))||($jhgrTransientID=='jhgrT-testpanel'))
             {
                  $jhgrOutput  = ($jhgrOpts->jhgrGetResponsive()=='1') ? $this->jhgrAddResponsiveInlineStyles($jhgrSCAtts) : $this->jhgrAddInlineStyles($jhgrSCAtts);
                  if($jhgrTransientID=='jhgrT-testpanel')
                  {
                      $jhgrOutput .= $this->jhgrShowReviews($jhgrSCAtts);
                  } else {
                      set_transient ($jhgrTransientID, $jhgrOutput . $this->jhgrShowReviews($jhgrSCAtts), $jhgrTransientExpire * HOUR_IN_SECONDS );
                      $jhgrOutput .= get_transient($jhgrTransientID);
                  }
             } else {
                  $jhgrOutput = get_transient($jhgrTransientID);
             }
         }
         else
         {
             $jhgrOutput = '<div id="goodreviews-output"><div id="goodreviews-error">' . __('You must allow GoodReviews to display Goodreads.com links on your site in order to use this plugin. Please review the GoodReviews Settings page.') . '</div></div>'; 
         }
         
         if(($jhgrOpts->jhgrGetDeferParse()=='1')&&($jhgrTransientID!='jhgrT-testpanel'))
         {
             $this->jhgrGRWidget = $jhgrOutput;
             $this->jhgrGRWidget = str_replace(array("\n", "\t", "\r"), '', $this->jhgrGRWidget);
             add_action('wp_footer', array(&$this,'jhgrDeferReviews'),100);
             return '<div id="defergrreviews' . $this->jhgrElementID . '"></div>';
         } else {
             return $jhgrOutput;
         }
     }
}

?>