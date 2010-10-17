<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Raphael Zschorsch <rafu1987@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_rzthreesixty_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_rzthreesixty_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_rzthreesixty_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'rzthreesixty';	// The extension key.
	var $pi_checkCHash = true;
	
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		// Read Flexform
  	$this->pi_initPIflexForm();
  	$images = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'images', 'sDEF');
  	$method = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'method', 'options');
  	$cycle = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'cycle', 'options');
  	$direction = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'direction', 'options');
  	$img_width = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'img_width', 'options');
  	$sensibility = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sensibility', 'options');
  	$sensibility = str_replace(",",".",$sensibility);
  	
  	// Set default values
  	if(empty($cycle)) $cycle = 3;
  	if(empty($sensibility)) $sensibility = '0.3';
    
    // Get content ID
    $ce_id = $this->cObj->data['uid'];
                
    // Create image array
    $images_arr = explode(",",$images);
        
    // Get image width and height of first image
    list($width, $height) = getimagesize('uploads/rzthreesixty/'.$images_arr[0].'');
    
    // Create array for JS
    foreach($images_arr as $i) {
      $images_new .= "'uploads/rzthreesixty/".$i."',";
    }	            
    $images_new = substr($images_new,0,-1);	        
  	
    // Process image if width is set
    $process_img = ''; // Clear var
    if(isset($img_width) && is_numeric($img_width)) {
      // Counter
      $i = 0; 
      foreach($images_arr as $img_p) {
        $imgConf['file'] = 'uploads/rzthreesixty/'.$img_p;
        $imgConf['file.']['width'] = $img_width;
        
        $process_img = $this->cObj->IMG_RESOURCE($imgConf);
        $process_img_out .= "'".$this->cObj->IMG_RESOURCE($imgConf)."',"; 
        // Get width and height of first processed image
        if($i == 0) {
          list($process_width, $process_height) = getimagesize($process_img);
        }        
        $i++;
      }
      $process_img_out = substr($process_img_out,0,-1);      
    }    
				
		// Add JavaScript
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId] = '
      <script type="text/javascript" src="typo3conf/ext/rzthreesixty/res/js/jquery.threesixty.js"></script>
    ';
	
		$content = '
    <div class="rzthreesixty_container">
	   <div class="rzthreesixty_container_inner">
	  ';
	  
	  // If image is processed
	  if($process_img != '') {
	    $content .= '
	   <img src="'.$process_img.'" class="rzthreesixty_'.$ce_id.'" width="'.$process_width.'" height="'.$process_height.'" alt="" title="" />';
	  }
	  // Original images
	  else {	  
  	  $content .= '
  		  <img src="uploads/rzthreesixty/'.$images_arr[0].'" class="rzthreesixty_'.$ce_id.'" width="'.$width.'" height="'.$height.'" alt="" title="" />';
  	}
  	
  	$content .= '
	   </div>
    </div>
    <script language="javascript">
      $(document).ready(function() {
        $(function() {
    ';
    
    // If image is processed
    if($process_img != '') {
      $content .= '
            var arr = new Array('.$process_img_out.');    
      ';
    }
    // Original images
    else {    
      $content .= '
            var arr = new Array('.$images_new.');
      '; 
    }   
    
    $content .= '
          	$(".rzthreesixty_'.$ce_id.'").threesixty({images:arr, method:\''.$method.'\', \'cycle\':'.$cycle.', direction:"'.$direction.'", sensibility: '.$sensibility.'});;        
          }); 
        });     
      </script>    
  		';
	
		return $this->pi_wrapInBaseClass($content);
	}
}  

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rzthreesixty/pi1/class.tx_rzthreesixty_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rzthreesixty/pi1/class.tx_rzthreesixty_pi1.php']);
}

?>