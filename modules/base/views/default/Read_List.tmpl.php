<div class="container">
    <h1>{{title}}</h1>
    <div class="table-responsive">
        <table class="table table-striped table-sm">

        <thead>
            <tr>
                <th><?=_T('Operations') ?></th>
                <?php 
            foreach($this->data[0] ?? [] as $name=>$col) 
            { 
                    ?><th>{{_T(ucwords(trim(str_replace('_',' ',$col))))}}</th>
<?php       } 
?>
            </tr>
        </thead>

        <tbody>
<?php 
            foreach($this->data['elements'] ?? [] as $p=>$elem) 
            { 
                if ($p==0)
                {
?>
                <tr>
                    <th><?=_T('Operations') ?></th>
<?php 
                    foreach($elem ?? [] as $name=>$val) 
                    { 
?>                  <th><?=$name?></th>
<?php               } 
?>
                </tr>
<?php                        
                }
?>
                <tr>
                    <td><a class="btn btn-secondary m-1" href="/<?=$this->data['metadata']['module']?>/<?=$elem['id']?>" title="<?=_T('Go To').': '.''?>"><?=_T('Details')?></td>
<?php 
                    foreach($elem ?? [] as $name=>$val) 
                    { 
?>                  <td><?=$val?></td>
<?php               } 
?>
                </tr>
<?php       } 
?>
        </tbody>

        </table>
    </div>
</div>




<!-- START EXAMPLE -->
<div class="container">
<b>BASE - {{creation_date}}</b>
<h1>{{title}}</h1>
<img src="/upload/img/{{img}}" style="float:right;width:40%;">
<h2><i>{{description}}</i></h2>
<h3>{{body}}</h3>
<p><br></p>

<?php 

echo "<pre>";print_r($this);echo "</pre>";
?>

</div>
<!-- END EXAMPLE -->

