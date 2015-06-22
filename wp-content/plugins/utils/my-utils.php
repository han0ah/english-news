<?php
/*
Plugin Name: My Utils
Plugin URI: http://han0ah.tistory.com
Description: Collection of utility functions
Version: 1.0
Author: KiJong
Author URI: http://han0ah.tistory.com
*/

function wpex_mce_google_fonts_array( $initArray )  {
	
	$fonts .= '굴림=굴림;';
	$fonts .= '굴림체=굴림체;';
	$fonts .= '나눔고딕=Nanum Gothic;';
	$fonts .= '궁서=궁서;';
	$fonts .= '궁서체=궁서체;';
	$fonts .= '돋움=돋움;';
	$fonts .= '돋움체=돋움체;';
	$fonts .= '바탕=바탕;';
	$fonts .= '바탕체=바탕체;';

	$fonts .= 'Comic Sans MS=Comic Sans MS;';
	$fonts .= 'Courier New=Courier New;';
	$fonts .= 'Tahoma=Tahoma;';
	$fonts .= 'Times New Roman=Times New Roman;';
	$fonts .= 'Verdana=Verdana;';
	
	$initArray['font_formats'] = $fonts;
	return $initArray;
}
add_filter( 'tiny_mce_before_init', 'wpex_mce_google_fonts_array' );

?>