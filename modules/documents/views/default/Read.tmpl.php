<?php 
if (isset($this->page_info)) 
{
    if (isset($this->page_info['module'])) $module = $this->page_info['module']; 
    if (isset($this->page_info['element'])) $element = $this->page_info['element']; 
}
?>
<i><?=$module['title'];?></i>
<h1><?=$element['name'] ?? '';?></h1>
<h2><?=$element['description'];?></h2>
<?php print '<p>'.($element['body'] ?? '').'</p>'; ?>

<pre>        

    <h2>PAGE INFO (PAGE MODULE)</h2>
    <?php print_r($this->page_info); ?>

    <!-- page content -->
    <h2>DATA</h2>
    <?php print_r($this->data); ?>

    <h2>USER</h2>
    <?php print_r($user); ?>

</pre>

<!-- h2>EDITOR</h2>
<div id="editor" contenteditable="true" placeholder="write something">
write something here....
</div -->