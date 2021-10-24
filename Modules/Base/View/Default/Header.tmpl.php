<?php 

$title = $this->page_info['element']['title'];
$description = $this->page_info['element']['description'];

if (count($this->data['elements'])==1)
    $element = $this->data['elements'][0];
else
    $elements = $this->data['elements'];

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?=$title?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        
        <meta name="description" content="<?=$description?>" />
        <meta property="twitter:card" content="<?=$title?>">
        <meta property="twitter:site" content="<?=DOMAIN_NAME?>">
        <meta property="twitter:creator" content="@lottologia.com"> <!-- Open Graph Meta-->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="<?=DOMAIN_NAME?>">
        <meta property="og:url" content="https://<?=DOMAIN_NAME?>"> 
        <meta property="og:image" content="https://<?=DOMAIN_NAME ?>/logo.webp" />
        <meta property="og:title" content="<?=$title?>" />
        <meta property="og:description" content="<?=$description?>" /> 
        <meta name="apple-mobile-web-app-capable" content="yes">
        
        <link rel="stylesheet" href="style.css">
        <script src="script.js"></script>
    </head>
    <body>
