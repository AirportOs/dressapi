<?php 
/**
 * PostProcess.php
 * 
 * This file can change the output code before send to browser
 * The html code is in the $output variable  
 * PostProcess.php is part of the function send() of object CHtmlView
 * 
 */

/*
// Example: 
$output = str_replace([ '<!-- [[LABEL 1]] -->',
                        '<!-- [[LABEL 2]] -->'],
                                
                        [$label_1,
                         $label_2,
                        ],

                        $output);
*/