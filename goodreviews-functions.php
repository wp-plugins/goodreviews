<?php
function goodreviews_admin_add_page() {
   add_options_page('GoodReviews Settings','GoodReviews','manage_options','goodrev-options','goodreviews_options');
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

<table width="650" border="0">
<tr>
<td valign="top" align="left"><input type="checkbox" name="goodreviews-agree" id="goodreviews-agree" value="on" <?php if(get_option('goodreviews-agree')) { echo get_option('goodreviews-agree'); } else { add_option('goodreviews-agree','unchecked'); } ?> /></td>
<td valign="top" align="left" width="300">You MUST agree to allow GoodReviews to display Goodreads.com links on your site in order to use this plugin. Select this checkbox to agree.</td>
</tr>
<tr><td valign="top" align="left" width="300">Goodreads API Developer Key </td>
<td valign="top" align="left"><input type="text" name="goodreviews-api-key" id="goodreviews-api-key" size="80" value="<?php echo get_option('goodreviews-api-key');?>" /><td>
</tr>
<tr>
<td valign="top" align="left"><input type="checkbox" name="goodreviews-getmethod" id="goodreviews-getmethod" value="on" <?php if(get_option('goodreviews-getmethod')) { echo get_option('goodreviews-getmethod'); } else { add_option('goodreviews-getmethod','unchecked'); } ?> /></td>
<td valign="top" align="left" width="300">If you receive cURL errors when attempting to retrieve reviews, check this box to use file_get_contents instead.</td>
</tr>
</table>
<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes');?>"/></p>
</form>

<h2>GoodReviews Usage</h2>

<p><b>Shortcode</b>: <code>[goodreviews isbn="&lt;ISBN-10 or ISBN-13&gt;"]</code></p>

<p>Insert the above shortcode into any page or post where you want Goodreads book information and reviews to appear. Replace <code>&lt;ISBN-10 or ISBN-13&gt;</code> with the book's International Standard Book Number (ISBN) to retrieve and display the information and reviews for that book.</p>

<p>GoodReviews also supports the following shortcode parameters:</p>
<p>* <b>cover:</b> toggles book cover image display. "Large" displays the large cover, "small" displays the small cover, and "off" removes the cover from display. "Large" is default.<br>
* <b>bookinfo:</b> toggles book information element. "On" displays the element. "Off" removes the element from display. "On" is default.<br>
* <b>buyinfo:</b> toggles book buying links elements. "On" displays the element. "Off" removes the element from display. "On" is default.<br>
* <b>grstyles:</b> toggles the default CSS for bookinfo and buyinfo elements. "On" uses default CSS. "Off" uses no CSS, thereby allowing you to create your own. "On" is default.<br>
* <b>width:</b> defines the full width of the GoodReviews element. Should be a numeric pixel value.<br>
* <b>height:</b> defines the height of each GoodReviews element. Should be a numeric pixel value.<br>
* <b>border:</b> toggles the reviews element iframe border. "On" enables the border. "Off" disables the border. "Off" is default.<br>
</p>
<p>For example, the following shortcode would display reviews only:</p>
<p><code>[goodreviews isbn="0000000000000" bookinfo="off" buyinfo="off" cover="off"]</code></p>

<p>The following shortcode would display everything except the book's cover image</p>
<p><code>[goodreviews isbn="0000000000000" cover="off"]</code></p>

<p>The following shortcode would display everything without the default CSS. However, the CSS delivered for the Goodreads reviews iframe remains intact:</p>
<p><code>[goodreviews isbn="0000000000000" grstyles="off"]</code></p>

</div>
<?php
}

