<?php

$link_base = '?area='.$area.'&amp;';

$bonds = '';
?>
<div class="row m-1" id="core_content">
  <div class="table-responsive">
    <table class="table table-striped table-hover table-bordered table-condensed">
        <thead>
                <tr class="active">
<?php
          $sorted = 'ASC';
          $table_mixed_col = array(); // contenitore dei nomi delle colonne della tabella (in caso di contenuti misti tipo metadata effettua un mix)
          if ( isset($this->structure['items']) )
          {
            foreach( $this->structure['items'] as $index=>$col )
            if ( $col['type']!='hidden' && 
                 ($this->parameters['exclude_items']===NULL || (!in_array($index, $this->parameters['exclude_items']) && !in_array('id_'.$index, $this->parameters['exclude_items']))) 
               )
            {
    // echo "<pre>$index "; print_r( $col ); echo "</pre>";
              $table_mixed_col[$index] = $index;
              
              $sorted = 'ASC';
              $selected = false;
              if ( isset($this->parameters['order_item']) && isset($this->parameters['order_type']) )
                if ( $this->parameters['order_item'].' '.$this->parameters['order_type'] == $col['db_name'].' ASC' ) 
                {
                  $sorted = 'DESC';
                  $selected = true;
                }
                else
                  if (  $this->parameters['order_item'].' '.$this->parameters['order_type'] == $col['db_name'].' DESC' )
                  {
                    $sorted = 'ASC';
                    $selected = true;
                  }
                  
                if ( strstr($index,'+') ) // Tabella di dettaglio
                {
?>
                    <th class="success text-<?php print $col['text_align']; ?>"><?php print TRANSLATE($col['realname']);  ?></th>
<?php
                }
                else
                if ( strstr($index,':') ) // cross table
                {
?>
                    <th class="warning text-<?php print $col['text_align']; ?>"><?php print TRANSLATE($col['realname']); ?></th>
<?php
                }
                else
                {
?>
                    <th class="sortable text-<?php print $col['text_align']; ?>" onclick="set_order_table('<?php print $sorted ?>','<?php print $col['db_name'] ?>')"><?php
              if ($selected) { ?><i class="fa fa-chevron-<?php print (($sorted != 'DESC')?('down'):('up')) ?>"></i><?php }
              print TRANSLATE($col['realname']);
            ?></th>
<?php
                }
            }
          }

          // Tabelle di dettaglio
          if ( isset($this->structure['details']) )
          {
            $total_types = count($this->structure['details']);
            foreach( $this->structure['details'] as $datatype=>$detail_items )
            {
              foreach( $detail_items['items'] as $ind=>$item )
                if ( !isset($table_mixed_col[$ind]) ) 
              {
                $table_mixed_col[$ind] =$ind;
?>
      <th class="success nowrap"><?php print TRANSLATE($item['realname']); ?></th>
<?php
              }
            }
          }

          // Tabelle cross relate
          if ( false && isset($this->parameters['cross_tables']['available']) )
            foreach( $this->parameters['cross_tables']['available'] as $cross_table=>$cross_items )
            {
              $cross_table_name = str_replace( array($this->structure['table'].'_','_'.$this->structure['table']), '', $cross_table );
              $cross_table_name = trim(ucwords(str_replace( '_', ' ', $cross_table_name )));

?>
      <th class="warning nowrap"><?php print TRANSLATE($cross_table_name); ?></th>
<?php
            }
?>
          </tr>
        </thead>
        <tbody id="main_table">
