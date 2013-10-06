<?php
function gr_check_for_curl() {
   if(in_array('curl',get_loaded_extensions())) {
      return true;
   } else {
      return false;
   }
}
function gr_check_for_fopen() {
   if(ini_get('allow_url_fopen')) {
      return true;
   } else {
      return false;
   }
}
function gr_curl_exec_enabled() {
  $disabled = explode(', ', ini_get('disable_functions'));
  return !in_array('curl_exec', $disabled);
}
/* 
   The following function was adapted from 
   http://bavotasan.com/2009/a-settings-link-for-your-wordpress-plugins/ 
*/
function goodreviews_settings_link($links) {
  $settings_link = '<a href="options-general.php?page=goodrev-options">Settings</a>';
  array_unshift($links,$settings_link);
  return $links;
  
}
function goodreviews_admin_add_page() {
   add_options_page('GoodReviews Settings','GoodReviews','manage_options','goodrev-options','goodreviews_options');
}

function goodreviews_deregister_styles() {
  if(preg_match('/^(http|https):\/\//i',get_option('goodreviews-alt-style'))) {
    wp_dequeue_style('gr-alternate-style');
  } else {
    // Deegister default stylesheet
    wp_dequeue_style('gr-default-style');
  }
}

function goodreviews_options() {
   if(!current_user_can('manage_options')) {
      wp_die( __('You do not have sufficient permissions to access this page.'));
   }
   echo '<div class="wrap">';
   echo '<h2>GoodReviews Settings</h2>';
   echo '<form method="post" action="options.php">';
   settings_fields('goodreviews_options');
?>


<p>In order to access review data from Goodreads.com, you must have a Goodreads.com <a href="http://www.goodreads.com/api/">API developer key</a> and abide by the <a href="http://www.goodreads.com/api/terms">Goodreads API Terms and Conditions</a>.</p>

<table border="0">
<tr>
<td valign="top" align="left" width="150"><input type="checkbox" name="goodreviews-agree" id="goodreviews-agree" value="on" <?php if(get_option('goodreviews-agree')) { echo get_option('goodreviews-agree'); } else { add_option('goodreviews-agree','unchecked'); } ?> /></td>
<td valign="top" align="left" width="300">You MUST agree to allow GoodReviews to display Goodreads.com links on your site in order to use this plugin. Select this checkbox to agree.</td>
</tr>
<tr><td valign="top" align="left" width="300">Goodreads API Developer Key </td>
<td valign="top" align="left" width="150"><input type="text" name="goodreviews-api-key" id="goodreviews-api-key" size="80" value="<?php echo get_option('goodreviews-api-key');?>" /></td>
</tr>
<tr>
<td valign="top" align="left" width="150">Requirements Check</td>
<td valign="top" align="left">
<?php
$green = "<strong>cURL Status:</strong> ";
$red = "<strong>file_get_contents Status:</strong> ";
if((gr_check_for_curl())&&(gr_curl_exec_enabled())) {
   $green .= '<span style="color:green">Available and enabled</span>';
} elseif ((gr_check_for_curl())&&(!(gr_curl_exec_enabled()))) {
   $green .= '<span style="color:red">Available, but not enabled</span>';
} else {
   $green = '<span style="color:red">Not available</span>';
} 
if(gr_check_for_fopen()) {
   $red .= '<span style="color:orange">Enabled</span>';
   if((gr_check_for_curl())&&(gr_curl_exec_enabled())) {
      $red .= '<span style="color:orange">, but you should use cURL</span>';
   }
} else {
   $red .= '<span style="color:lime">Disabled</span>';
} 
echo $green . '<br>';
echo $red;
?>
</td>
</tr>
<tr>
<td valign="top" align="left" width="150"><input type="checkbox" name="goodreviews-getmethod" id="goodreviews-getmethod" value="on" <?php if(get_option('goodreviews-getmethod')) { echo get_option('goodreviews-getmethod'); } else { add_option('goodreviews-getmethod','unchecked'); } ?> /></td>
<td valign="top" align="left" width="300">Select this checkbox to use file_get_contents instead of cURL (<span style="color:red">not recommended</span>).</td>
</tr>
<tr>
<td valign="top" align="left" width="200">Alternate Stylesheet URL</td>
<td valign="top" align="left" width="300"><input type="text" name="goodreviews-alt-style" id="goodreviews-alt-style" size="80" value="<?php echo get_option('goodreviews-alt-style');?>" /></td>
</tr>
</table>
<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes');?>"/></p>
</form>

<h2>GoodReviews Usage</h2>

<p><b>Shortcode</b>: <code>[goodreviews isbn="&lt;ISBN-10 or ISBN-13&gt;"]</code></p>

<p>Insert the above shortcode into any page or post where you want Goodreads book information and reviews to appear. Replace <code>&lt;ISBN-10 or ISBN-13&gt;</code> with the book's International Standard Book Number (ISBN) to retrieve and display the information and reviews for that book.</p>

<p>GoodReviews also supports the following shortcode parameters:</p>
<p>* <b>cover:</b> toggles book cover image display. "Large" displays the large cover, "small" displays the small cover, and "off" removes the cover from display. "Large" is default.<br>
* <b>author:</b> toggles the author image display. "Large" displays the large image, "small" displays the small image, and "off" removes the image from display. "Off" is default.<br>
* <b>bookinfo:</b> toggles book information element. "On" displays the element. "Off" removes the element from display. "On" is default.<br>
* <b>buyinfo:</b> toggles book buying links elements. "On" displays the element. "Off" removes the element from display. "On" is default.<br>
* <b>width:</b> defines the full width of the GoodReviews element. Should be a numeric pixel value.<br>
* <b>height:</b> defines the height of each GoodReviews element. Should be a numeric pixel value.<br>
* <b>border:</b> toggles the reviews element iframe border. "On" enables the border. "Off" disables the border. "Off" is default.<br>
* <b>grbackground:</b> defines the hexadecimal background color of the plugin elements. Do not include the "#" symbol.<br>
* <b>grtext:</b> defines the hexadecimal text color of the plugin elements. Do not include the "#" symbol.<br>
* <b>grstars:</b> defines the hexadecimal stars color of the plugin elements. Do not include the "#" symbol.<br>
* <b>grlinks:</b> defines the hexadecimal link color of the plugin elements. Do not include the "#" symbol.<br>
* <b>grheader:</b> defines the header text that appears above the reviews element.<br>
* <b>grnumber:</b> defines the number of reviews per page. Default is 10.<br>
* <b>grminimum:</b> defines the minimum star rating that is required for a review to be displayed. By default, all reviews are displayed.<br>
</p>
<p>For example, the following shortcode would display reviews only:</p>
<p><code>[goodreviews isbn="0000000000000" bookinfo="off" buyinfo="off" cover="off"]</code></p>

<p>The following shortcode would display everything except the book's cover image</p>
<p><code>[goodreviews isbn="0000000000000" cover="off"]</code></p>

<p>If your title does not have an ISBN but does have a Goodreads ID, you can replace the <strong>isbn</strong> parameter with <strong>grid</strong>:</p>
<p><code>[goodreviews grid="0000000000"]</code></p>

</div>
<?php
}

