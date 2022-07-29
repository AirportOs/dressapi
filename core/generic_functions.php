<?php

/**  
 * Translate text
 * @param string $text text to translate
 * @return string translated text
*/
function _T(string $text) : string { return $text; }


/**  
 * like print_r() print an variable or an complex object on browser
 * @param mixed $var the variable or object to display
 * @return string translated text
*/
function printr(mixed $var) : void { ?><pre><?=print_r($var); ?></pre><?php }


/**  
 * like var_dump() print an variable or an complex object on browser
 * @param mixed $var the variable or object to display
*/
function vardump(mixed $var) : void { ?><pre><?=var_dump($var); ?></pre><?php }
