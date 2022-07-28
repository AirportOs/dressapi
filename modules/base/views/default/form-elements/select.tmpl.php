
<div class="bootstrap-select-wrappe mb-5">
<div class="form-group">
  <label for="item_<?=$name ?>" class="active"><?=$display_name ?></label>
  <select id="item_<?=$name ?>" name="<?=$name ?>">
<?php
    if ($element_structure['null']!='NO')
    {
?>
<option value=""></option>
<?php
    }

    $values = [];
    if (isset($element_structure['options']))
    {

      $v = explode('|',$element_structure['options']);
      $values = array_combine($v,$v);
    }
    else
    {
      $matches = [];
      $related_table_from_id = '/^'.str_replace('[related_table]','([\S]*)',RELATED_TABLE_ID).'/';
      
      if (preg_match($related_table_from_id, $name, $matches))
      {
          $rel_table = $matches[1];
          $values = $this->data['related_tables'][$rel_table];
      }

    }


    foreach($values as $key=>$opt)
    {
        $id = $name.'_'.str_replace(' ','_',$key);
        $selected = (($key==$value)?(' selected'):(''));
?>
    <option value="<?=$key ?>"<?=$selected?>><?=$opt ?></option>
<?php
    }
?>
  </select>
</div>
</div>