function goodreviews_register_settings() {
   register_setting('goodreviews_options','goodreviews-api-key','goodreviews_validate_api');
   register_setting('goodreviews_options','goodreviews-getmethod','goodreviews_validate_getmethod');
   register_setting('goodreviews_options','goodreviews-agree','goodreviews_validate_agree');
   register_setting('goodreviews_options','goodreviews-alt-style','goodreviews_validate_style');
}

function goodreviews_validate_style($input) {
   $newKey = trim($input);
   if(isset($newKey)) {
      if(!preg_match('/[\w\W]*/i',$newKey)) {
         $newKey='';
      }
   }
   return $newKey;
}

function goodreviews_validate_getmethod($input) {
   $newKey = trim($input);
   if(isset($newKey)) {
      if(preg_match('/on/',$newKey)) {
         $newKey='checked';
      } else {
         $newKey='unchecked';
      }
   } else {
      $newKey = 'unchecked';
   }
   return $newKey;
}

function goodreviews_validate_agree($input) {
   $newKey = trim($input);
   if(isset($newKey)) {
      if(preg_match('/on/',$newKey)) {
         $newKey='checked';
      } else {
         $newKey='unchecked';
      }
   } else {
      $newKey = 'unchecked';
   }
   return $newKey;
}

function goodreviews_validate_api($input) {
   $newKey = trim($input);
   if(!preg_match('/^[\w]*$/i',$newKey)) {
      $newKey = '';
   }
   return $newKey;
}

