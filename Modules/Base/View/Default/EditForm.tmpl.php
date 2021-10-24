<div class="content">
<div class="col-12">
<?php
// printr($this);

// printr($this->structure['items']['dispach_time_max']);
?>
<form enctype="multipart/form-data" action="<?php print INDEX_PAGE?>" method="post" name="edit_form">
<input type="hidden" name="area" value="<?php print $area; ?>" />
  <input type="hidden" name="do" value="<?php print $this->parameters['do']?>" />
  <input type="hidden" name="next[area]" value="<?php print $area; ?>" />
  <input type="hidden" name="next[do]" value="" />
  
  <input type="hidden" name="MAX_FILE_SIZE" value="<?php print MAX_FILE_SIZE; ?>" />
<?php
  if ( isset($_REQUEST['bonds']) )
  {
?>
  <input type="hidden" name="bonds" value="<?php print $_REQUEST['bonds']; ?>" />
<?php
  }
  if ( isset($_REQUEST['back']) )
  {
    $a = array('area','do','id');
    $v = explode( ',', $_REQUEST['back'] );
    foreach( $v as $i=>$val )
    {
?>
  <input type="hidden" name="next[<?php print $a[$i] ?>]" value="<?php print $val; ?>" />
<?php
        if ( isset($this->parameters['next']) && isset($this->parameters['next'][$a[$i]]) )
            unset( $this->parameters['next'][$a[$i]] );
    }
  }
?>
  
<article>

<?php
/*
  // Utilizzare questo codice se si vuole tornare al dettaglio
  if ( isset($_REQUEST['id']) )
  {
?>
  <input type="hidden" name="next[id]" value="<?php print (int)$_REQUEST['id']; ?>" />
<?php
  }
*/
// echo "<pre>"; print_r( $this->parameters ); echo "</pre>";

  $close_related = (($this->parameters['do']=="Insert")?('no'):('yes'));
  $close_related = ((isset($_REQUEST['close_related']))?($_REQUEST['close_related']):($close_related));

  if (  isset($this->parameters['next']) )
    foreach( $this->parameters['next'] as $name=>$value )
    {    
?>
  <input type="hidden" name="next[<?php print $name ?>]" value="<?php print $value ?>" />
<?php
    }
?>

    <h1><?php print TRANSLATE($area); ?> </h1>

    <div class="submit-buttons">
<?php
  if ( isset($this->parameters['buttons']) )
    foreach( $this->parameters['buttons'] as $name_button=>$button )
    if ($name_button=='Read')
    {
?>
    <a href="./?area=<?php print $user->GetAreaParameter('name')?>" class="btn btn-<?php print $button['type']; ?>"><?php print TRANSLATE($button['text']) ?></a>
<?php
    }
    else
    {
?>
    <button type="submit" name="<?php print $name_button?>" value="<?php print $name_button ?>" class="btn btn-<?php print $button['type']; if ($button['type']=='danger') print ' pull-right'; ?>" <?php if ($button['confirm']!='') print 'onclick="javascript: return confirm(\''.$button['confirm'].'\')"'; ?>><?php print TRANSLATE($button['text']) ?></button>
<?php
    }
?>
    </div><!-- submit-buttons -->

