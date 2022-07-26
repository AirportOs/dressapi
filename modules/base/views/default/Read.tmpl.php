
<h1><?=$this->page_info['module']['title'];?></h1><?php

if (isset($this->data) && isset($this->data['elements']))
{
    foreach($this->data['elements'] as $n=>$element)
    {
?><table><tr><th>Name</th><th>Value</th></tr><?php
        foreach($element as $name=>$value)
        {
?><tr>
        <td class="bg-dark text-white pl-2 pr-2 text-nowrap fw-bold font-weight-bold align-top border-bottom"><?=ucwords(str_replace('_',' ',trim($name)))?></td>
        <td class="pl-2 pr-2 "><?=$value?></td>
    </tr>
<?php
        } // end item
?></table>
<br>
<?php

    } // end elements
}

?>

<hr>
<i>{{creation_date}}</i>
<h1>{{title}}</h1>
<h5><i>{{description}}</i></h5>
<div>{{body}}</div>
<hr>
<?php

echo '<pre>'; print_r($this);echo '</pre>';
