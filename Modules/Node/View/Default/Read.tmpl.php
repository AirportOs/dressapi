<i><?=$this->page_info['module']['title'];?></i>
<h1><?=$title?></h1>
<h2><?=$description?></h2>
<?php if (isset($element)) print '<p>'.$element['body'].'</p>'; ?>

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
<div id="editor" contenteditable="true" placeholder='write something'>
write something here....
</div -->