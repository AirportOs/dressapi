<?php
    if ( $element_structure['realname']=='Id' )
      $element_structure['type'] = 'hidden';

//printr($this);
    if ( isset($element[$index]) && ($action!='InsertForm' || !isset($element_structure['default'])) ) 
      $value = $element[$index];
    else
      if ( $action == 'InsertForm' && isset($element_structure['default'])  && $element_structure['default'] )
        $value = $element_structure['default'];
      else
        if ( isset($element_structure['empty_null']) && $element_structure['empty_null'] )
          $value = NULL;
        else
          $value = '';
    if (isset($_SESSION[DB_NAME]['force_value'][$area][$index]) )
    {
      $value = $_SESSION[DB_NAME]['force_value'][$area][$index];
      unset( $_SESSION[DB_NAME]['force_value'][$area][$index] );
    }

    if (isset($element_structure['group']) && $prev_group!=$element_structure['group'])
    {
      if ($prev_group!='' && $prev_group!='single') // end well
      {
        $open_well = false;
?></div></div></div><?php 
      }
      $prev_group = $element_structure['group'];
      
      if ($element_structure['group']!='single')
      {
        $open_well = true;
        if (isset($element_structure['elem_per_row']) && $element_structure['elem_per_row']>0)
          $elem_per_row = 12/$element_structure['elem_per_row'];
        else
          $elem_per_row = 4; // 3 elementi per riga
?>
<div class="card card-primary clearfix mb-2 p-2" style="background:#eee;">
            <div class="card-heading"><h3 class="text-muted"><?php print TRANSLATE($element_structure['group'])?></h3></div>
            <div class="card-body p-2">
                <div class="row">
<?php
      }
    }

    if ( !in_array($element_structure['type'], array('hidden')) )
    {
      $required = (in_array($element_structure['db_name'], $this->parameters['required_items'] ));
?>
        <div class="form-group<?php if (isset($open_well) && $open_well) print ' col-12 col-sm-6 col-md-'.$elem_per_row; ?>"<?php if (in_array($element_structure['type'],array('number','float'))) print  'style="max-width: 250px;"'; ?>>
                <label><strong><?php print TRANSLATE($element_structure['realname']).(($required)?(' *'):('')) ?></strong></label>
<?php 
      $info = TRANSLATE('NOTE:'.$element_structure['realname'], NULL, true, true);
      // 2020 TRANSLATE
      // if ( $info!==NULL && $info!="" && strtolower(TRANSLATE($info))!=strtolower(TRANSLATE($element_structure['realname'])) && !$open_well)
      if ( $info!==NULL && $info!="" && strtolower($info)!=strtolower($element_structure['realname']) && !$open_well)
      {
?>
                        <br /><span class="help-block" style="margin:0;padding:0;font-size:0.8em;" id="id_item_<?php print $id_element ?>_<?php print $index?>_desc"><?php print $info; ?>&nbsp;</span>
<?php
      }
    }

    switch( $element_structure['type'] )
    {
      case 'hidden':
?>
        <input type="hidden" name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]" value="<?php print $value?>" />
<?php
                  break;
      case 'read_only':
                  print "<br>".$value;
                  break;
      case 'password':

?>
        <input class="form-control" type="password" placeholder="password" name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]" value="<?php print $value?>"<?php if ($required) print ' required' ?>/>
