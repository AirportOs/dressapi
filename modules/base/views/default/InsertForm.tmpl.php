<?php 

$action = 'insert';
$url = $module_name.'/'.$action;
$next_url = '/{{data::metadata::module}}/'.$action;
include(__DIR__.'/EditForm.tmpl.php');
 