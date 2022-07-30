{{foreach data::elements elem}}
<?php 
$url = '/{{data::metadata::module}}/{{elem::id}}/modify';
$next_url = '/{{data::metadata::module}}';

include(__DIR__.'/EditForm.tmpl.php');
?>
{{end foreach elem}}
