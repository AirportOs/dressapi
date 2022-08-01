
<h1>{{page_info::module::title}}::{{page_info::element::title}}{{page_info::element::name}}</h1>
<br>
<div class="result">

<?php
/*
?>

{{if data::elements}}

    {{foreach data::elements elem}}
        <form method="POST" action="{{url}}<?=$url; ?>">
            <input type="hidden" name="next_url" value="{{next_url}}<?=$next_url?>">
            {{foreach data::structure element_structure}}
                {{element::value}}
                {{element_structure::field}}
                {{element_structure::comment}}
                {{element_structure::pattern}}
                {{element_structure::display_name}}
                {{filename =__DIR__.'/form-elements/'.{{element_structure::html_type}}.'.tmpl.php'}}
                {{include filename}}
            {{end foreach element_structure}}
            <div class="row">
                <input value="Save" type="submit" class="btn btn-warning col-sm-3 col-lg-2 m-3 top-50 start-0">  
                <a href="<?=$next_url?>" class="btn btn-secondary col-sm-3 col-lg-2 m-3 top-50 start-0">{{'Go to List'}}</a>  
                {{if permission::can_delete}}
                <input value="Delete" type="submit" class="btn btn-danger col-sm-3 col-lg-2 m-3 top-50 end-0" onclick="return confirm('Are you sure?');">
                {{if permission::can_delete}}
                <br>
            </div>
        </form>
    {{end foreach elem}}

{{end if data::elements}}
<?php
*/

$data = $this->data;
foreach($data['elements'] as $pos_element=>$element)
{
?>
<form method="POST" action="{{url}}<?=$url; ?>">
<input type="hidden" name="next_url" value="<?=$next_url?>">

<?php
    foreach($this->data['structure'] as $element_structure)
    {
        $filename = realpath(__DIR__.'/form-elements/'.$element_structure['html_type'].'.tmpl.php');
        $name = $element_structure['field'];
        $value = $element[$name] ?? ($element_structure['default'] ?? '');
        $comment = $element_structure['comment'] ?? '';
        $pattern = $element_structure['rule'] ?? '';
        $display_name = $element_structure['display_name'] ?? '';        
        
        if ($filename)
            include($filename); 
    }
?>

    <div class="row">
        <input value="Save" type="submit" class="btn btn-warning col-sm-3 col-lg-2 m-3 top-50 start-0">  
        <a href="<?=$next_url?>" class="btn btn-secondary col-sm-3 col-lg-2 m-3 top-50 start-0">{{'Go to List'}}</a>  
        <input value="Delete" type="submit" class="btn btn-danger col-sm-3 col-lg-2 m-3 top-50 end-0" onclick="return confirm('Are you sure?');">
        <br>
    </div>
    </form>
<?php    
}

// printr($this);
// $related_tables = $this->data['related_tables'];

// printr($request);
// printr($response);

?>
</div>