function goodreviews_shortcode($goodrevAtts) {
   extract( shortcode_atts( array(
      'grid' => '',
      'isbn' => '',
      'border' => 'off',
      'width' => '565',
      'height' => '400',
      'bookinfo' => 'on',
      'buyinfo' => 'on',
      'cover' => 'large',
      'author' => 'off',
      'grstars' => '000',
      'grlinks' => '660',
      'grheader' => '',
      'grbackground' => 'fff',
      'grtext' => '382110',
      'grnumber' => '',
      'grminimum' => '',
	  ), $goodrevAtts ) );

   $width = preg_replace('/px/i','',$width);
   $height = preg_replace('/px/i','',$height);
   $grstars = preg_replace('/\#/','',$grstars);
   $grlinks = preg_replace('/\#/','',$grlinks);
   $grheader = preg_replace('/\#/','',$grheader);
   $grbackground = preg_replace('/\#/','',$grbackground);
   $grtext = preg_replace('/\#/','',$grtext);

   return goodreviews_scrape($author,$cover,$grid,$isbn,$border,$width,$height,$bookinfo,$buyinfo,$grlinks,$grstars,$grheader,$grbackground,$grtext,$grnumber,$grminimum);
}

function goodreviews_mod_styles($author,$cover,$width,$height,$bookinfo,$buyinfo,$grlinks,$grstars,$grheader,$grbackground,$grtext,$grnumber,$grminimum) {
   $goodreviews_style_mods = "<style>\n";
   if(!preg_match('/382110/',$grlinks)) {
      $goodreviews_style_mods .= "#goodreviews-div a,#goodreads-widget .gr_branding,.goodreviews-label { color:#" . $grlinks . ";}\n";
   }
   if((!preg_match('/fff/i',$grbackground))||(!preg_match('/382110/',$grtext))) {
      $goodreviews_style_mods .= ".goodreviews-booklist {";
      if(!preg_match('/fff/i',$grbackground)) {
         $goodreviews_style_mods .= "background-color:#" . $grbackground . ";";
      }
      $goodreviews_style_mods .= "color:#" . $grtext . ";";
      $goodreviews_style_mods .= "}\n";
   }
   if(!preg_match('/400/',$height)) {
      $goodreviews_style_mods .= "#goodreviews-bookinfo, #goodreviews-data, #goodreviews-buybook { ";
      $goodreviews_style_mods .= " height:" . $height . "px; ";
      $goodreviews_style_mods .= "}\n";
   } else {
      $grdataheight = $height . 'px';
   }
   if(!preg_match('/565/',$width)) {
      $grstylewidth = $width . 'px';
      $goodreviews_style_mods .= "#goodreviews-div { width:" . $grstylewidth . "; }\n";
      $goodreviews_style_mods .= "#goodreads-widget { width:" . $grstylewidth ."; }\n";
   }
   if(preg_match('/off/i',$buyinfo)) {
      $grdatawidth = $width . 'px';
      $goodreviews_style_mods .= "#goodreviews-bookinfo { width:" . $grdatawidth . "; }\n";
   }
   if((preg_match('/off/i',$bookinfo))||(!preg_match('/fff/i',$grbackground))||(!preg_match('/382110/',$grtext))) {
      $grbuywidth = $width . 'px';
      $goodreviews_style_mods .= "#goodreviews-buybook { ";
      if(preg_match('/off/i',$bookinfo)) {
         $goodreviews_style_mods .= "width:" . $grbuywidth . "; height:" . $grdataheight . "; ";
      }
      if(!preg_match('/fff/i',$grbackground)) {
         $goodreviews_style_mods .= "background-color:#" . $grbackground . ";";      
      }
      $goodreviews_style_mods .= "color:#" . $grtext .";";
      $goodreviews_style_mods .= " }\n";
   }
   if(!preg_match('/000/i',$grstars)) {
      $goodreviews_style_mods .= ".goodreviews-star { color:#" . $grstars . "; }\n";
   }
   if(!preg_match('/large/i',$cover)) {
      $coversize = "55px";
      if(preg_match('/off/i',$cover)) {
         $coverdisplay = "none";
      } else {
         $coverdisplay = "block";
      }
   } else {
      $coversize = "115px";
      $coverdisplay = "block";
   }
   $goodreviews_style_mods .= "#goodreviews-cover { display:" . $coverdisplay . "; width:" . $coversize . "; height:" . $grdataheight . "; }\n";
   if(!preg_match('/off/i',$author)) {
      $authordisplay = "block";
      $goodreviews_style_mods .= "#grauthorimage { display:" . $authordisplay . "; }\n";
   }
   $goodreviews_style_mods .= "</style>\n";
   return $goodreviews_style_mods;
}

