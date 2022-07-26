<?php
// echo "<pre>";print_r($_SERVER);exit;
?><!doctype html>
<html lang="it">
  <head>
    

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Bootstrap Italia è un tema Bootstrap 4 per la creazione di applicazioni web nel pieno rispetto delle Linee guida di design per i servizi web della PA">
<meta name="author" content="DressApi - Tufano Pasquale">

<title>Template vuoto · Bootstrap Italia</title>

<!-- Bootstrap core CSS -->
<link href="/frameworks/bootstrap-italia/dist/css/bootstrap-italia.min.css" rel="stylesheet">

<!-- Documentation extras -->
<link href="/frameworks/bootstrap-italia/docs/assets/dist/css/docs.min.css" rel="stylesheet">

<!-- Dressapi -->
<link href="/assets/css/dressapi.css" rel="stylesheet">

<!-- Favicons -->
<link rel="icon" href="/frameworks/bootstrap-italia/favicon.ico">
<link rel="icon" href="/frameworks/bootstrap-italia/docs/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">
<link rel="icon" href="/frameworks/bootstrap-italia/docs/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">
<link rel="mask-icon" href="/frameworks/bootstrap-italia/docs/assets/img/favicons/safari-pinned-tab.svg" color="#0066CC">
<link rel="apple-touch-icon" href="/frameworks/bootstrap-italia/docs/assets/img/favicons/apple-touch-icon.png">

<link rel="manifest" href="/frameworks/bootstrap-italia/docs/assets/img/favicons/manifest.webmanifest">
<meta name="msapplication-config" content="/frameworks/bootstrap-italia/docs/assets/img/favicons/browserconfig.xml">

<meta name="theme-color" content="#0066CC">

<!-- Twitter -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@teamdigitaleIT">
<meta name="twitter:creator" content="Team per la Trasformazione Digitale">
<meta name="twitter:title" content="Template vuoto">
<meta name="twitter:description" content="Bootstrap Italia è un tema Bootstrap 4 per la creazione di applicazioni web nel pieno rispetto delle Linee guida di design per i servizi web della PA">
<meta name="twitter:image" content="https://italia.github.io/cms/docs/assets/img/favicons/social-card.png">

<!-- Facebook -->
<meta property="og:url" content="https://italia.github.io/cms/docs/esempi/template-vuoto/">
<meta property="og:title" content="Template vuoto">
<meta property="og:description" content="Bootstrap Italia è un tema Bootstrap 4 per la creazione di applicazioni web nel pieno rispetto delle Linee guida di design per i servizi web della PA">
<meta property="og:type" content="website">
<meta property="og:image" content="https://italia.github.io/cms/docs/assets/img/favicons/social-card.png">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<!-- [[INLINE-STYLES]] -->

<!-- Global site tag (gtag.js) - Google Analytics -->
<script>
// Disabilita tracciamento se il cookie cookies_consent non esiste
if (document.cookie.indexOf('cookies_consent=true') === -1) {
  window['ga-disable-UA-114758441-1'] = true;
}
</script>

<script>
if (localStorage.token == null)
    document.location = '/cms/signin/';

let list_options = 'wr/ob/id-DESC';
localStorage.list_options = list_options;
</script>


    <!-- [[INLINE-BEGIN-JS]] -->    

  </head>
  <body>
    <div class="it-header-wrapper">
  <div class="it-header-slim-wrapper">
    <div class="container">
      <div class="row">
        <div class="col-12">
          
          <div class="it-header-slim-wrapper-content">
            <a class="d-none d-lg-block navbar-brand p-2" href="<?=$CONFIG['WEBSITE_OWNER_URI'] ?>"><?=$CONFIG['WEBSITE_OWNER_NAME'] ?></a>
            
            <div class="nav-mobile">              
              <nav>
                <a class="it-opener d-lg-none" data-toggle="collapse" href="#top-menu" role="button" aria-expanded="false" aria-controls="top-menu">
                  <span><?=$CONFIG['WEBSITE_OWNER_NAME'] ?></span>
                  <svg class="icon">
                    <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-expand"></use>
                  </svg>
                </a>
                <div class="link-list-wrapper collapse" id="top-menu">
                  <div class="row">
                    <div class="col-12">
                      <ul<?=((isset($menu['TopLeftMenu']) && isset($menu['TopLeftMenu']['submenu']))?(' class="link-list"'):(''))?>>