function goodreviews_register_settings() {
   register_setting('goodreviews_options','goodreviews-api-key','goodreviews_validate_api');
   register_setting('goodreviews_options','goodreviews-getmethod','goodreviews_validate_getmethod');
   register_setting('goodreviews_options','goodreviews-agree','goodreviews_validate_agree');
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
      'isbn' => '',
      'border' => 'off',
      'width' => '565',
      'height' => '400',
      'bookinfo' => 'on',
      'buyinfo' => 'on',
      'grstyles' => 'on',
      'cover' => 'large'
	  ), $goodrevAtts ) );
   return goodreviews_scrape($cover,$isbn,$border,$width,$height,$bookinfo,$buyinfo,$grstyles);
}

function goodreviews_styles($cover,$width,$height,$bookinfo,$buyinfo,$grstyles) {
   if(((preg_match('/on/i',$bookinfo))||(preg_match('/on/i',$buyinfo)))&&(preg_match('/on/i',$grstyles))) {
      $grstylewidth = $width . 'px';
      if(preg_match('/off/i',$buyinfo)) {
         $grdatawidth = $width . 'px';
      } else {
         $grdatawidth = ($width * .65) . 'px';
      }
      if(preg_match('/off/i',$bookinfo)) {
         $grbuywidth = $width . 'px';
      } else {
         $grbuywidth = ($width * .33) . 'px';
      }
      $grdataheight = ($height * .65) . 'px';
      if(preg_match('/large/i',$cover)) {
         $coversize = '115px';
      } else {
         $coversize = '63px';
      }
      if(preg_match('/off/i',$cover)) {
         $coverdisplay = "none";
      } else {
         $coverdisplay = "block";
      }
      
      $goodreviews_style = <<<EOS
<style>
#goodreviews-div {
   display: block;
   width: $grstylewidth;
   float: none;
}
.goodreviews-label {
   color:#660;
   font-family: georgia, serif;
   font-size: 16px;
   text-align: right;
}
#goodreviews-bookinfo {
   display: block;
   margin: 0;
   padding: 0;
   width: $grdatawidth;
   float: left;
}
#goodreviews-cover {
  display: $coverdisplay;
  float: left;
  width: $coversize;
  height: $grdataheight;
  border: none;
}
#goodreviews-data {
  border: none;  
  margin: 0;
  padding-left: 5px;
  padding-right: 5px;
  font-family: georgia, serif;
  font-size: 12px;
  color: #382110;
  line-height: 1.5em;
  height: $grdataheight;
  overflow: scroll;
}
#goodreviews-data a,
#goodreviews-data a:hover,
#goodreviews-data a:visited,
#goodreviews-data a:active {
  color: #382110;
  text-decoration: underline;
}
.goodreviews-based {
  font-size: 9px;
  line-height: 1em;
}
.goodreviews-booklist {
  border-top: solid 1px #382110;
  border-bottom: solid 1px #382110;
  background-color: #fff;
  padding: 5px;
  margin: 0;
  list-style: none;
}
#goodreviews-buybook {
   display: block;
   margin: 0;
   padding: 0;
   float: left;
}
.goodreviews-buylist {
  position: relative;
  border-top: solid 1px #382110;
  border-bottom: solid 1px #382110;
  background-color: #fff;
  padding: 5px;
  margin: 0;
  list-style: none;
  width: $grbuywidth;
  height: $grdataheight;
}
.goodreviews-buylist li {
  display: block;
  font-family: georgia, serif;
  font-size: 12px;
  color: #382110;
  line-height: 1.5em;
}
.goodreviews-buylist li a,
.goodreviews-buylist li a:hover,
.goodreviews-buylist li a:active,
.goodreviews-buylist li a:visited {
  color: #382110;
  text-decoration: underline;
}
.goodreviews-clear {
  float:none;
  clear:both;
}
.goodreviews-credit,
.goodreviews-credit a {
  color: #382110; 
  font-size: 11px;
  text-decoration: none; 
  font-family: verdana, arial, helvetica, sans-serif; 
  text-align: right;
}
.goodreviews-credit a:hover {
  text-decoration: underline;
}
</style>
EOS;
   } else {
      $goodreviews_style = "";
   }
        
   return $goodreviews_style;
}