<?php
                  break;

      case 'datetime':
      case 'date':
      case 'time':
                    $calendar_name = 'calendar_'.$id_element.'_'.str_replace( ':', '_', $index );
                    $value_view = '';
                    if ( $element_structure['type']=='date' )
                    {
                      if ($value!="")
                         $calendar_date = $value;
                      else
                         $value = $calendar_date = date('Y-m-d');
                      $format = "yyyy-MM-dd";
                      $v = explode( '-',$value );
                      if ( TRANSLATE('#DATE_FORMAT')=='d-m-Y' )
                        $value_view  = sprintf( "%02d-%02d-%04d", $v[2], $v[1], $v[0] ); // Formato latino (Little Endian)
                      else
                        $value_view  = sprintf( "%02d-%02d-%04d", $v[1], $v[2], $v[0] ); // Formato inglese (Middle Endian)
                    }
                    else // time
                    if ( $element_structure['type']=='time' )
                    {
                      if ($value=="")
                        $value = $calendar_time = date('H:i').':00';
                      else
                        $calendar_time = $value;
                      list( $cal_hh, $cal_mm, $cal_ss ) = explode(':',$calendar_time);
        
                      $format = "hh:mm:ss";
                      $value_view = $value;
                    }
                    else // datatime
                    {
                      if ($value!="" && strstr($value,' ') )
                         list($calendar_date, $calendar_time ) = explode( ' ', $value );
                      else
                      {
                        $calendar_date = date('Y-m-d');
                        $calendar_time = date('H:i').':00';
                        $value = $calendar_date.' '.$calendar_time;
                      }
                      list( $cal_year, $cal_month, $cal_day ) = explode('-',$calendar_date);
                      list( $cal_hh, $cal_mm, $cal_ss ) = explode(':',$calendar_time);
                      $format = "yyyy-MM-dd hh:mm:ss";
                      
                      if ( TRANSLATE('#DATE_FORMAT')=='d-m-Y' )
                        $value_view  = sprintf( "%02d-%02d-%04d", $cal_day, $cal_month, $cal_year ); // Formato latino (Little Endian)
                      else
                        $value_view  = sprintf( "%02d-%02d-%04d", $cal_month, $cal_day, $cal_year ); // Formato inglese (Middle Endian)
                      
                      $value_view .= ' '.$calendar_time;
                    }

?>
                <div class="input-group date" id="id_<?php print $calendar_name?>_datetime">
                    <input type="hidden" data-format="<?php print $format; ?>" name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]" value="<?php print $value ?>" />
                        <div class="input-group col-11 col-sm-5 col-md-4">
                            <input id="id_<?php print $calendar_name?>_datetime_view" type="text" value="<?php print $value_view; ?>" class="form-control"<?php if ($required) print ' required' ?>>
                            <div class="addon input-group-append pl-3">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div>
                </div>
                <script type="text/javascript">
                  $(function() {
                    $('#id_<?php print $calendar_name?>_datetime').datetimepicker({
<?php 
                      print "                      language: '".$user->GetLanguage()."',";
?>
                      maskInput: true,
                      pickTime: <?php print ( ($element_structure['type']=='datetime' || $element_structure['type']=='time') ? ("true") : ("false") ); ?>,
                      pickDate: <?php print ( ($element_structure['type']=='datetime' || $element_structure['type']=='date') ? ("true") : ("false") ); ?>
                    }).on('changeDate', 
                            function(ev)
                            {
                                var date = new Date(ev.date.valueOf());
                                var d = date.getUTCDate( ),  m = date.getUTCMonth( )+1, y = date.getUTCFullYear( );
                                var result = '';
<?php
                     if ($element_structure['type']=='datetime' || $element_structure['type']=='date')
                     {
?>
<?php 
                        if ( TRANSLATE('#DATE_FORMAT')=='d-m-Y' )
                        {
?>
                                  result += ((d<10)?('0'):('')) +d + '-' + ((m<10)?('0'):(''))+ m + '-' + y;
<?php
                        }
                        else
                        {
?>
                                  result += ((m<10)?('0'):(''))+ m + '-' + ((d<10)?('0'):('')) +d + '-' + y;
<?php
                        }
                     }
                     
                     if ($element_structure['type']=='datetime' || $element_structure['type']=='time')
                     {
?>
                                  var H = date.getUTCHours( ), i  = date.getUTCMinutes( ), s = date.getUTCSeconds( );
                                  if ( result != '' )
                                    result += ' ';
                                  result += ((H<10)?('0'):('')) + H +  ':' + ((i<10)?('0'):(''))+ i + ':' + ((s<10)?('0'):(''))+s;
<?php
                     }
?>
                                $( '#id_<?php print $calendar_name?>_datetime_view' ).val( result );
                            });
                  });                      
          </script>
<?php
                  break;

      case  'file':
      case 'image':
?>
        <div class="row">
        <div class="col-12">
            <input type="hidden" name="FormFiles[<?php print $form_group_name ?>][<?php print $id_element ?>][<?php print $index?>]" value="<?php print $value ?>" />
            <input type="file" name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]"/>
