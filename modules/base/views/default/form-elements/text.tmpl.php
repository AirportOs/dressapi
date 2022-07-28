
<div class="form-group">
    <label for="item_<?=$name ?>"><?=$display_name?></label>
    <input <?php if ($pattern!='') { ?>pattern="<?=$pattern ?>"<?php } ?> 
           style="background:#eee" type="text" class="form-control" id="item_<?=$name?>" 
           aria-describedby="text_help_<?=$name?>" 
           name="<?=$name ?>" value="<?=$value ?>">
    <small id="text_help_<?=$name ?>" class="form-text text-muted"><?=$comment ?></small>
</div>
