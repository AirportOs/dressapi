<?php
// echo "<pre>";print_r($_SERVER);exit;
?><!doctype html>
<html lang="it">
  <head>
    

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Bootstrap Italia è un tema Bootstrap 4 per la creazione di applicazioni web nel pieno rispetto delle Linee guida di design per i servizi web della PA">
<meta name="author" content="Team per la Trasformazione Digitale">

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
            <a class="d-none d-lg-block navbar-brand" href="/#">Ente appartenenza/Owner</a>
            <div class="nav-mobile">
              <nav>
                <a class="it-opener d-lg-none" data-toggle="collapse" href="#menu-principale" role="button" aria-expanded="false" aria-controls="menu-principale">
                  <span>Ente appartenenza/Owner</span>
                  <svg class="icon">
                    <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-expand"></use>
                  </svg>
                </a>
                <div class="link-list-wrapper collapse" id="menu-principale">
                  <ul class="link-list">
                    <li><a class="list-item" href="/#" onclick="GetList(CONFIG_TABLE);">Config</a></li>
                    <li><a class="list-item" href="/#" onclick="GetList(TRANSLATIONS_TABLE);">Translations</a></li>
                    <li><a class="list-item" href="/#" onclick="GetList(ACL_TABLE);">ACL</a></li>
                    <li id="mainMenuTable">
                        <a class="list-item" href="/#" data-toggle="dropdown" aria-expanded="false">
                          Tables
                          <svg class="icon icon-xs">
                            <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-expand"></use>
                          </svg>
                        </a>
                        <div class="dropdown-menu">
                          <div class="link-list-wrapper">
                            <ul class="link-list" id="mainMenuTableList">
                              <li>
                                <h3 class="no_toc" id="heading">DB Tables</h3>
                              </li>
                              <li><span class="divider"></span></li>
                              <li><a class="list-item" href="/#"><span>Link list 4</span></a></li>
                            </ul>
                          </div>
                        </div>
                      </li>      
                  </ul>
                </div>
              </nav>
            </div>
            <div class="it-header-slim-right-zone">
                <div class="nav-link px-3 text-white bg-danger" id="username_viewer"></div>
                <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="/#" data-toggle="dropdown" aria-expanded="false">
                  <span>ITA</span>
                  <svg class="icon d-none d-lg-block">
                    <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-expand"></use>
                  </svg>
                </a>
                <div class="dropdown-menu">
                  <div class="row">
                    <div class="col-12">
                      <div class="link-list-wrapper">
                        <ul class="link-list">
                          <li><a class="list-item" href="/#"><span>ITA</span></a></li>
                          <li><a class="list-item" href="/#"><span>ENG</span></a></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
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
                <a href="/#">
                  <svg class="icon">
                    <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-code-circle"></use>
                  </svg>
                  <div class="it-brand-text">
                    <h2 class="no_toc">DressApi CMS</h2>
                    <h3 class="no_toc d-none d-md-block">Programmable, modular, CMS</h3>
                  </div>
                </a>
              </div>
              <div class="it-right-zone">
                <div class="it-socials d-none d-md-flex">
                  <span>Seguici su</span>
                  <ul>
                    <li>
                      <a href="/#" aria-label="Facebook" target="_blank">
                        <svg class="icon">
                          <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-facebook"></use>
                        </svg>
                      </a>
                    </li>
                    <li>
                      <a href="/#" aria-label="Github" target="_blank">
                        <svg class="icon">
                          <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-github"></use>
                        </svg>
                      </a>
                    </li>
                    <li>
                      <a href="/#" target="_blank" aria-label="Twitter">
                        <svg class="icon">
                          <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-twitter"></use>
                        </svg>
                      </a>
                    </li>
                  </ul>
                </div>
                <div class="it-search-wrapper">
                  <span class="d-none d-md-block">Cerca</span>
                  <div id="search_on_table" class="m-2"><input class="form-control rounded-icon" type="text" placeholder="Search" aria-label="Search" 
                    onkeyup="GetList(localStorage.current_table,  localStorage.list_options + '/all~'+this.value);"></div>
                    <a class="search-link rounded-icon" href="/#" aria-label="Cerca">
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
    <div class="it-header-navbar-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12">

            <nav class="navbar navbar-expand-lg has-megamenu">
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
$module_names = $user->getAllAvaiableModules();
foreach($menu['MainMenu']['submenu'] as $voice_name=>$voice)
{
  $voice_id = 'menu_voice_'.strtolower(str_replace(' ','_',$voice_name));
  if (!isset($voice['submenu'])) 
  {
?>
<li class="nav-item"><a class="nav-link" id="<?=$voice_id?>" href="<?=$voice['url'] ?>"><?=ucwords($voice_name)?></a></li>
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
              <!-- li><h3 class="no_toc" id="heading">DB Tables</h3></li -->
              <li><span class="divider"></span></li>
<?php
    if (isset($voice['submenu']))
      foreach($voice['submenu'] as $subvoice_name=>$subvoice)
      {
        $subvoice_id = 'menu_subvoice_'.strtolower(str_replace(' ','_',$subvoice_name));
        if (!isset($subvoice['submenu'])) 
        {
      ?>
      <!-- li class="nav-item"><a class="nav-link" id="<?=$subvoice_id?>" href="<?=$subvoice['url'] ?>"><?=ucwords($subvoice_name)?></a></li -->
      <li><a class="list-item" href="<?=$subvoice['url'] ?>"><span><?=ucwords($subvoice_name)?></span></a></li>
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
                    <!-- li class="nav-item dropdown" id="mainMenuTable">
                        <a class="nav-link dropdown-toggle" href="/#" data-toggle="dropdown" aria-expanded="false">
                          <span>Tables</span>
                          <svg class="icon icon-xs">
                            <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-expand"></use>
                          </svg>
                        </a>
                        <div class="dropdown-menu">
                          <div class="link-list-wrapper">
                            <ul class="link-list" id="mainMenuTableList">
                              <li>
                                <h3 class="no_toc" id="heading">DB Tables</h3>
                              </li>
                              <!-- li><span class="divider"></span></li -->
                              <!-- li><a class="list-item" href="/#"><span>Link list 4</span></a></li -->
                            </ul>
                          </div>
                        </div>
                      </li -->                      
                    <!-- li class="nav-item active"><a class="nav-link active" href="/#"><span>link 1 attivo</span><span class="sr-only">current</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="/#"><span>link 2</span></a></li>
                    <li class="nav-item"><a class="nav-link disabled" href="/#"><span>link 3 disabilitato</span></a></li>
  
                    <li class="nav-item dropdown megamenu">
                      <a class="nav-link dropdown-toggle" href="/#" data-toggle="dropdown" aria-expanded="false">
                        <span>Esempio di Megamenu</span>
                        <svg class="icon icon-xs">
                          <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-expand"></use>
                        </svg>
                      </a>
                      <div class="dropdown-menu">
                        <div class="row">
                          <div class="col-12 col-lg-4">
                            <div class="link-list-wrapper">
                              <ul class="link-list">
                                <li>
                                  <h3 class="no_toc">Heading 1</h3>
                                </li>
                                <li><a class="list-item" href="/#"><span>Link list 1 </span></a></li>
                                <li><a class="list-item" href="/#"><span>Link list 2 </span></a></li>
                                <li><a class="list-item" href="/#"><span>Link list 3 </span></a></li>
                              </ul>
                            </div>
                          </div>
                          <div class="col-12 col-lg-4">
                            <div class="link-list-wrapper">
                              <ul class="link-list">
                                <li>
                                  <h3 class="no_toc">Heading 2</h3>
                                </li>
                                <li><a class="list-item" href="/#"><span>Link list 1 </span></a></li>
                                <li><a class="list-item" href="/#"><span>Link list 2 </span></a></li>
                                <li><a class="list-item" href="/#"><span>Link list 3 </span></a></li>
                              </ul>
                            </div>
                          </div>
                          <div class="col-12 col-lg-4">
                            <div class="link-list-wrapper">
                              <ul class="link-list">
                                <li>
                                  <h3 class="no_toc">Heading 3</h3>
                                </li>
                                <li><a class="list-item" href="/#"><span>Link list 1 </span></a></li>
                                <li><a class="list-item" href="/#"><span>Link list 2 </span></a></li>
                                <li><a class="list-item" href="/#"><span>Link list 3</span></a></li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </li -->
                  </ul>
                </div>

              </div>
            </nav>
          </div>
        </div>
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