<?php
                if ( $value !="" && strstr($value, '.' ) )
                {
                  $v = explode( '.', strtolower($value) );
                  $ext = $v[count($v)-1];

                  if ( in_array($ext, array('png','jpg','jpeg','gif')) )
                  {
                    $image_exists = false;                      
                    $file_image = UPLOAD_URL.'users/u'.$element['id_user'].'/img/'.$value;
                    if (file_exists($file_image))
                      $image_exists = true;
                    else
                    {
                      $file_image = UPLOAD_URL.'users/u'.$element['id_user'].'/img/default_preview.png'; // Immagine di default dell'utente
                      if (file_exists($file_image))
                        $file_image = UPLOAD_URL.'img/default_preview.png'; // Immagine di default generale 
                    }
                    
                    if (file_exists($file_image)) 
                    {
?><div id="<?php print md5($file_image) ?>"><?php
                      if ($image_exists)
                      {
?>
<div class="btn btn-danger" title="<?php print TRANSLATE('Delete') ?>" 
            onclick="if (confirm('<?php print TRANSLATE('Are you sure to delete this image?') ?>')) $.get('./', 'area=<?php print $area; ?>&amp;do=Modify_DeleteAttach&id=<?php print $id_element; ?>&amp;item=<?php print $index?>', function(data){ $('#<?php print md5($file_image) ?>').remove(); }); return false;"><i class="fa fa-remove"></i></div>
<?php
                      }
?><br /><img class="col-10" alt="<?php print $value; ?>" src="<?php print $file_image; ?>" /><br />
</div>
<?php
                    }
                  }
                  else
                  {
?><a href="./?area=<?php print $area?>&amp;do=Download&amp;id=<?php print $element['id'] ?>"><?php print TRANSLATE('Download') ?></a><?php
?>
<div class="btn btn-danger" title="<?php print TRANSLATE('Delete') ?>" 
            onclick="if (confirm('<?php print TRANSLATE('Are you sure to delete this image?') ?>')) $.get('./', 'area=<?php print $area; ?>&amp;do=Modify_DeleteAttach&id=<?php print $id_element; ?>&amp;item=<?php print $index?>', function(data){alert(data);}); return false;"><i class="fa fa-remove"></i></div>
<?php
                  }
                }
?>
        </div>
        </div>
<?php 
                break;
      
      case 'preview':
                  $vext = explode( ".", $value );
                  $ext = $vext[count($vext)-1];
                  if ( in_array(strtolower($ext),array('png','jpg','jpeg','gif') ) )
                  {
?><br /><img  style="max-width:150px;max-height:220px;" src="<?php if (file_exists(UPLOAD_URL.'u'.$element['id_user'].'/img/'.$value)) print UPLOAD_URL.'u'.$element['id_user'].'/img/'.$value; else if (file_exists(UPLOAD_URL.'u'.$element['id_user'].'/img/default_preview.png')) print UPLOAD_URL.'u'.$element['id_user'].'/img/default_preview.png'; else print UPLOAD_URL.'link-preview/default_preview.png' ?>" />
<?php
                  }
                  else
                  {
                    list($filename,) = explode( $ext, $value );
                    $filename .= $ext;
?><br /><a href="./?area=<?php print $area ?>&amp;do=Download&amp;id=<?php print $id_element ?>"><?php print $filename; ?></a>
<?php
                  }
                  break;

      case 'preview_file':
                  $vext = explode( ".", $value );
                  $ext = $vext[count($vext)-1];
                  if ( in_array(strtolower($ext),array('png','jpg','jpeg','gif') ) )
                  {
?><br /><img width="250" src="<?php print UPLOAD_URL.$value; ?>" />
        <input type="file" name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]" value="<?php print $value?>" />
<?php
                  }
                  else
                  {
                    if ( $value != "" )
                    {
                      list($filename,) = explode( $ext, $value );
                      $filename .= $ext;
                    }
                    else
                      $filename = "";

?><br /><a href="./?area=<?php print $area ?>&amp;do=Download&amp;id=<?php print $id_element ?>"><?php print $filename; ?></a>
        <input type="file" name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]" value="<?php print $value?>" />
<?php
                  }
                  break;

      case 'number':
?>
        <input class="form-control" step="1" min="0" type="number" name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]" value="<?php print $value?>"<?php if ($required) print ' required' ?> />
<?php
                  break;
      
      case 'float':
?>
        <input class="form-control" type="number" step="0.01" min="0"  name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]" value="<?php print $value?>"<?php if ($required) print ' required' ?> />
<?php
                  break;

      case 'text':
?>
        <input class="form-control" type="text" name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]" value="<?php print $value?>"<?php if ($required) print ' required' ?> />
