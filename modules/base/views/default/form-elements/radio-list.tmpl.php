<div class="form-group">
    <label class="active"><?=$display_name ?></label>
    <small id="text_help_<?=$name ?>" class="form-text text-muted"><?=$comment ?></small>
    <div  style="background:#eee">
<?php
    $options = explode('|',$element_structure['options']);

    $inline = ((strlen($element_structure['options'])<=60)?' form-check-inline':'');

    if ($element_structure['null']!='NO')
    {
        $checked = ((in_array($value, $opt))?(' checked'):(''));
?>
        <div class="form-check<?=$inline ?>">
            <input name="<?=$name ?>" value="" type="radio" id="<?=$name.'_null' ?>"<?=$checked?>>
            <label for="<?=$name.'_null' ?>">None</label>
        </div>
<?php
    }

    foreach($options as $opt)
    {
        $id = $name.'_'.str_replace(' ','_',$opt);
        $checked = (($opt==$value)?(' checked'):(''));
?>
        <div class="form-check<?=$inline ?>">
            <input name="<?=$name ?>" value="<?=$opt ?>" type="radio" id="<?=$id ?>"<?=$checked?>>
            <label for="<?=$id ?>"><?=$opt ?></label>
        </div>
<?php

    }
?>
    </div>
</div>