function goodreviews_styles($author,$cover,$width,$height,$bookinfo,$buyinfo,$grlinks,$grstars,$grheader,$grbackground,$grtext,$grnumber,$grminimum) {
   if((preg_match('/on/i',$bookinfo))||(preg_match('/on/i',$buyinfo))) {
      $goodreviews_style = goodreviews_mod_styles($author,$cover,$width,$height,$bookinfo,$buyinfo,$grlinks,$grstars,$grheader,$grbackground,$grtext,$grnumber,$grminimum);
   } else {
      if(preg_match('/[\w\W]*/i',$gr_alt_style)) {
         $goodreviews_style = goodreviews_mod_styles($author,$cover,$width,$height,$bookinfo,$buyinfo,$grlinks,$grstars,$grheader,$grbackground,$grtext,$grnumber,$grminimum);
      } else {
         $goodreviews_style = goodreviews_mod_styles($author,$cover,$width,$height,$bookinfo,$buyinfo,$grlinks,$grstars,$grheader,$grbackground,$grtext,$grnumber,$grminimum);
      }
   }
   return $goodreviews_style;
}

function goodreviews_stars($Result) {
   $goodreviewAvg = floor($Result->book->average_rating);
   $stars = '<b>Average Rating:</b><br>';

   for($i=0;$i<$goodreviewAvg;$i++) {
      $stars .= '<span class="goodreviews-star">&#9733;</span>';
   }
   $blankstars = 5 - $goodreviewAvg;
   for($i=0;$i<$blankstars;$i++) {
      $stars .= '<span class="goodreviews-star">&#9734;</span>';
   }
   $stars .= '<br>' .
             '<span class="goodreviews-based">based on ' . $Result->book->ratings_count . ' rating(s)</span><br><br>';
      return $stars;
}

