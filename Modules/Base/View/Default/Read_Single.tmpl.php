<?php
// echo "<pre>"; print_r( $this->elements ); echo "</pre>";
// echo "<pre>"; print_r( $this->structure ); echo "</pre>";

if ( !isset($_REQUEST['core_content']) || $_REQUEST['core_content']=='no' ) 
{
?>
<article>

<?php
} // end core content
?>
<div id="core_content">
<?php
    $table_mixed_col = array(); // contenitore dei nomi delle colonne della tabella (in caso di contenuti misti tipo metadata effettua un mix)
    if ( isset($this->structure['items']) )
    {
        foreach( $this->structure['items'] as $index=>$col )
        if ( $col['type']!='hidden' && ( $this->parameters['exclude_items']===NULL || (!in_array($index, $this->parameters['exclude_items']) && !in_array('id_'.$index, $this->parameters['exclude_items'])) ) )
        {
        // echo "<pre>$index "; print_r( $col ); echo "</pre>";
          $table_mixed_col[$index] = $index;
          
          $ordered = 'ASC';
          $selected = false;
          if ( isset($this->parameters['order_item']) && isset($this->parameters['order_type']) )
            if ( $this->parameters['order_item'].' '.$this->parameters['order_type'] == $col['db_name'].' ASC' ) 
            {
              $ordered = 'DESC';
              $selected = true;
            }
            else
              if (  $this->parameters['order_item'].' '.$this->parameters['order_type'] == $col['db_name'].' DESC' )
              {
                $ordered = 'ASC';
                $selected = true;
              }
        }
    }

    if ( isset($this->elements) )
    {
        $row_color = false;
        foreach( $this->elements as $id=>$element )
        {
?>
      <div class="col-12">
          <div class="clearfix">
  <?php if (isset($element['name'])) print '<h1 class="pull-left">'.strtoupper($element['name']).'</h1>'; ?>
<a class="btn btn-primary pull-right ml-1" href="./?area=<?php print $area ?>" 
        title="Back to list"><span class="fa fa-arrow-up fa-2x"></span></a><?php
            if ( $this->parameters['can_modify'] )
            {
?>
<a class="btn btn-success pull-right" title="<?php print TRANSLATE('Edit') ?>" href="<?php print INDEX_PAGE.$link_base ?>do=ModifyForm&amp;id=<?php print $element['id']?><?php if (isset($bonds) && $bonds!='') print '&amp;bonds='.$bonds ?>"><span class="fa fa-refresh fa-2x"></span></a>
<?php
            }
?>
          </div>
<?php
            $table = $this->structure['table'];
            foreach( $table_mixed_col as $index )
            {
                if ( $this->parameters['exclude_items']===NULL || (!in_array($index, $this->parameters['exclude_items']) && !in_array('id_'.$index, $this->parameters['exclude_items'])) )
                {
?>
         <div class="row m-1">
                  <div class="col-5 col-sm-3 bg-primary text-white p-1"><strong><?php print TRANSLATE($this->structure['items'][$index]['realname']) ?></strong></div>
                  <div class="col-7 col-sm-9">
<?php
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
                        $val[] = '<strong>'.$nvts.'</strong>:'.TRANSLATE( $vts );
                      $value = implode( '<br />', $val );
                    }
                    else
                    {
                      $all_attributes = array( );
                      foreach( $value as $attr=>$val )
                      {
                          if ( substr($attr,0,3)=='img' && $val!="")
                              $all_attributes[] = "<strong>".TRANSLATE($attr)."</strong>: <img style=\"max-width:150px;\" alt=\"".$val."\" src=\"".UPLOAD_URL.$val."\" />";
                          else
                          {
                            if (in_array($structure['db_type'],array('enum','set')))
                              $val = TRANSLATE($val);
                            if (is_array($val))
                            {
                              $s ="";
                              foreach($val as $anam=>$aval)
                                if (strlen($aval)>60)
                                  $s .= "<b>$anam</b>: [size ".strlen($aval)."]<br />";
                                else
                                  $s .= "<b>$anam</b>: ".$aval."<br />";
                              $all_attributes[] = '<div class="bg-warning p-2"><strong>'.TRANSLATE($attr).'</strong> '.$s.'</div>';
                            }
                            else
                              if (trim(strip_tags($val))!="") $all_attributes[] = '<strong>'.TRANSLATE($attr).'</strong> '. $val;
                          }
                      }
                      $value = implode( '<br />', $all_attributes );
                    }
                  }
                  else
                      $value = htmlentities( $value );
                  if ( isset($this->parameters['related_values'][$index][$value]) )
                  {
                      if ( is_array($this->parameters['related_values'][$index][$value]) && 
                           isset($this->parameters['related_values'][$index][$value]['name']) )
                          $value = $this->parameters['related_values'][$index][$value]['name'];
                      else
                          $value = $this->parameters['related_values'][$index][$value];
                 }

                 if ( in_array($structure['db_type'],array('date','datetime','time')) )
                 {
                    $time = "";
                    if ( $structure['db_type'] != 'date' )
                    {
                      $time = "H:i";
                      if ( $user->GetLanguage()=='en' )
                        $date = TransformDate2Text( $value, $time );
                      else
                        $date = TransformDate2Text( $value, $time );
                    }
                    else
                      $date = "";
                    if ( $structure['db_type'] != 'time' ) 
                    {
                      if ( $user->GetLanguage()=='en' )
                          $date = TransformDate2Text( $value, "X Q d, Y $time" );
                      else
                          $date = TransformDate2Text( $value, "X, d Q Y $time" );
                    }
                    $value_text = $date;
                 }
                 else
                    if ( $structure['db_type'] == 'enum' || $structure['db_type'] == 'set'  ) 
                      $value_text = TRANSLATE($value);
                    else
                      $value_text = $value;

                 if ( $structure['type']=='image' && $value!="" )
                    print '<img width="150" src="'.UPLOAD_URL.$value.'" alt="'.((isset($element['name']))?($element['name'].' '):('')).TRANSLATE('Image').'" />';
                 else
                    print $value_text;
?>
                 </div>
        </div>
<?php
                }
            }

          // Tabelle cross relate
          if ( isset($this->parameters['cross_tables']['available']) )
            foreach( $this->parameters['cross_tables']['available'] as $cross_table=>$cross_items )
            {
              $first_table = true;
              if ( isset($this->parameters['cross_tables']['elements'][$cross_table]) )
              foreach( $this->parameters['cross_tables']['elements'][$cross_table] as $id_cross_table=>$row )
                if ( $row['id_'.$this->structure['table']]==$element['id'] )
                {
// echo "<h1>CI SONO</h1>";
                  if ( count($row)>3 && $first_table )
                  {
                    $tab = str_replace( array($this->structure['table'].'_','_'.$this->structure['table']), '', $cross_table );
                    $tab_text = ucwords(str_replace('_',' ',$tab));

?>
<h2><?php print $tab_text ?></h2>
<table class="table table-striped table-hover table-bordered">
<tr>
<?php
                    foreach( $row as $name=>$dummy )
                    if ( $name != 'id' && $name!='id_'.$this->structure['table'] )
                    {
                        $name = TRANSLATE($name);
?>
<th><?php print $name ?></th>
<?php
                    }
?>
</tr>
<?php
                    $first_table = false;
                    $first_table_row = true;
                  }

                  $vout = array( );
// echo "<pre>ROW:  ";print_r( $row['selected_value'] );echo "</pre>";
                  foreach( $row as $name=>$col_value )
                    if ( $name != 'id' && $name!='id_'.$this->structure['table'] )
                    {
                        if ( count($row)>3 && $first_table_row )
                        {
                            $first_table_row = false;
                            ?><tr><?php
                        }
                      if ( $col_value == NULL )
                        $col_value = 'NULL';
                      $print_name = TRANSLATE($name);
                      if ( isset($cross_items[$name][$col_value]['name']) )
                      {
                        if ( isset( $row['selected_value']) )
                        {
                            $name_property = $cross_items[$name][$col_value]['name'];
                            continue;
                        }
                        else
                            $value = $cross_items[$name][$col_value]['name'];
                      }
                      else   
                        if ( $name=='selected_value' )
                        {
                          $print_name = TRANSLATE($name_property);
                          if ( isset($this->parameters['cross_tables']['option_values'][$cross_table]['items'][$id_cross_table][$col_value]['name']) )
                            $value = ($this->parameters['cross_tables']['option_values'][$cross_table]['items'][$id_cross_table][$col_value]['name']); // TRANSLATE
                          else
                          {
// echo "<pre>VALUE: ".print_r($col_value)."</pre>";
                            if ( substr($name_property,0,3)=='img' && $col_value!="" ) 
                                $value = "<img style=\"max-width:150px;\" src=\"".UPLOAD_URL."$col_value\" />";
                            else
                            if ( substr($name_property,0,6)=='color' ) 
                                $value = "<div style=\"height: 50px; width: 50px; background-color: $col_value\">&nbsp;</div>";
                            else
                                $value = $col_value;
                          }
                        }
                        else
                            if ( $col_value === NULL || $col_value =='NULL' )
                                $value = TRANSLATE('ALL'); // ;
                            else
                                $value = $col_value; // ;

                        if ( count($row)>3 )
                        {
?>
<td><?php print $value ?></td><?php
                        }
/**/
                    }
if ( count($row)>3 )
{
?></tr><?php
}
                }
if ( isset($row) && count($row)>3 )
{
?></table><?php
}
            }
?>
      </div>
<?php
        }
?>
<hr />
<?php
    }
?>

</div><!-- end core content -->

<?php
if ( !isset($_REQUEST['core_content']) || $_REQUEST['core_content']=='no' ) 
{
?>
</article>
<?php
}
?>