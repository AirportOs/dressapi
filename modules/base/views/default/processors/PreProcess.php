<?php 
/**
 * PreProcess.php
 * 
 * This file can prepare some contents for templates file 
 * PreProcess.php is part of the function send() of object CHtmlView
 * 
 */

//  $page_title = $this->page_info['element']['title'];
//  $page_description = $this->page_info['element']['description'];

/**  
 * Translate text
 * @param string $text text to translate
 * @return string translated text
*/
function _T(string $text) : string
{
    return $text;
}