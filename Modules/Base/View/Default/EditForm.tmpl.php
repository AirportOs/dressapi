<?php
$current_model = $controller->getCurrentModel();
?>
<pre>
<?php
print_r($current_model->getFields());
