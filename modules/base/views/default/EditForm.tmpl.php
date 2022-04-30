<form method="POST" action="/<?=$module_name.'/'.$action; ?>">
<?php

$data = $this->data;

$module_name = $this->data['metadata']['module'];
$related_tables = $this->data['related_tables'];
foreach($this->data['elements'] as $pos_element=>$element)
{
    foreach($this->data['structure'] as $element_structure)
    {
        $filename = realpath(__DIR__.'/form-elements/'.$element_structure['html_type'].'.tmpl.php');
        $name = $element_structure['field'];
        $value = $element[$name] ?? '';
        $comment = $element_structure['comment'] ?? '';
        $pattern = $element_structure['rule'] ?? '';
        $display_name = $element_structure['display_name'] ?? '';
        if ($filename)
            include($filename); 
    }
}

echo "<pre>";
print_r($this);
// print_r($request);
// print_r($response);

?>
</form>