<?php 

$url = '/{{data::metadata::module}}/{{elem::id}}/insert';
$next_url = '/{{data::metadata::module}}';
$this->data['elements'] = [0=>[]];
include(__DIR__.'/EditForm.tmpl.php');
 