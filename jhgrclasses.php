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
    public $jhgrCurlEnabled    = 'Client URL (cURL) is enabled on your server. GoodReviews can use it to retrieve reviews.';
    public $jhgrCurlDisabled   = 'Client URL (cURL) is either <a href="http://us2.php.net/manual/en/curl.setup.php">not installed or not enabled</a> on your server. GoodReviews might not be able to retrieve reviews.';
    public $jhgrFileGetEnabled = 'PHP fopen wrappers (file_get_contents) are enabled on your server. For security, <a href="http://www.php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen" target="_blank">disable fopen wrappers</a> and <a href="http://us2.php.net/manual/en/curl.setup.php">use cURL</a> instead.';
    public $jhgrNoRetrieval    = 'Neither client URL (cURL) nor fopen wrappers (file_get_contents) are enabled on your server. GoodReviews will not be able to retrieve reviews.';

    public function jhgrLoadLocal()
    {
        load_plugin_textdomain('goodreviews',false,basename(dirname(__FILE__)).'/lang');
    }

    public function jhgrCurlCheck()
    {
        return (in_array('curl',get_loaded_extensions())) ? true : false;
    }
    
    public function jhgrCurlExecCheck($jhgrDisabled)
    {
        $jhgrDisabled = explode(', ', ini_get('disable_functions'));
        return !in_array('curl_exec', $jhgrDisabled);
    }
    
    public function jhgrFileGetCheck()
    {
        return (ini_get('allow_url_fopen')) ? true : false;
    }
    
    public function jhgrRestoreNotices()
    {
        $jhgrUser = wp_get_current_user();
        if(isset($_GET['restore_jhgrNotices']) && '1' == $_GET['restore_jhgrNotices'])
        {
            delete_user_meta($jhgrUser->ID, 'goodreviews_ignore_FileGetEnabled','true');
            delete_user_meta($jhgrUser->ID, 'goodreviews_ignore_CurlEnabled','true');
            delete_user_meta($jhgrUser->ID, 'goodreviews_ignore_CurlDisabled','true');
        }
    }

    public function jhgrHideNotices()
    {
        $jhgrUser = wp_get_current_user();
        if(isset($_GET['ignore_FileGetEnabled']) && '0' == absint($_GET['ignore_FileGetEnabled']))
        {
            add_user_meta($jhgrUser->ID, 'goodreviews_ignore_FileGetEnabled','true',true);
        }
        if(isset($_GET['ignore_CurlEnabled']) && '0' == absint($_GET['ignore_CurlEnabled']))
        {
            add_user_meta($jhgrUser->ID, 'goodreviews_ignore_CurlEnabled','true',true);
        }
        if(isset($_GET['ignore_CurlDisabled']) && '0' == absint($_GET['ignore_CurlDisabled']))
        {
            add_user_meta($jhgrUser->ID, 'goodreviews_ignore_CurlDisabled','true',true);
        }
    }
        
    public function jhgrShowNotices()
    {
        $jhgrScreenID = get_current_screen()->id;
        $jhgrUser     = wp_get_current_user();
        
        if ('1' == absint($_GET['restore_jhgrNotices'])) 
        {
            $this->jhgrRestoreNotices();
        }
        
        if($jhgrScreenID == 'settings_page_goodrev-options') {
            if((!($this->jhgrCurlExecCheck($jhgrDisabled)))&&(!($this->jhgrFileGetCheck())))
            {
                printf('<div class="error"><p>%s</p></div>', __($this->jhgrNoRetrieval,'goodreviews'),'GoodReviews');
            }
            if ( ! get_user_meta($jhgrUser->ID, 'goodreviews_ignore_FileGetEnabled') ) {
                if($this->jhgrFileGetCheck())
                {
                    printf('<div class="error"><p>' . __($this->jhgrFileGetEnabled,'goodreviews') . ' | <a href="%1$s">' . __('Hide Notice','goodreviews') . '</a></p></div>', '?page=goodrev-options&ignore_FileGetEnabled=0');
                }
            }
            if ( ! get_user_meta($jhgrUser->ID, 'goodreviews_ignore_CurlEnabled') ) {
                if(($this->jhgrCurlExecCheck($jhgrDisabled))&&($this->jhgrCurlCheck()))
                {
                    printf('<div class="updated"><p>' . __($this->jhgrCurlEnabled,'goodreviews') . ' | <a href="%1$s">' . __('Hide Notice','goodreviews') . '</a></p></div>', '?page=goodrev-options&ignore_CurlEnabled=0');
                }
            }
            if ( ! get_user_meta($jhgrUser->ID, 'goodreviews_ignore_CurlDisabled') ) {
                if((!($this->jhgrCurlExecCheck($jhgrDisabled)))||((!($this->jhgrCurlCheck()))))
                {
                    printf('<div class="updated"><p>' . __($this->jhgrCurlDisabled,'goodreviews') . ' | <a href="%1$s">' . __('Hide Notice','goodreviews') . '</a></p></div>', '?page=goodrev-options&ignore_CurlDisabled=0');
                }
            }
        }
    }
}