<?php
if (isset($menu['TopLeftMenu']) && isset($menu['TopLeftMenu']['submenu']))
foreach($menu['TopLeftMenu']['submenu'] as $voice_name=>$voice)
{
  $voice_id = 'left_menu_voice_'.strtolower(str_replace(' ','_',$voice_name));
  if (!isset($voice['submenu'])) 
  {
?>
                        <li><a id="<?=$voice_id?>" class="list-item" href="<?=$voice['url'] ?>"><?=ucwords($voice_name)?></a></li>
<?php
  }
  else
  {
?>
                        <li id="<?=$voice_id?>">
                          <a class="nav-link dropdown-toggle" href="/#" data-toggle="dropdown" aria-expanded="false">
                            <?=ucwords($voice_name)?>
                            <svg class="icon icon-xs">
                              <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-expand"></use>
                            </svg>
                          </a>
                          
                          <div class="dropdown-menu">
                            <div class="link-list-wrapper">
                              <ul class="link-list" id="<?=$voice_id?>List">
<?php
    if (isset($voice['submenu']))
      foreach($voice['submenu'] as $subvoice_name=>$subvoice)
      {
        $subvoice_id = 'left_submenu_voice_'.strtolower(str_replace(' ','_',$subvoice_name));
        if (!isset($subvoice['submenu'])) 
        {
?>
                                <li><a id="<?=$subvoice_id?>" class="list-item" href="<?=$subvoice['url'] ?>"><span><?=ucwords($subvoice_name)?></span></a></li>
<?php
        }
      }
?>
                              </ul>
                              </div>
                              </div>
                            </div>
                          </div>
                        </li>      
<?php
    }
}
?>
                      </ul>
                    </div>
                  </div>
                </div>
              </nav>
            </div>




            <div class="it-header-slim-right-zone">                
<?php
  if (isset($menu['TopRightMenu']) && $menu['TopRightMenu']['submenu'])
  {
    
    foreach($menu['TopRightMenu']['submenu'] as $voice_name=>$voice)
    {
      $voice_id = 'right_menu_voice_'.strtolower(str_replace(' ','_',$voice_name));
      if (!isset($voice['submenu'])) 
      {
?>
              <a class="nav-link" href="<?=$voice['url'] ?>">
                <span><?=ucwords($voice_name)?></span>
              </a>
<?php
      }
      else
      {
?>
              <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false">
                <?=ucwords($voice_name)?>
                <svg class="icon icon-xs">
                  <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-expand"></use>
                </svg>
              </a>
              
              <div class="dropdown-menu">
                <div class="link-list-wrapper">
                <div class="menu-wrapper">
                  <ul class="link-list" id="<?=$voice_id?>List">
<?php
        if (isset($voice['submenu']))
          foreach($voice['submenu'] as $subvoice_name=>$subvoice)
          {
            $subvoice_id = 'submenu_voice_'.strtolower(str_replace(' ','_',$subvoice_name));
            if (isset($voice['submenu'])) 
            {
?>
                    <li><a id="<?=$subvoice_id?>" class="list-item" href="<?=$subvoice['url'] ?>"><span><?=ucwords($subvoice_name)?></span></a></li>
<?php
            }
          }
?>
                  </ul>      
                </div>      
              </div>      
              </div>      
<?php
      }
    }
  }
?>              
              </div>
              <div class="nav-link px-3 text-white bg-danger" id="username_viewer"></div>
              <div class="it-access-top-wrapper">
                <a class="btn btn-primary btn-sm" href="/login">Accedi</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="it-nav-wrapper">
    <div class="it-header-center-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="it-header-center-content-wrapper">
              <div class="it-brand-wrapper">
                <a href="/#"><?=$CONFIG['WEBSITE_ICON'] ?>
                  <div class="it-brand-text">
                    <h2 class="no_toc"><?=$CONFIG['WEBSITE_TITLE'] ?></h2>
                    <h3 class="no_toc d-none d-md-block"><?=$CONFIG['WEBSITE_TAG_LINE'] ?></h3>
                  </div>
                </a>
              </div>
              <div class="it-right-zone">
                <div class="it-socials d-none d-md-flex">
                  <span><?=_T('Follow us') ?></span>
                  <ul>