<?php
          if ( isset($this->elements) )
          {
            $row_color = false;
            foreach( $this->elements as $id=>$element )
            {
              $row_color = !$row_color;
?>
          <tr onclick="document.location='<?php print INDEX_PAGE?>?area=<?php print $user->GetAreaParameter('name')?>&amp;do=<?php print $this->parameters['do_on_detail']?>&amp;id=<?php print $element['id']?><?php if (isset($bonds) && $bonds!='') print '&amp;bonds='.$bonds ?>';">
<?php
              $table = $this->structure['table'];
              foreach( $table_mixed_col as $index )
              {
                if ( $this->parameters['exclude_items']===NULL || !in_array($index, $this->parameters['exclude_items']) )
                {
                  $structure = NULL;
                  if ( isset($this->structure['items'][$index]) )
                    $structure = $this->structure['items'][$index];
                  else
                    if ( isset($this->structure['details'][$table.'type']['items'][$index]) )
                      $structure = $this->structure['details'][$table.'type']['items'][$index];

                  $text_align = $structure['text_align'];

                  if ( $structure!==NULL && $structure['empty_null']==1 )
                    $null_value = TRANSLATE( 'ALL' );
                  else
                    if ( $structure!==NULL )
                      $null_value = '';
                    else
                    {
                      $null_value = 'X';
                      $text_align = 'center';
                    }

                    $value = ((isset($this->elements[$id][$index]))?($this->elements[$id][$index]):($null_value));
                    if ( is_array($value) )
                    {
                        if ( isset($value['text_settings']) )
                        {
                          $val = array( );
                          foreach ( $value['text_settings'] as $nvts=>$vts )
                            $val[] = '<strong>'.$nvts.'</strong>:'.$vts; // TRANSLATE( $vts );
                          $value = implode( '<br />', $val );
                        }
                        else
                        {
                            $all_attributes = array( );
                            foreach( $value as $attr=>$val )
                            if ($val && $val!="")
                            {
                              if ( substr($attr,0,3)=='img' )
                                  $all_attributes[] = "<strong>".TRANSLATE($attr)."</strong>: <img style=\"max-width:150px;\" alt=\"".$val."\" src=\"".UPLOAD_URL.$val."\" />";
                              else
                              {
                                if (is_array($val) && isset($val['name']) )
                                  $val_text = $val['name'];
                                else
                                  $val_text = $val;
                                if (in_array($structure['db_type'],array('enum','set')))
                                  $val_text = TRANSLATE($val_text);
                                $all_attributes[] = '<strong>'.TRANSLATE($attr).'</strong>: '.$val_text;
                              }
                            }
                            $value = implode( '<br />', $all_attributes );
                        }
                    }
                    else
                      if ( $value && $value!="" )
                      {
                        if ( substr($index,0,3)=='img' )
                            $value = "<img style=\"max-width:150px;\" alt=\"".$value."\" src=\"".UPLOAD_URL.$value."\" />";
                        else
                            $value = htmlentities( $value );
                      }

                    if ( isset($this->parameters['related_values'][$index][$value]) )
                    {
                        if ( is_array($this->parameters['related_values'][$index][$value]) && 
                             isset($this->parameters['related_values'][$index][$value]['name']) )
                            $value = $this->parameters['related_values'][$index][$value]['name'];
                        else
                            $value = $this->parameters['related_values'][$index][$value];
                    }
                    if ( in_array($structure['type'],array('file','image','preview')) && $value!="")
                    {
                        // $value = $date;
                        $value = '<img width="150" src="'.UPLOAD_URL.$value.'" alt="'.$value.'" />';
                    }
                    else
                    if ( in_array($structure['db_type'],array('date','datetime','time')) )
                    {
                        $time = "";
                        if ( $structure['db_type'] != 'date'  ) 
                            $time = " H:i";

                        $date = "";
                        if ( $structure['db_type'] != 'time'  ) 
                        {
                            if ( $user->GetLanguage()=='en' )
                                $date = TransformDate2Text( $value, "X Q d, Y$time" );
                            else
                                $date = TransformDate2Text( $value, "X, d Q Y$time" );
                        }
                        $value = $date;
                    }
                    else
                      if (in_array($structure['db_type'],array('enum','set')))
                        $value = TRANSLATE($value);
                        
                    if ($this->structure['items'][$index]['db_type']=='text' && strlen($value)>60)
                      $value = substr($value, 0, 57).'...';
?>
          <td class="text-<?php print $text_align?>"><?php if ($index=="id") { ?><a href="<?php print INDEX_PAGE?>?area=<?php print $user->GetAreaParameter('name')?>&amp;id=<?php print $element['id']?><?php if (isset($bonds) && $bonds!='') print '&amp;bonds='.$bonds ?>"><?php print $value; ?></a><?php } else print $value; ?></td>
<?php
                }
              }

              // Tabelle cross relate
              if ( false && isset($this->parameters['cross_tables']['available']) )
                foreach( $this->parameters['cross_tables']['available'] as $cross_table=>$cross_items )
                {
?>
        <td class="nowrap text-left">
<?php
                  if ( false && isset($this->parameters['cross_tables']['elements'][$cross_table]) )
                  foreach( $this->parameters['cross_tables']['elements'][$cross_table] as $id_cross_table=>$row )
                    if ( $row['id_'.$this->structure['table']]==$element['id'] )
                    {
    // echo "<h1>CI SONO</h1>";
    // echo "<pre>";
    // print_r( $row );
    // echo "</pre>";

                      $vout = array( );
                      foreach( $row as $name=>$col_value )
                        if ( $name != 'id' && $name!='id_'.$this->structure['table'] )
                        {
                          if ( $col_value == NULL )
                            $col_value = 'NULL';
    // echo "<pre class='nowrap'>$col_value ";print_r( $cross_items[$name] );echo "</pre>";
                          if ( isset($cross_items[$name][$col_value]['name']) )
                          {
                            if ( isset( $row['selected_value']) )
                                $name_property = $cross_items[$name][$col_value]['name'];
                            else
                                $vout[] = "<strong>".TRANSLATE($name).": </strong>".$cross_items[$name][$col_value]['name'];
                          }
                          else   
                            if ( $name=='selected_value' )
                            {
                              if ( isset($this->parameters['cross_tables']['option_values'][$cross_table]['items'][$id_cross_table][$col_value]['name']) )
                                $vout[] = "<strong>".TRANSLATE($name).": </strong>".($this->parameters['cross_tables']['option_values'][$cross_table]['items'][$id_cross_table][$col_value]['name']);
                              else
                              {
                                if ( substr($name_property,0,3)=='img' ) 
                                    $vout[] = "<strong>$name_property:</strong><br /><img src=\"".UPLOAD_URL."$col_value\" />";
                                else
                                if ( substr($name_property,0,6)=='color' ) 
                                    $vout[] = "<strong>$name_property:</strong><div style=\"height: 50px; width: 50px; background-color: $col_value\">&nbsp;</div>";
                                else
                                    $vout[] = "<strong>$name_property:</strong>".$col_value;
                              }
                            }
                            else
                                if ( $col_value === NULL || $col_value =='NULL' )
                                    $vout[] = "<strong>".TRANSLATE($name).": </strong>".TRANSLATE('ALL'); // ;
                                else
                                    $vout[] = "<strong>".TRANSLATE($name).": </strong>".$col_value; // ;
                        }
                      print implode('<br />',$vout)."<br />";
                    }
    /*
                if ( isset($cross_items) )
                  foreach( $cross_items as $cross_item_name=>$cross_table_rows )
                  {
                    print implode( ',',$cross_table_rows['elements'] );
                  }
    */
?>
            </td>
<?php
                }
?>
          </tr>
<?php
            }
          }
?>
        </tbody>
      </table>
  </div><!-- responsive table -->
</div><!-- end core content -->