function goodreviews_bookinfo($author,$cover,$bookinfo,$Result) {
   if(preg_match('/on/i',$bookinfo)) {
      $showbook = '<div id="goodreviews-bookinfo">' .
                  '<label for="goodreviews-bookinfo" class="goodreviews-label">' . $Result->book->title . '</label>' .
                  '<div class="goodreviews-booklist">' .
                  '<div id="goodreviews-cover"><img src="';
      if(preg_match('/large/i',$cover)) {
         $showbook .= $Result->book->image_url . '"/></div>';
      } else {
         $showbook .= $Result->book->small_image_url . '"/></div>';
      }
      
      $showbook .= '<div id="goodreviews-data">';
     
      // show stars 
      $showbook .= goodreviews_stars($Result);              
                 
      // Show remainder of info
      $showbook .= '<b>ISBN-10:</b> ' . $Result->book->isbn . '<br>' .
                   '<b>ISBN-13:</b> ' . $Result->book->isbn13 . '<br>' .
                   '<b>Goodreads:</b> <a href="' . $Result->book->link . '">' . $Result->book->id . '</a><br><br>' .
                   '<b>Author(s):</b> ';
                   
      foreach ($Result->book->authors->author as $grauthor) {
         if(preg_match('/large/i',$author)) {
            $showbook .= '<div id="grauthorimage"><a href="'. $grauthor->link . '"><img src="' . $grauthor->image_url . '" /></a></div>';
         } else {
            $showbook .= '<div id="grauthorimage"><a href="'. $grauthor->link . '"><img src="' . $grauthor->small_image_url .'" /></a></div>';
         }
         $showbook .= '<div id="grauthorname"><a href="'. $grauthor->link . '">'. $grauthor->name . '</a></div>';
      }
      
      $showbook .= '<b>Publisher:</b> ' . $Result->book->publisher . '<br>' .
                   '<b>Published:</b> ' . $Result->book->publication_month . '/' . $Result->book->publication_day . '/' . $Result->book->publication_year . '<br><br>' .
                   $Result->book->description .
                   '</div>' .
                   '</div>' .
                   '<div class="goodreviews-credit"><a href="http://www.goodreads.com">Information from Goodreads.com</a></div>' .
                   '</div>';
   } else {
      $showbook = "";
   }
   return $showbook;
}

function goodreviews_buyinfo($buyinfo,$Result) {
   if(preg_match('/on/i',$buyinfo)) {
      $buybook = '<div><label for="goodreviews-buybook" class="goodreviews-label">Buy This Book</label>' .
                 '<div id="goodreviews-buybook">' .
                 '<ul class="goodreviews-buylist">';

      foreach($Result->book->book_links->book_link as $buyme) {
         $buybook .= '<li><a href="' . $buyme->link . '?book_id=' . $Result->book->id . '" class="grbuy">' . $buyme->name . '</a></li>';
      }
      
      $buybook .= '</ul>' .
      '</div>' .
      '<div class="goodreviews-credit"><a href="http://www.goodreads.com">Links from Goodreads.com</a></div></div>';
   } else {
      $buybook = "";
   }
   return $buybook;
}

