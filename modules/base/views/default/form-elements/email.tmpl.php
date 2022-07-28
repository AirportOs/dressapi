<div class="form-group">
    <label for="item_<?=$element_structure['field']?>"><?=$element_structure['display_name']?></label>
    <input type="email" class="form-control" id="item_<?=$element_structure['field']?>" 
           aria-describedby="text_help_<?=$element_structure['field']?>" placeholder="{{'Enter text'}}"
           name="<?=$name ?>" value="<?=$value ?>">
    <small id="text_help_<?=$element_structure['field']?>" class="form-text text-muted"><?=$element_structure['comment']?></small>
</div>