<?php
    if (isset($menu['SocialMenu']) && $menu['SocialMenu']['submenu'])
      foreach($menu['SocialMenu']['submenu'] as $voice_name=>$voice)
      {
?>
                    <li>
                      <a aria-label="<?=$voice['name']?>" title="<?=_T('Go To').': '.$voice['name']?>" class="p-2 text-white" href="<?=$voice['url']?>" target="_blank">
                        <svg class="icon icon-sm icon-white align-top">
                          <use
                            xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#<?=$voice['icon']?>"
                          ></use>
                        </svg>
                        </a>
                    </li>
<?php
      }
?>                  
                  </ul>
                </div>
                <div class="it-search-wrapper">
                  <span class="d-none d-md-block"><?=_T('Search') ?></span>
                  <div id="search_on_table" class="m-2"><input class="form-control rounded-icon" type="text" placeholder="<?=_T('Search') ?>" aria-label="<?=_T('Search') ?>" 
                    onkeyup="GetList(localStorage.current_table,  localStorage.list_options + '/all~'+this.value);"></div>
                    <a class="search-link rounded-icon" href="/#" aria-label="<?=_T('Search') ?>" title="<?=_T('Search') ?>">
                    <svg class="icon">
                      <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-search"></use>
                    </svg>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="it-header-navbar-wrapper">
    <div class="container">
      <div class="row">
        <div class="col-12">

          <nav class="navbar navbar-expand-lg">
            <button class="custom-navbar-toggler" type="button" aria-controls="nav10" aria-expanded="false" aria-label="Toggle navigation" data-target="#nav10">
              <svg class="icon">
                <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-burger"></use>
              </svg>
            </button>
            <div class="navbar-collapsable" id="nav10">
              <div class="overlay"></div>
              <div class="close-div sr-only">
                <button class="btn close-menu" type="button"><span class="it-close"></span>close</button>
              </div>
              <div class="menu-wrapper">
                <ul class="navbar-nav" id="mainMenu">
<?php
    if (isset($menu['MainMenu']) && $menu['MainMenu']['submenu'])
      foreach($menu['MainMenu']['submenu'] as $voice_name=>$voice)
      {
        $voice_id = 'mr_menu_voice_'.strtolower(str_replace(' ','_',$voice_name));
        if (!isset($voice['submenu'])) 
        {
?>
                  <li class="nav-item"><a class="nav-link text-nowrap" id="<?=$voice_id?>" href="<?=$voice['url'] ?>"><?=ucwords($voice_name)?></a></li>
<?php
        }
        else
        {
?>
                  <li class="nav-item dropdown" id="<?=$voice_id?>_drop_down">
                      <a class="nav-link dropdown-toggle" href="/#" data-toggle="dropdown" aria-expanded="false">
                        <span><?=ucwords($voice_name)?></span>
                        <svg class="icon icon-xs">
                          <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-expand"></use>
                        </svg>
                      </a>
                      <div class="dropdown-menu">
                        <div class="link-list-wrapper">
                          <ul class="link-list" id="<?=$voice_id?>_list">
<?php
            /* <li><h3 class="no_toc" id="heading">DB Tables</h3></li>
            <li><span class="divider"></span></li> */
          if (isset($voice['submenu']))
            foreach($voice['submenu'] as $subvoice_name=>$subvoice)
            {
              $subvoice_id = 'menu_subvoice_'.strtolower(str_replace(' ','_',$subvoice_name));
              if (!isset($subvoice['submenu'])) 
              {
?>
                            <li><a class="list-item text-nowrap" href="<?=$subvoice['url'] ?>"><span><?=ucwords($subvoice_name)?></span></a></li>
<?php
              }
            }
?>              
                          </ul>
                      </div>
                    </div>
                  </li>                      
<?php
          }
        }
?>
                </ul>
              </div>

            </div>
          </nav>
        </div>
      </div>
    </div>
  </div>

<div class="container">

    <div class="alert alert-primary invisible" role="alert" id="message">
    </div>


<?php 
// echo "<pre>";
// print_r($user->getAllAvaiableModules());