<?php
                  break;


      case 'readonly-text':
?>
        <pre><?php print $value ?></pre>
        <input type="hidden" name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]" value="<?php print $value?>" />
<?php
                  break;


      case 'image':
?>
                                    <img class="col-12" src="<?php print UPLOAD_PATH.$value ?>" alt="<?php print $value ?>"/>
<?php
                  break;

      case 'html':
?>
      <textarea class="html form-control" id="text_area_<?php print $index?>"
                            name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]" 
                            rows="24"
                          ><?php print str_replace("<","&lt;",str_replace(">","&gt;",$value))?></textarea>

<?php
                  $ids_html_editor[] = 'text_area_'.$index;
                  break;

      case 'textarea':
?>
        <textarea class="form-control" id="text_area_<?php print $index?>" name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]" rows="<?php print ((isset($element_structure['extras']['rows']))?($element_structure['extras']['rows']):(4))?>"><?php print $value?></textarea>
<?php
                  break;

      case 'select':
//          printr($element_structure['values']);
//          printr($this->parameters['related_values'][$index]);
?>
        <select class="form-control selectpicker" name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]">
<?php
          if ( isset( $element_structure['values']) )
            foreach( $element_structure['values'] as $option_value=>$option_text )
            {
              $is_selected = ( $value==$option_value || ($option_value=='NULL' && $value==NULL) || ($value==='NULL' && $option_value==NULL) || (!isset($value) && isset($element_structure['default_value'])) );
?><option value="<?php print $option_value ?>"<?php print (( $is_selected )?(' selected="selected"'):(''))?>><?php print TRANSLATE($option_text); ?></option>
<?php
            }
          if ( isset( $this->parameters['related_values'][$index]) )
            foreach( $this->parameters['related_values'][$index] as $option_value=>$option_text )
            {
                if ( is_array($option_text) )
                {
//                    if (isset($option_text['id_parent']) )
                    if ( isset($option_text['sons'] ) )
                    {
                        if (isset($option_text['name']))
                            $label = $option_text['name'];
                        else
                            $label = $index.'_sons';
                        
                        print '<optgroup label="'.$label.'">'."\r\n";
                        foreach( $option_text['sons'] as $son_id=>$son_name )
                        {
                          if ($son_id=='NULL')
                            $son_name= TRANSLATE('#UNDEFINED');
                          else
                            if ($element_structure['db_type']=='enum')
                              $son_name = $text; // 2020 TRANSLATE

                          $is_selected = ( $value==$son_id || ($son_id=='NULL' && $value==NULL) || ($value==='NULL' && $son_id==NULL) || (!isset($value) && isset($element_structure['default_value'])) );
?><option value="<?php print $son_id ?>"<?php print (( $is_selected )?(' selected="selected"'):(''))?>><?php print TRANSLATE($son_name); ?></option>
<?php
                        }
                        print '</optgroup>'."\r\n";
                        continue;
                    }
                    else
                        if (isset($option_text['name']) )
                        {
                          $text = $option_text['name']; 
                          if (isset($option_text['option_values']))
                            $text .= " (".$option_text['option_values'].")"; 
                        }
                        else
                          $text = implode(', ', $option_text );
                }
                else 
                    $text = $option_text;
          
                $is_selected = ( $value==$option_value || ($option_value=='NULL' && $value==NULL) || ($value==='NULL' && $option_value==NULL) || (!isset($value) && isset($element_structure['default_value'])) );

                if ($option_value=='NULL')
                  $text = TRANSLATE('#UNDEFINED');
                // 2020 TRANSLATE
                // else
                //  if ($element_structure['db_type']=='enum')
                //    $text = TRANSLATE($text);
?>
              <option value="<?php print $option_value?>"<?php print (( $is_selected )?(' selected="selected"'):(''))?>><?php print TRANSLATE($text); ?></option>
<?php
            }
?>
        </select>
<?php
                  break;

      case 'dynamic':
        $area_item = str_replace('id_','',$index);
        if ($area_item=='parent')
          $area_item = $user->GetAreaParameter('name');