class jhgrWPOptions
{
    public $jhgrGoodreadsAPIKey = '';
    public $jhgrGoodreviewsCSS  = '';
    public $jhgrRetrieveMethod  = '';
    public $jhgrTermsAgreement  = '1';
    public $jhgrResponsiveStyle = '0';
    
    public function jhgrAddHelp($jhgrContextHelp)
    {
        $jhgrOverview     = '<p>' .
                          __('The GoodReviews plugin retrieves Goodreads.com reader reviews for books you choose and displays them in pages or posts on your WordPress blog by way of a WordPress shortcode.','goodreviews') .
                          '</p> <p>' .
                          __('You must have a Goodreads API Developer Key in order to use this plugin. Links to Goodreads.com information about this program are available on the GoodReviews Settings page.','goodreviews') .
                          '</p> <p><strong>' .
                          __('WARNING','goodreviews') .
                          '</strong>: ' .
                          __('If your PHP implementation does not have client URL (cURL) installed and enabled, you should not attempt to use this plugin. You might be able to use the plugin without cURL if your PHP implementation has fopen wrappers enabled. However, fopen wrappers can be a security risk.','goodreviews') .
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
                          '</li><li><strong>' . __('Use file_get_contents','goodreviews') . '</strong>: ' .
                          __('If cURL is enabled on your site, DO NOT select this checkbox. If your site does not support cURL, you can select this checkbox to use fopen wrappers instead. However, fopen wrappers are a security risk. Consider installing cURL.','goodreviews') .
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
                          '</li><li><code>grheader</code>: ' .
                          __('defines the header text that appears above the reviews element. Default is the book title.','goodreviews') .
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

    public function jhgrRegisterSettings() 
    {
       register_setting('goodrev_options','goodreviews-api-key',array(&$this,'jhgrSetAPIKey'));
       register_setting('goodrev_options','goodreviews-getmethod',array(&$this,'jhgrSetRetrieveMethod'));
       register_setting('goodrev_options','goodreviews-agree',array(&$this,'jhgrSetAgreement'));
       register_setting('goodrev_options','goodreviews-alt-style',array(&$this,'jhgrSetCustomCSS'));
       register_setting('goodrev_options','goodreviews-responsive-style',array(&$this,'jhgrSetResponsive'));
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

    public function jhgrRestoreNoticesField($args)
    {
        $jhgrField  = '<a href="?page=goodrev-options&restore_jhgrNotices=1" name="goodreviews-restore-notices" id="goodreviews-restore-notices">' . __('Restore Hidden GoodReviews Notices','goodreviews') . '</a><br />';
        $jhgrField .= '<label for="goodreviews-restore-notices"> ' . sanitize_text_field($args[0]) . '</label>';
        echo $jhgrField;
    }
    
    public function jhgrGRTestField($args)
    {
        echo do_shortcode('[goodreviews isbn="1451627289" buyinfo="off" bookinfo="off"]');
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
              __('If your site uses a responsive theme, you can enable a default GoodReviews stylesheet that includes responsive elements. If you enable this option, any Custom CSS URL you have specific above will be ignored.','goodreviews')
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
            'goodreviews-getmethod',
            __('Use file_get_contents<br><span style="color:red">(Not Recommended)</span>','goodreviews'),
            array(&$this, 'jhgrRetrieveMethodField'),
            'goodrev-options',
            'goodreviews_retrieval_section',
            array(
                __('Select this checkbox to use fopen wrappers if your host does not support cURL (not recommended).','goodreviews')
            )
        );
        
        add_settings_field(
            'goodreviews-restore-notices',
            __('Restore Notices','goodreviews'),
            array(&$this, 'jhgrRestoreNoticesField'),
            'goodrev-options',
            'goodreviews_retrieval_section',
            array(
                __('Click this link to restore any important GoodReviews Settings notifications you might have hidden.','goodreviews')
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
    
    public function jhgrUsageCallback()
    {
        echo '<p><b>' . __('Shortcode','goodreviews') .'</b>: <code>[goodreviews isbn="<i>isbn</i>"]</code></p>';

        $jhgr1      = __('Insert the above shortcode into any page or post where you want Goodreads.com customer reviews to appear. Replace ','goodreviews');
        $jhgr2      = __(' with the International Standard Book Number (ISBN) to retrieve and display the reviews for that product.','goodreviews');
        $jhgr3      = __('For a more detailed and complete overview of how GoodReviews works, click the "Help" tab on the upper right of the GoodReviews settings page.','goodreviews');
        $jhgrFormat = '<p>%s<code><i>isbn</i></code>%s</p><p>%s</p>';

        printf($jhgrFormat,$jhgr1,$jhgr2,$jhgr3);
    }
    
    public function jhgrOptionsLink($jhgrLink) 
    {
        $jhgrSettingsLink  = '<a href="' . admin_url() . 'admin.php?page=goodrev-options">' . __('Settings','goodreviews') . '</a> | ';
        $jhgrSettingsLink .= '<a href="' . admin_url() . 'admin.php?page=goodrev-tests">' . __('Test','goodreviews') . '</a>';
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
            case 'Usage':
                 $_GET['tab'] = 'goodreviews_usage_section';
                 break;
        }
        // Settings navigation tabs
        if( isset( $_GET[ 'tab' ] ) ) {
            $active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field($_GET[ 'tab' ]) : 'goodreviews_retrieval_section';
        }
        
        echo '<h2 class="nav-tab-wrapper"><a href="' . admin_url() .'admin.php?page=goodrev-options&tab=goodreviews_retrieval_section" class="nav-tab ';
        echo $active_tab == 'goodreviews_retrieval_section' ? 'nav-tab-active' : '';
        echo '">GoodReviews</a><a href="' . admin_url() .'admin.php?page=goodrev-tests&tab=goodreviews_test_section" class="nav-tab ';
        echo $active_tab == 'goodreviews_test_section' ? 'nav-tab-active' : '';
        echo '">Tests</a><a href="' . admin_url() .'admin.php?page=goodrev-usages&tab=goodreviews_usage_section" class="nav-tab ';
        echo $active_tab == 'goodreviews_usage_section' ? 'nav-tab-active' : '';
        echo '">Usage</a></h2>';
        
        // Settings form
        echo '<form method="post" action="options.php">';

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
            'goodreviews_usage_section',
            __('GoodReviews Usage','goodreviews'),
            array(&$this, 'jhgrUsageCallback'),
            'goodrev-usages'
        );
        
        // Create settings fields
        $this->jhgrGetOptionsForm();
        settings_fields('goodrev_options');
        
        switch($active_tab)
        {
            case 'goodreviews_retrieval_section':
                 do_settings_sections('goodrev-options');
                 echo get_submit_button();
                 break;
            case 'goodreviews_test_section':
                 do_settings_sections('goodrev-tests');
                 break;
            case 'goodreviews_usage_section':
                 do_settings_sections('goodrev-usages');
                 break;
        }
        
        echo '</form>';
    }
    
    public function jhgrAddAdminPage() 
    {
        $jhgrOptionsPage = add_submenu_page('options-general.php','GoodReviews','GoodReviews','manage_options','goodrev-options',array(&$this, 'jhgrGetOptionsScreen'));
        $jhgrTestingPage = add_submenu_page('goodrev-options','Tests','Tests','manage_options','goodrev-tests',array(&$this, 'jhgrGetOptionsScreen'));
        $jhgrUsingPage   = add_submenu_page('goodrev-options','Usage','Usage','manage_options','goodrev-usages',array(&$this, 'jhgrGetOptionsScreen'));    
        add_action('load-' . $jhgrOptionsPage, array(&$this, 'jhgrAddHelp'));
        add_action('load-' . $jhgrTestingPage, array(&$this, 'jhgrAddHelp'));
        add_action('load-' . $jhgrUsingPage, array(&$this, 'jhgrAddHelp'));       
    }
    
    public function jhgrRequireStyles()
    {
        $this->jhgrDequeueStyles();

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
        
        return true;
    }
    
    public function jhgrDequeueStyles()
    {
        wp_dequeue_style('goodrev-styles');
        wp_deregister_style('goodrev-styles');
        wp_dequeue_style('dashicons');
    }
}

class jhgrBuyBookWidget extends WP_Widget {
    var $textdomain;
 
    function __construct() {
        $this->textdomain = 'goodreviews';
 
        // This is where we add the style and script
        add_action( 'load-widgets.php', array(&$this, 'jhgrLoadBuyBookWidget') );
 
        $this->WP_Widget( 
            'goodreviews-buybook', 
            'Buy This Book', 
            array( 'classname' => 'goodreviews-buybook', 'description' => 'Goodreads.com Buy Book Links' )
        );
    }
 
    function jhgrLoadBuyBookWidget() {    
        wp_enqueue_style( 'wp-color-picker' );        
        wp_enqueue_script( 'wp-color-picker' );    
    }
 
    function widget($args, $instance) {
        extract( $args, EXTR_SKIP );
        echo $before_widget;

		if( isset ($instance[ 'itemtype' ]) )
		{
		    if(empty($instance['itemtype']))
		    {
		        $instance['itemtype'] = 'isbn';
		    }
		    $jhgrSCode = '[goodreviews ' . strip_tags($instance['itemtype']) . '="' . strip_tags($instance['itemvalue']) . '"';
		    if(($instance['width']!=0) && isset ($instance[ 'width' ]) )
		    {
		        $jhgrSCode .= ' width="' . absint($instance[ 'width' ]) . '"';
		    }
		    if(($instance['height']!=0) && isset ($instance[ 'height' ]) )
		    {
		        $jhgrSCode .= ' height="' . absint($instance[ 'height' ]) . '"';
		    }
		    if(isset ($instance[ 'grbackground' ]) )
		    {
		        $jhgrSCode .= ' grbackground="' . strip_tags($this->jhgrSanitizeHexColor($instance[ 'grbackground' ])) . '"';
		    }
		    if(isset ($instance[ 'grlinks' ]) )
		    {
		        $jhgrSCode .= ' grlinks="' . strip_tags($this->jhgrSanitizeHexColor($instance[ 'grlinks' ])) . '"';
		    }
		    $jhgrSCode .= ' bookinfo="off" reviews="off"]';
		    echo do_shortcode($jhgrSCode);
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
 
    function __construct() {
        $this->textdomain = 'goodreviews';
 
        // This is where we add the style and script
        add_action( 'load-widgets.php', array(&$this, 'jhgrLoadBuyBookWidget') );
 
        $this->WP_Widget( 
            'goodreviews-bookinfo', 
            'About This Book', 
            array( 'classname' => 'goodreviews-bookinfo', 'description' => 'Goodreads.com Book Information' )
        );
    }
 
    function jhgrLoadBuyBookWidget() {    
        wp_enqueue_style( 'wp-color-picker' );        
        wp_enqueue_script( 'wp-color-picker' );    
    }
 
    function widget($args, $instance) {
        extract( $args, EXTR_SKIP );
        echo $before_widget;

		if( isset ($instance[ 'itemtype' ]) )
		{
		    if(empty($instance['itemtype']))
		    {
		        $instance['itemtype'] = 'isbn';
		    }
		    $jhgrSCode = '[goodreviews ' . strip_tags($instance['itemtype']) . '="' . strip_tags($instance['itemvalue']) . '"';
		    if(($instance['width']!=0) && isset ($instance[ 'width' ]) )
		    {
		        $jhgrSCode .= ' width="' . absint($instance[ 'width' ]) . '"';
		    }
		    if(($instance['height']!=0) && isset ($instance[ 'height' ]) )
		    {
		        $jhgrSCode .= ' height="' . absint($instance[ 'height' ]) . '"';
		    }
		    if(isset ($instance[ 'grbackground' ]) )
		    {
		        $jhgrSCode .= ' grbackground="' . strip_tags($this->jhgrSanitizeHexColor($instance[ 'grbackground' ])) . '"';
		    }
		    if(isset ($instance[ 'grlinks' ]) )
		    {
		        $jhgrSCode .= ' grlinks="' . strip_tags($this->jhgrSanitizeHexColor($instance[ 'grlinks' ])) . '"';
		    }
		    if(isset ($instance[ 'grtext' ]) )
		    {
		        $jhgrSCode .= ' grtext="' . strip_tags($this->jhgrSanitizeHexColor($instance[ 'grtext' ])) . '"';
		    }

		    if(isset ($instance[ 'grstars' ]) )
		    {
		        $jhgrSCode .= ' grstars="' . strip_tags($this->jhgrSanitizeHexColor($instance[ 'grstars' ])) . '"';
		    }
		    if(isset ($instance[ 'cover' ]) )
		    {
		        $jhgrSCode .= ' cover="' . strip_tags($instance[ 'cover' ]) . '"';
		    }
		    if(isset ($instance[ 'author' ]) )
		    {
		        $jhgrSCode .= ' author="' . strip_tags($instance[ 'author' ]) . '"';
		    }
		    $jhgrSCode .= ' buyinfo="off" reviews="off"]';
		    echo do_shortcode($jhgrSCode);
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
 
    function __construct() {
        $this->textdomain = 'goodreviews';
 
        // This is where we add the style and script
        add_action( 'load-widgets.php', array(&$this, 'jhgrLoadBuyBookWidget') );
 
        $this->WP_Widget( 
            'goodreviews-reviews', 
            'Reviews From Goodreads', 
            array( 'classname' => 'goodreviews-reviews', 'description' => 'Goodreads.com Book Reviews' )
        );
    }
 
    function jhgrLoadBuyBookWidget() {    
        wp_enqueue_style( 'wp-color-picker' );        
        wp_enqueue_script( 'wp-color-picker' );    
    }
 
    function widget($args, $instance) {
        extract( $args, EXTR_SKIP );
        echo $before_widget;

		if( isset ($instance[ 'itemtype' ]) )
		{
		    if(empty($instance['itemtype']))
		    {
		        $instance['itemtype'] = 'isbn';
		    }
		    $jhgrSCode = '[goodreviews ' . strip_tags($instance['itemtype']) . '="' . strip_tags($instance['itemvalue']) . '"';
		    if(($instance['width']!=0) && isset ($instance[ 'width' ]) )
		    {
		        $jhgrSCode .= ' width="' . absint($instance[ 'width' ]) . '"';
		    }
		    if(($instance['height']!=0) && isset ($instance[ 'height' ]) )
		    {
		        $jhgrSCode .= ' height="' . absint($instance[ 'height' ]) . '"';
		    }
		    if(isset ($instance[ 'grbackground' ]) )
		    {
		        $jhgrSCode .= ' grbackground="' . strip_tags($this->jhgrSanitizeHexColor($instance[ 'grbackground' ])) . '"';
		    }
		    if(isset ($instance[ 'grlinks' ]) )
		    {
		        $jhgrSCode .= ' grlinks="' . strip_tags($this->jhgrSanitizeHexColor($instance[ 'grlinks' ])) . '"';
		    }
		    if(isset ($instance[ 'grtext' ]) )
		    {
		        $jhgrSCode .= ' grtext="' . strip_tags($this->jhgrSanitizeHexColor($instance[ 'grtext' ])) . '"';
		    }
		    if(isset ($instance[ 'grstars' ]) )
		    {
		        $jhgrSCode .= ' grstars="' . strip_tags($this->jhgrSanitizeHexColor($instance[ 'grstars' ])) . '"';
		    }
		    $jhgrSCode .= ' buyinfo="off" bookinfo="off"]';
		    echo do_shortcode($jhgrSCode);
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
    public function jhgrIsSSL()
    {
        $jhgrSSL = (isset($_SERVER['HTTPS'])) || (is_ssl()) ? 'https://' : 'http://';
        return $jhgrSSL;
    }
    
    public function jhgrGetIDType($jhgrISBN,$jhgrGRID)
    {
        $jhgrItemType = '';
        
        if(isset($jhgrISBN))  { $jhgrItemType = '/book/isbn?format=xml&isbn=' . $jhgrISBN; }
        if(isset($jhgrGRID))  { $jhgrItemType = '/book/show?format=xml&id=' . $jhgrGRID; }
        
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
        $jhgrSCCode    = "500";
        $jhgrCustomCSS = $jhgrOpts->jhgrGetCustomCSS();
        
        $jhgrURL  = $this->jhgrIsSSL . 'www.goodreads.com' . $this->jhgrGetIDType($jhgrSCAtts["isbn"],$jhgrSCAtts["grid"]);
        $jhgrURL .= '&key=' . $jhgrOpts->jhgrGetAPIKey();
        $jhgrURL .= (isset($jhgrSCAtts["grminimum"])) ? '&min_rating=' . absint($jhgrSCAtts["grminimum"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["width"])) ? '&width=' . absint($jhgrSCAtts["width"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["height"])) ? '&height=' . absint($jhgrSCAtts["height"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["grlinks"])) ? '&links=' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grlinks"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["grstars"])) ? '&stars=' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grstars"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["grtext"])) ? '&text=' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grtext"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["grbackground"])) ? '&review_back=' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grbackground"]) : '';
        $jhgrURL .= (isset($jhgrSCAtts["grnumber"])) ? '&num_reviews=' . absint($jhgrSCAtts["grnumber"]) : '';
        $jhgrURL .= (isset($jhgrCustomCSS)) ? '&stylesheet=' . esc_url($jhgrCustomCSS) : '';
        
        while(($jhgrRetries<5)&&($jhgrCCode != "200")) {
            usleep(500000*pow($jhgrRetries,2));
            if($jhgrOpts->jhgrGetRetrieveMethod()==1)
            {
                $jhgrXML = file_get_contents($jhgrURL);
            }
            else 
            {
                $jhgrCurl = curl_init();
                curl_setopt($jhgrCurl, CURLOPT_URL, $jhgrURL);
                curl_setopt($jhgrCurl, CURLOPT_RETURNTRANSFER, true);
                $jhgrXML = curl_exec($jhgrCurl);
                $jhgrCCode = curl_getinfo($jhgrCurl,CURLINFO_HTTP_CODE);
                curl_close($jhgrCurl);
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
            'number' => intval($jhgrXML->book->ratings_count),
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
		    $title = _n( '%1$s rating based on %2$s rating', '%1$s rating based on %2$s ratings', $number );
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
            $jhgrAuthors = (strtolower($jhgrSCAtts["author"])=='small') ? '<div id="grauthorimage"><a href="'. $grauthor->link . '"><img src="' . $grauthor->small_image_url .'" /></a></div>' : '';
            $jhgrAuthors = (strtolower($jhgrSCAtts["author"])=='large') ? '<div id="grauthorimage"><a href="'. $grauthor->link . '"><img src="' . $grauthor->image_url . '" /></a></div>' : $jhgrAuthors;
            $jhgrAuthors .= '<div id="grauthorname"><a href="'. $grauthor->link . '">'. $grauthor->name . '</a></div>';
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
            $jhgrBookOutput .= (strtolower($jhgrSCAtts["cover"])=='large') ? ' class="large"><img src="' . $jhgrXML->book->image_url : '><img src="' . $jhgrXML->book->small_image_url;
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
                               '<b>Goodreads:</b> <a href="' . $jhgrXML->book->link . '">' . $jhgrXML->book->id . '</a><br /><br />' .
                               '<b>Author(s):</b> ';
            $jhgrBookOutput .= $this->jhgrShowAuthors($jhgrSCAtts,$jhgrXML);
            $jhgrBookOutput .= '<b>Publisher:</b> ' . $jhgrXML->book->publisher . '<br>' .
                               '<b>Published:</b> ' . $jhgrXML->book->publication_month . '/' . $jhgrXML->book->publication_day . '/' . $jhgrXML->book->publication_year . '<br><br>' .
                               $jhgrXML->book->description;
            $jhgrBookOutput .= '</div></div>';
            $jhgrBookOutput .= '<div class="goodreviews-credit"><a href="http://www.goodreads.com">Information from Goodreads.com</a></div>';
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
                $jhgrBuyOutput .= '<li><a href="' . $buyme->link . '?book_id=' . $jhgrXML->book->id . '" class="grbuy">' . $buyme->name . '</a></li>';
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
        $jhgrInlineStyles .= (isset($jhgrSCAtts["height"])) ? '#goodreviews-bookinfo, #goodreviews-data, #goodreviews-buybook  { height:' . absint($jhgrSCAtts["height"]) . 'px; } ' : '';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["grstars"])) ? '.star-rating .star { color:#' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grstars"]) . '; } ' : '';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["bookinfo"])) ? '#goodreviews-buybook { width: 100%; } ' : '';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["buyinfo"])) ? '#goodreviews-bookinfo { width: 100%; }' : ''; 
        $jhgrInlineStyles .= (isset($jhgrSCAtts["grlinks"])) ? '#goodreviews-div a,#goodreads-widget .gr_branding,.goodreviews-label { color:#' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grlinks"]) . '; } ' : '';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["grbackground"])) ? '#goodreviews-buybook,.goodreviews-booklist { background-color:#' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grbackground"]) . '; } ' : '';
        $jhgrInlineStyles .= (isset($jhgrSCAtts["grtext"])) ? '.goodreviews-booklist { color:#' . $this->jhgrSanitizeHexColor($jhgrSCAtts["grtext"]) . '; } ' : '';
        $jhgrInlineStyles .= '</style>';
        return $jhgrInlineStyles;
    }
    
    public function jhgrParseShortcode($jhgrSCAtts)
    { 
        $jhgrOpts = new jhgrWPOptions;
        if($jhgrOpts->jhgrGetAgreement()==1)
        {
            extract( shortcode_atts( array(
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
                 'reviews'      => 'on'
	         ), $jhgrSCAtts) );
	         
	         $jhgrOutput  = $this->jhgrAddInlineStyles($jhgrSCAtts);
             $jhgrOutput .= $this->jhgrShowReviews($jhgrSCAtts);
        }
        else
        {
             $jhgrOutput = '<div id="goodreviews-output"><div id="goodreviews-error">' . __('You must allow GoodReviews to display Goodreads.com links on your site in order to use this plugin. Please review the GoodReviews Settings page.') . '</div></div>'; 
        }
        
        return $jhgrOutput;
    }
}

?>