function goodreviews_stars($Result) {
   $goodreviewAvg = floor($Result->book->average_rating);
   if($Result->book->average_rating>$goodreviewAvg) {
      $goodreviewHalf='<img src="' . plugins_url('goodreviews') .'/images/grev_star_half.png">';
   } else {
      $goodreviewHalf="";
   }
      
   $stars = '<b>Average Rating:</b><br>';

   for($i=0;$i<$goodreviewAvg;$i++) {
      $stars .= '<img src="' . plugins_url('goodreviews') .'/images/grev_star.png">';
   }
   $stars .= $goodreviewHalf .
             '<br>' .
             '<span class="goodreviews-based">based on ' . $Result->book->ratings_count . ' rating(s)</span><br><br>';
EOSTARS;
      return $stars;
}

function goodreviews_bookinfo($cover,$bookinfo,$Result) {
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
         $showbook .= '<a href="'. $grauthor->link . '">'. $grauthor->name . '</a><br>';
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
      $buybook = '<div id="goodreviews-buybook">' .
                 '<label for="goodreviews-buybook" class="goodreviews-label">Buy This Book</label>' .
                 '<ul class="goodreviews-buylist">';

      foreach($Result->book->book_links->book_link as $buyme) {
         $buybook .= '<li><a href="' . $buyme->link . '" class="grbuy">' . $buyme->name . '</a></li>';
      }
      
      $buybook .= '</ul>' .
      '<div class="goodreviews-credit"><a href="http://www.goodreads.com">Links from Goodreads.com</a></div>' .
      '</div>' .
      '<div class="goodreviews-clear">&nbsp;</div>';
   } else {
      $buybook = "";
   }
   return $buybook;
}

function goodreviews_scrape($cover,$isbn,$border,$width,$height,$bookinfo,$buyinfo,$grstyles) {
   $goodreviews_api = get_option('goodreviews-api-key');
   $goodreviews_getmethod = get_option('goodreviews-getmethod');
   $goodreviews_getagree = get_option('goodreviews-agree');

   if(preg_match('/unchecked/i',$goodreviews_getagree)) {
      echo "<!-- You MUST allow Goodreads.com links on your site in order to use this plugin -->";
   } else {
      
   if((preg_match('/\w/',$isbn))&&(strlen($isbn)>=10)&&(strlen($isbn)<=13)) {
   
     // construct the Goodreads URL to retrieve the data
     $host = "www.goodreads.com";
     $path = "/book/isbn";
     $req = "?callback=myCallback" .
            "&format=xml" .
            "&isbn=" . $isbn .
            "&key=" . $goodreviews_api;
   
     $uri = "http://" . $host . $path . $req;

     if ((strlen($goodreviews_api)==22))  {
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
              $goodreviews_display = goodreviews_styles($cover,$width,$height,$bookinfo,$buyinfo,$grstyles);
              $goodreviews_display .= '<div id="goodreviews-div">';
              
              // Book Info
              $goodreviews_display .= goodreviews_bookinfo($cover,$bookinfo,$Result);
        
              // Buy Info
              $goodreviews_display .= goodreviews_buyinfo($buyinfo,$Result);

              // Review Info
              $goodreviews_message = $goodreviews_display .
                                     $Result->book->reviews_widget;
              if(strlen($height)>0) {
                 $goodreviews_message = str_replace('400',$height,$goodreviews_message);
              }
              if(strlen($width)>0) {
                 $goodreviews_message = str_replace('565',$width,$goodreviews_message);
              }
              if(preg_match('/on/i',$border)) {
                 $goodreviews_message = str_replace('frameborder="0"','frameborder="1"',$goodreviews_message);
              }
              echo '</div>';
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
      $goodreviews_message = "\n<!-- A valid ISBN was not provided or GoodReviews is not properly configured. -->\n";
   }
   return $goodreviews_message;
   }
}
?>