?>
            <div class="card card-primary" style="margin-top:0;background:#eee;">
                  <div class="card-body">
                      <a style="margin-left: 10px; clear:right;" onclick="edit_dynamic_item( '<?php print $area_item; ?>', 'dynamic_<?php print $index ?>', '<?php print $value; ?>' );" data-toggle="modal" href="#edit_dynamic_value" class="btn btn-warning btn-xs">edit</a>
                      <input class="form-control" id="item_dynamic_<?php print $index ?>" type="hidden" name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]" value="<?php print $value?>" /><br />
                      <div style="background: #dddddd;min-width: 250px;border-radius: 4px;padding:4px;" id="dynamic_<?php print $index ?>"><?php if (isset($this->parameters['related_values'][$index][$value])) print $this->parameters['related_values'][$index][$value]; else if ($value===null) print TRANSLATE('#UNDEFINED'); else print $value; ?></div>
                  </div>
            </div>        
<?php
                  break;

      case 'radio':
?>
            <div class="card card-primary" style="margin-top:0;<?php if (!isset($open_well) || !$open_well) print 'background:#eee;'; ?>">
                  <div class="card-body">
<?php

        $first_selected = false;
        if (strstr($index, ":" ))
        {
          list( $item_table, $item ) = explode( ':', $index );
          if ( isset($element_structure['values']['id_'.$item]) )
            $struct = $element_structure['values']['id_'.$item];
          else
            $struct = $element_structure['values'];
        }
        else
        if (isset($element_structure['values']))
          $struct = $element_structure['values'];
        else
          $struct = null;
//        if (isset($this->structure['details'][$this->parameters['producttype']['name']]))
//          $struct = $this->structure['details'][$this->parameters['producttype']['name']];

        if ( isset($struct) )
          foreach( $struct as $option_value=>$option_text )
          {
            $readonly = false;
            if ( $option_text!="" && $option_text{0}=='*' )
            {
              $option_text = substr( $option_text, 1 );
              $readonly = true;
            }

            if ($option_text=='NULL')
              $option_text = TRANSLATE('#UNDEFINED');
            // 2020 TRANSLATE
            else
              if ($element_structure['db_type']=='enum')
                $option_text = TRANSLATE($option_text);
            if ($open_well)
              $class = "col-12 col-sm-6";
            else
              $class = "col-12 col-sm-6 col-md-4 col-lg-3";
?>
        <div class="<?php print $class; ?>"><label><input type="radio" <?php if ($readonly) print 'disabled="disabled"'; ?> name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>]" value="<?php print $option_value?>" <?php print (($value==$option_value || (('NULL'==$option_value || !$first_selected) && ($value===NULL || $value==="")) || (isset($element_structure['default_value']) && $option_value==$element_structure['default_value']) )?(' checked="checked"'):(''))?> /> <?php print $option_text?></label></div>
<?php
            $first_selected = true;
          }
?>
                  </div>
            </div>        
<?php
                  break;

      case 'checkbox':
?>
         <div class="col-12 bg-light mt-2">
              <div class="row p-2">
<?php
          if ( isset($element_structure['values']) )
          {
            // list( $table_prefixless, $column ) = explode( ':', $index );
            $checked_values = @array_values( $element[$index]['settings'] );
// echo "<pre>CHECKED: $index ";print_r( $element[$index] );
            foreach( $element_structure['values'] as $option_value=>$option_text )
            {
              $checked = (( (count($checked_values) && in_array($option_value, $checked_values)) || (isset($element[$index]) && in_array($option_value, explode(',', $element[$index])) ) )?(' checked="checked"'):(''));

            if ($option_text=='NULL')
              $option_text = TRANSLATE('#UNDEFINED');
            // 2020 TRANSLATE
            // else
            //   if ($element_structure['db_type']=='set')
            //      $option_text = TRANSLATE($option_text);
?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3"><label><input type="checkbox" class="mr-1" name="<?php print $form_group_name ?>[<?php print $id_element ?>][<?php print $index?>][]" value="<?php print $option_value?>" <?php print $checked?> /><?php print $option_text?></label></div>
<?php
            }
          }
?>
              </div>
         </div>
<?php
                  break;
    }

    if ( !in_array($element_structure['type'], array('hidden')) )
    {
      if ( $info!==NULL && $info!="" && $info!=$element_structure['realname'] && $open_well)
      {
?>
                        <span class="help-block" style="margin:0;padding:0;font-size:0.8em;" id="id_item_<?php print $id_element ?>_<?php print $index?>_desc"><?php print $info; ?>&nbsp;</span>
<?php
      }
?>
        </div>
<?php
    }
?>