function goodreviews_scrape($author,$cover,$grid,$isbn,$border,$width,$height,$bookinfo,$buyinfo,$grlinks,$grstars,$grheader,$grbackground,$grtext,$grnumber,$grminimum) {
   $goodreviews_api = get_option('goodreviews-api-key');
   $goodreviews_getmethod = get_option('goodreviews-getmethod');
   $goodreviews_getagree = get_option('goodreviews-agree');
   $goodreviews_alt_style = get_option('goodreviews-alt-style');

   if(preg_match('/unchecked/i',$goodreviews_getagree)) {
      echo "<!-- You MUST allow Goodreads.com links on your site in order to use this plugin -->";
   } else {
      
   if(((preg_match('/\w/',$isbn))&&(strlen($isbn)>=10)&&(strlen($isbn)<=13))||(preg_match('/\w/',$grid))) {
   
     // construct the Goodreads URL to retrieve the data
     $host = "www.goodreads.com";
     if(strlen($isbn)>0) {
     $path = "/book/isbn";
     $req = "?callback=myCallback" .
            "&format=xml" .
            "&isbn=" . $isbn .
            "&key=" . $goodreviews_api;
     } else {
        $path = "/book/show";
        $req = "?callback=myCallback" .
               "&format=xml" .
               "&id=" . $grid .
               "&key=" . $goodreviews_api; 
     }
    if(isset($_SERVER['HTTPS']) || (is_ssl())) {
        $gr_proto = "https://";
     } else {
        $gr_proto = "http://";
     }
     $uri = $gr_proto . $host . $path . $req;

     if (strlen(trim($goodreviews_api))>0)  {
        if(preg_match('/unchecked/',$goodreviews_getmethod)) {
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $uri);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           $xml = curl_exec($ch);
           curl_close($ch);
        } else {
           $xml = file_get_contents($uri);
        }
        
        // Parse XML results
        $Result = @simplexml_load_string($xml);
        
        // Helps mitigate traffic volume to Goodreads API
        sleep(1);
        
        if(isset($Result->book->id)) {

              // Set styles
              $goodreviews_display = goodreviews_styles($author,$cover,$width,$height,$bookinfo,$buyinfo,$grlinks,$grstars,$grheader,$grbackground,$grtext,$grnumber,$grminimum);
              $goodreviews_display .= '<div id="goodreviews-div">';
              
              if(preg_match('/on/i',$bookinfo)) {
                 // Book Info
                 $goodreviews_display .= goodreviews_bookinfo($author,$cover,$bookinfo,$Result);
              }
              if(preg_match('/on/i',$buyinfo)) {
                 // Buy Info
                 $goodreviews_display .= goodreviews_buyinfo($buyinfo,$Result);
              }
              if((preg_match('/on/i',$bookinfo))||(preg_match('/on/i',$buyinfo))) {
                 $goodreviews_display .='<div class="goodreviews-clear">&nbsp;</div>';
              }
              
              // BROKEN OUT Review Info
              preg_match('/src="([^"]*)"/',$Result->book->reviews_widget,$gr_iframe_src);
              preg_match('/(<div[\w\W]*)<iframe/',$Result->book->reviews_widget,$gr_pre_iframe);

              if($grheader!="") {
                $gr_pre_iframe[1] = preg_replace('/((<a [^>]*)).[^<]*/','\\1>'.$grheader,$gr_pre_iframe[1]);
              }
              
              preg_match('/<\/iframe>([\w\W]*<\/div>)/',$Result->book->reviews_widget,$gr_post_iframe);   
              preg_match('/http[^?]*/',$gr_iframe_src[1],$gr_iframe_url);
              preg_match('/(\?[\w\W]*)links/',$gr_iframe_src[1],$gr_params1);
              $gr_iframe_parsed = $gr_iframe_url[0] . 
                                  $gr_params1[1] .
                                 "links=" . $grlinks .
                                 "&header_text=" . preg_replace('/ /','+',$grheader) .
                        		 "&min_rating=" . $grminimum .
                                 "&num_reviews=" . $grnumber . 
                                 "&review_back=" . $grbackground .
                                 "&stars=" . $grstars .
                                 "&stylesheet=" . $goodreviews_alt_style .
                                 "&text=" . $grtext;
              $goodreviews_message = $goodreviews_display .
                                     $gr_pre_iframe[1] .
                                     "<iframe src=\"" .
                                     $gr_iframe_parsed .
                                     "\" width=\"" . $width .
                                     "\" height=\"" . $height .
                                     "\" frameborder=\"";
              if(preg_match('/on/i',$border)) {
                 $goodreviews_message .= "0";
              } else {
                 $goodreviews_message .= "1";
              }
              $goodreviews_message .= "\"></iframe>" .
                                      $gr_post_iframe[1]; 

              if(strlen($height)>0) {
                 $goodreviews_message = str_replace('400',$height,$goodreviews_message);
              }
              if(strlen($width)>0) {
                 $goodreviews_message = str_replace('565',$width,$goodreviews_message);
              }
              if(preg_match('/on/i',$border)) {
                 $goodreviews_message = str_replace('frameborder="0"','frameborder="1"',$goodreviews_message);
              }
              $goodreviews_message .= '</div>';
        } else {
              if($Result->Error->Message) {
                 $goodreviews_message = "<div id=\"goodreviews-error\"><h2>A GoodReviews Error Occurred</h2> " . $Result->Error->Code . ": " . $Result->Error->Message . "</div>\n";
              } else {
                 $goodreviews_message = "\n<!-- GoodReviews did not find any available reviews. -->\n";
              }
        }
     } else {
        $goodreviews_message = "\n<!-- GoodReviews is not properly configured. -->\n";
     }
   } else {
      $goodreviews_message = "\n<!-- A valid ISBN or Goodreads.com ID was not provided, or GoodReviews is not properly configured. -->\n";
   }
   return $goodreviews_message;
   }
   add_action('wp_print_styles', 'goodreviews_deregister_styles');
}

?>