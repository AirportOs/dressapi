<div class="it-datepicker-wrapper">
  <div class="form-group">
    <label for="item_<?=$name ?>"><?=$display_name?></label>
    <input style="background:#eee"  
           class="form-control it-date-datepicker" id="item_<?=$name ?>" type="time" 
           name="<?=$name ?>" value="<?=$value ?>">
    <small  id="item_<?=$name ?>" aria-describedby="text_help_<?=$name ?>" class="form-text text-muted"><?=$comment ?></small>
  </div>
</div>