<?php
  $prev_group = ''; // Gruppo precedente
  if ( isset($this->structure['items']) && isset($this->elements) )
  {
    $form_group_name = 'FormParameters';
    $structure_items = &$this->structure['items'];
    
    $ids_html_editor = array();
    if (isset($this->parameters['first_items_base']) && count($this->parameters['first_items_base'])>0)
      $first_items_base = $this->parameters['first_items_base'];
    else
      $first_items_base = array();
      
    if (isset($this->parameters['first_items_details']) && count($this->parameters['first_items_details'])>0)
      $first_items_details = $this->parameters['first_items_details'];
    else
      $first_items_details = array();
    // printr($form_group_name);
    
    $open_well = false;
    foreach( $this->elements as $id_element=>$element )
    {
        // Principali di base
        if (isset($this->parameters['first_items_base']) && count($this->parameters['first_items_base'])>0)
          foreach( $structure_items as $index=>$element_structure )
            if (in_array($index, $first_items_base))
              include ( 'EditFormElement.tmpl.php' ); // $this->parameters['THEME'].$area.'/'
        
        // Principali di dettaglio
        if ( isset($this->structure['details']) )
        {
          $form_group_name = 'DetailParameters';
          foreach( $this->structure['details'] as $datatype=>$datatypetable )
          {
            $structure_items = &$datatypetable['items'] ;
            foreach( $structure_items as $index=>$element_structure )
              if (in_array($index,$first_items_details))
                include ( 'EditFormElement.tmpl.php' ); // $this->parameters['THEME'].$area.'/'
          }
        }

        // Secondari di base
        $form_group_name = 'FormParameters';
        $structure_items = &$this->structure['items'];
        foreach( $structure_items as $index=>$element_structure )
          if ( $this->parameters['exclude_items']===NULL || !in_array($index, $this->parameters['exclude_items']) )
          {
            if (!in_array($index,$first_items_base)) // Sono stati gia' inseriti sopra
              include ( 'EditFormElement.tmpl.php' ); // $this->parameters['THEME'].$area.'/'
          } // end for structure

        // Secondari di dettaglio
        if ( isset($this->structure['details']) )
        {
          $form_group_name = 'DetailParameters';
          foreach( $this->structure['details'] as $datatype=>$datatypetable )
          {
            $structure_items = &$datatypetable['items'];
            foreach( $structure_items as $index=>$element_structure )
              if ( $this->parameters['exclude_items']===NULL || !in_array($index, $this->parameters['exclude_items']) )
              {
                if (!in_array($index,$first_items_details))
                  include ( 'EditFormElement.tmpl.php' ); // $this->parameters['THEME'].$area.'/'
              } // end for structure
          }
        }
     
        // 2 DIV  DI UN GRUPPO EVENTUALMENTE APERTO DENTRO EditFormElement
        if (isset($open_well) && $open_well) { ?><br /></div></div><?php }
      
        if ( isset($this->parameters['cross_tables']['available']) )
        foreach( $this->parameters['cross_tables']['available'] as $cross_table=>$cross_values )
        {
          $tab = str_replace( array($this->structure['table'].'_','_'.$this->structure['table']), '', $cross_table );
          $tab_text = '';
?><h2><?php print TRANSLATE($tab); ?></h2><?php

            $can_insert = $user->GetAreaPermission( $cross_table, 'Insert' );
            
            $questionable = $checkable = false;
            if ( isset($this->parameters['cross_tables']['option_values'][$cross_table]) )
                $questionable = true;
            else
                $checkable = (count($this->parameters['cross_tables']['available'][$cross_table])==1 );
            
            if ( $can_insert )
            {
?><a title="<?php print TRANSLATE('Insert') ?>" href="./?area=<?php if ( count(explode('_',$cross_table))==2 ) print str_replace(array($user->GetAreaParameter('name').'_','_'.$user->GetAreaParameter('name')),'', $cross_table); else print $cross_table ?>&amp;do=InsertForm&amp;back=<?php print $user->GetAreaParameter('name')?>,ModifyForm,<?php print $id_element; if (!$questionable /* && !$checkable */) { print "&amp;bonds=".$this->structure['table'].urlencode(':').$id_element; } ?>"><span class="fa fa-plus-circle" aria-hidden="true"></span></a><?php
            }
          $info = TRANSLATE('#NOTE:'.$tab_text, NULL, true, true);
          if ( $info!="" && $info!=NULL && strtolower($info)!=strtolower($tab_text))
          {
?>
                        <span class="help-block" id="id_item_<?php print $id_element ?>_<?php print $index?>_desc2"><?php print $info; ?>&nbsp;</span>
<?php
          }

          if ( $questionable ) // tipo user_preference
          {
            if ( isset( $this->parameters['cross_tables']['available'][$cross_table]) )
              foreach( $this->parameters['cross_tables']['available'][$cross_table] as $name=>$row )
                if ( isset($row) )
                {
                  foreach( $row as $id=>$col )
                    if ( $name != 'id' && $name != 'id_'.$this->structure['table'] )
                    {
?><div class="well form-group"><label><a href="./?<?php print "area=$tab&amp;do=ModifyForm&amp;id=$id&amp;back=".$user->GetAreaParameter('name').",ModifyForm,$id_element"; ?>"><span class="fa fa-edit" aria-hidden="true"></span></a> <?php print ucwords(str_replace('_',' ',$col)); ?></label><br /><?php 
                      $option_values = $this->parameters['cross_tables']['option_values'][$cross_table];

// echo "<pre>$name<br />";print_r( $this->parameters['cross_tables']['elements'][$cross_table] );echo "</pre>";

                      if ( isset($option_values['items'][$id]) )
                        switch( $option_values['type'][$id] )
                        {
                          case 'tselect':
                                          $selected_value = '';
                                          if ( isset($this->parameters['cross_tables']['elements'][$cross_table]) )
                                            foreach ( $this->parameters['cross_tables']['elements'][$cross_table] as $dummy=>$opt_items )
                                              if ( $opt_items[$name]==$id )
                                              {
                                                $selected_value = $opt_items['selected_value'];
                                                break;
                                              }
?><select class="form-control" name="CrossTableParameters[<?php print $cross_table ?>][<?php print $id_element ?>][<?php print $name ?>][<?php print $id ?>]">
<?php
                                          foreach( $option_values['items'][$id] as $opt_value=>$opt_text )
                                          {
?><option value="<?php print $opt_value; ?>" <?php if ($selected_value==$opt_value) print 'selected="selected"'; ?>><?php print $opt_text; ?></option>
<?php
                                          }
?></select>
<?php
                                          break;

                          case  'tradio':
                                          $selected_value = '';
                                          if ( isset($this->parameters['cross_tables']['elements'][$cross_table]) )
                                            foreach ( $this->parameters['cross_tables']['elements'][$cross_table] as $dummy=>$opt_items )
                                              if ( $opt_items[$name]==$id )
                                              {
                                                $selected_value = $opt_items['selected_value'];
                                                break;
                                              }
                                          foreach( $option_values['items'][$id] as $opt_value=>$opt_text )
                                          {
?><div class="radio"><label><input type="radio" name="CrossTableParameters[<?php print $cross_table ?>][<?php print $id_element ?>][<?php print $name ?>][<?php print $id?>]" value="<?php print $opt_value; ?>" <?php if ($selected_value==$opt_value) print 'checked="checked"'; ?>>&nbsp;<?php print $opt_text; ?></label></div>
<?php
                                          }
                                          break;

                          case  'tcheckbox': // inusato, non testato, forse incompleto nella gestione lato server
                                          foreach( $option_values['items'][$id] as $opt_value=>$opt_text )
                                          {
                                            $selected_value = '';
                                            if ( isset($this->parameters['cross_tables']['elements'][$cross_table]) )
                                              foreach ( $this->parameters['cross_tables']['elements'][$cross_table] as $dummy=>$opt_items )
                                                if ( $opt_items[$name]==$id )
                                                {
                                                  $selected_value = $opt_items['selected_value'];
                                                  break;
                                                }
?><div class="col-12 col-sm-6 col-md-4 col-lg-3"><label><input class="form-control" type="checkbox" name="CrossTableParameters[<?php print $cross_table ?>][<?php print $id_element ?>][<?php print $name ?>][]" value="<?php print $opt_value; ?>" <?php if ($selected_value==$opt_value) print 'checked="checked"'; ?>><?php print $opt_text; ?></label></div>
<?php
                                          }
                                          break;
                          
                          case   'ttext':
                                          $selected_value = '';
                                          if ( isset($this->parameters['cross_tables']['elements'][$cross_table]) )
                                            foreach ( $this->parameters['cross_tables']['elements'][$cross_table] as $dummy=>$opt_items )
                                              if ( $opt_items[$name]==$id )
                                              {
                                                $selected_value = $opt_items['selected_value'];
                                                break;
                                              }
?><input class="form-control" type="text" name="CrossTableParameters[<?php print $cross_table ?>][<?php print $id_element ?>][<?php print $name ?>][<?php print $id?>]" value="<?php print $selected_value; ?>"></label><br />
<?php                                      
                                          break;

                          case   'tfile':
                                          $selected_value = '';
                                          if ( isset($this->parameters['cross_tables']['elements'][$cross_table]) )
                                            foreach ( $this->parameters['cross_tables']['elements'][$cross_table] as $dummy=>$opt_items )
                                              if ( $opt_items[$name]==$id )
                                              {
                                                $selected_value = $opt_items['selected_value'];
                                                break;
                                              }
/* class="form-control"  */
?><input type="file" name="CrossTableParameters[<?php print $cross_table ?>][<?php print $id_element ?>][<?php print $name ?>][<?php print $id?>]" value="<?php print $selected_value; ?>"></label><br />
<?php
  if ( $selected_value !="" && strstr($selected_value, '.' ) )
  {
    $v = explode( '.', strtolower($selected_value) );
    $ext = $v[count($v)-1];
    if ( in_array($ext, array('png','jpg','jpeg','gif')) )
    {
?><img alt="" src="<?php print UPLOAD_URL.'img/'.$selected_value; ?>" /><?php
    }
    else
    {
?><a href="<?php print UPLOAD_URL.$selected_value ?>"><?php print TRANSLATE('Download') ?></a><?php
    }
  }
                                          break;
                        }
?></div><?php
                    }
                }
          }
          else
          if ( $checkable ) // checked
          {
?>
<div class="panel panel-primary" style="margin-top:0;background:#eee;">
                  <div class="panel-body">
                  <?php

// printr($this->parameters['id_producttag_labels']);

            if ( isset( $this->parameters['cross_tables']['available'][$cross_table]) )
              foreach( $this->parameters['cross_tables']['available'][$cross_table] as $name=>$row )
              {
                if ( $row !== NULL )
                  foreach( $row as $id_opt=>$col )
                  {
                    $checked_value = false;
                    
                    if ( isset( $this->parameters['cross_tables']['elements'][$cross_table]) )
                      foreach( $this->parameters['cross_tables']['elements'][$cross_table] as $opt_id_selected=>$opt )
                        if ( $opt[$name] == $id_opt )
                        {
                          $checked_value = true;
                          break;
                        }
                    
                    if (isset($this->parameters['id_producttag_labels'][$id_opt]))
                    {
                      $label = $this->parameters['id_producttag_labels'][$id_opt];
?>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3"><label style="font-weight:bold;" class="text-danger"><input type="checkbox" name="CrossTableParameters[<?php print $cross_table ?>][<?php print $id_element ?>][<?php print $name ?>][<?php print $id_opt ?>]" value="<?php print $id_opt ?>" <?php if ($checked_value) print 'checked'; ?> />&nbsp;<span><?php if (is_array($col)) print TRANSLATE($col['name']); else print TRANSLATE($col); ?></span></label></div>
<?php
                    }
                    else
                    {
?>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3"><label><input type="checkbox" name="CrossTableParameters[<?php print $cross_table ?>][<?php print $id_element ?>][<?php print $name ?>][<?php print $id_opt ?>]" value="<?php print $id_opt ?>" <?php if ($checked_value) print 'checked'; ?> />&nbsp;<span><?php if (is_array($col)) print TRANSLATE($col['name']); else print TRANSLATE($col); ?></span></label></div>
<?php
                    }
                  }
              }
?>
        </div>
        </div>
<?php
          }
          else // tabella correlata con piu' valori tipo user_area_permission
          if (0)
          {
?>
    <div class="table-responsive">
    <input type="hidden" name="CrossTableParameters[<?php print $cross_table ?>][<?php print $id_element ?>]" value="ignore" />
<?php
    $available = &$this->parameters['cross_tables']['available'][$cross_table];
    $can_modify = $user->GetAreaPermission( $cross_table, 'Modify' );
?>
    <table class="table table-striped table-hover table-bordered">
<?php 
            $head = true;
            if ( isset( $this->parameters['cross_tables']['elements'][$cross_table]) )
              foreach( $this->parameters['cross_tables']['elements'][$cross_table] as $id=>$row )
              {
                if ($head)
                {
                  $head = false;
                  $row_head = array_keys( $row );
?>
    <thead>
    <tr>
<?php 
                    if ( $can_modify )
                    {
?><th>&nbsp;</th><?php
                    }
                  if ( isset($row_head) )
                    foreach( $row_head as $col )
                      if ( $col != 'id' && $col != 'id_'.$this->structure['table'] )
                      {
?><th><?php print TRANSLATE($col) ?></th>
<?php
                      }
?>
    </tr>
    </thead>
<?php
                }

                if ( isset($row) )
                {
?><tr><?php
/* 
    onclick="edit_cross_table( '<?php print $cross_table ?>', <?php print $id ?> )"
*/
                    if ( $can_modify )
                    {
?><td><a title="<?php print TRANSLATE('Modify') ?>" href="./?area=<?php print $cross_table ?>&amp;do=ModifyForm&amp;id=<?php print $row['id'] ?>&amp;back=<?php print $user->GetAreaParameter('name')?>,ModifyForm,<?php print $id_element; ?>">
  <span class="fa fa-edit" aria-hidden="true"></span></a></td>
<?php
                    }


                  foreach( $row as $name=>$col )
                    if ( $name != 'id' && $name != 'id_'.$this->structure['table'] )
                    {
                      if ( $col==NULL )
                        $col = 'NULL';
    ?><td class="text-left">
    <?php
                      if ( 0 && substr( $name,0,3)=='id_' ) // Non consente la modifica di cross table multiple
                      {
?><select name="CrossTableParameters[<?php print $cross_table ?>][<?php print $id_element ?>][<?php print $name ?>][<?php print $col ?>]">
<?php 
                        foreach( $available[$name] as $value=>$text )
                        {
?>    <option value="<?php if ( $value != NULL ) print TRANSLATE($value); else print 'NULL'; ?>" <?php if ($col==$value) print 'selected="selected"' ?>><?php print TRANSLATE($text); ?></option>
<?php
                        }
    ?>
    </select>
<?php  
                      }
                      if ( isset($this->parameters['cross_tables']['available'][$cross_table][$name][$col]) )
                        print $this->parameters['cross_tables']['available'][$cross_table][$name][$col];
                      else
                        if ( $col == 'NULL' || $col===NULL )
                            print TRANSLATE('UNDEFINED');
                        else
                            print TRANSLATE($col);
                      if ( $can_modify )
                      {
?></a>
<?php
                      }
?></td>
<?php
                    }
?></tr><?php
                }
              } // end for each $row as $name=>$col
            
            // Nuovi record della tabella
            if ( isset($available) )
              for( $n=1; $n<0; $n++ ) // per ogni parametro
              {
    ?><tr class="danger"><?php 
                foreach( $available as $name=>$row ) // per ogni parametro
                {
                    if ( $name != 'id' && $name != 'id_'.$this->structure['table'] )
                    {
    ?><td class="text-left">
      <select name="CrossTableParameters[<?php print $cross_table ?>][<?php print $id_element ?>][<?php print $name ?>][_NEW_<?php print $n ?>]">
    <?php 
    ?>    <option value="---"><?php print TRANSLATE('Select') ?></option>
    <?php
                      foreach( $row as $value=>$text )
                      {
                        if ( $value==NULL )
                          $value = 'NULL';
    ?>    <option value="<?php if ( $value != NULL ) print $value; else print 'NULL'; ?>"><?php print TRANSLATE($text); ?></option>
    <?php
                      }
    ?>
      </select></td>
    <?php
                    }
                }
    ?></tr><?php
              }
    ?>
    </table>
    </div>
    <?php
          }
        }
    } // end for elements (schede)  
  }
?>

<?php
  if ( isset($this->parameters['buttons']) )
  {
?><div class="submit-buttons"><br />
<?php
    foreach( $this->parameters['buttons'] as $name_button=>$button )
    {
?>
    <button class="btn btn-<?php print $button['type']; if ($button['type']=='danger') print ' pull-right'; ?>" <?php if ($button['confirm']!='') print 'onclick="javascript: return confirm(\''.$button['confirm'].'\')"'; ?> type="submit" name="<?php print $name_button?>"><?php print TRANSLATE($button['text']) ?></button>
<?php
    }
?>
</div><!-- submit-buttons -->
<?php
  }
?>

</article>

</form>
</div>
</div><!-- // end content -->
<p><br /></p>
 <!-- Modal -->
  <div class="modal" id="edit_dynamic_value">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><?php print TRANSLATE("Select a value"); ?></h4>
        </div>
        <div class="modal-body" id="modal_body_container">
          ...
        </div>
        <!-- div class="modal-footer">
          <a href="#" class="btn"><?php print TRANSLATE('Close') ?></a>
          <a href="#" class="btn btn-primary"><?php print TRANSLATE('Save changes') ?></a>
        </div -->
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  
