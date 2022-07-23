</div>

<footer class="it-footer">
  <div class="it-footer-main">
    <div class="container">
      <section>
        <div class="row clearfix">
          <div class="col-sm-12">
            <div class="it-brand-wrapper">
              <a href="/"><?=$CONFIG['WEBSITE_ICON'] ?>
                <div class="it-brand-text">
                  <h2 class="no_toc"><?=$CONFIG['WEBSITE_TITLE'] ?></h2>
                  <h3 class="no_toc d-none d-md-block"><?=$CONFIG['WEBSITE_TAG_LINE'] ?></h3>
                </div>
              </a>
            </div>
          </div>
        </div>
      </section>
      <section>
        <div class="row">

<?php
    if (isset($menu['FooterMenu']) && $menu['FooterMenu']['submenu'])
      foreach($menu['FooterMenu']['submenu'] as $voice_name=>$voice)
      {
        $voice_id = 'mb_menu_voice_'.strtolower(str_replace(' ','_',$voice_name));
        if (isset($voice['submenu'])) 
        {
?>
          <div class="col-lg-3 col-md-3 col-sm-6 pb-2">
            <h4>
              <a href="/#" title="Vai alla pagina: Amministrazione"><?=$voice_name?></a>
            </h4>
            <div class="link-list-wrapper">
              <ul class="footer-list link-list clearfix">
<?php
          if (isset($voice['submenu']))
            foreach($voice['submenu'] as $subvoice_name=>$subvoice)
            {
              $subvoice_id = 'menu_subvoice_'.strtolower(str_replace(' ','_',$subvoice_name));
              if (!isset($subvoice['submenu'])) 
              {
?>
                <li><a class="list-item text-nowrap" title="<?=_T('Go To').': '.$subvoice_name?>" href="<?=$subvoice['url'] ?>"><?=$subvoice_name?></a></li>
<?php
              }
            }
?>
              </ul>
            </div>
          </div>
<?php
          }
        }
?>
        </div>
      </section>
      <section class="py-4 border-white border-top">
        <div class="row">

<?php
    if (isset($menu['TextMenu']) && $menu['TextMenu']['submenu'])
      foreach($menu['TextMenu']['submenu'] as $voice_name=>$voice)
      {
        $voice_id = 'mb_menu_voice_'.strtolower(str_replace(' ','_',$voice_name));
        if (isset($voice['submenu'])) 
        {
?>
          <div class="col-lg-4 col-md-4 col-sm-6 pb-2">
            <h4><a href="/#" title="<?=_T('Go To').': '.$voice_name?>"><?=$voice_name?></a></h4>
<?php
          if (isset($voice['submenu']))
          {
?>
            <div class="link-list-wrapper">
            <ul class="footer-list link-list clearfix">
<?php
            foreach($voice['submenu'] as $subvoice_name=>$subvoice)
            {
?>
              <li>
<?php

              $subvoice_id = 'menu_subvoice_'.strtolower(str_replace(' ','_',$subvoice_name));
              if (!str_starts_with($subvoice['name'],'#text') && $subvoice['url']=='')
              {
?>
                  <strong><?=$subvoice_name?></strong><br>
<?php
              }
?>
                  <?php if (str_starts_with($subvoice['query'],'#text:')) 
                          print substr($subvoice['query'],6);
                        elseif ($subvoice['url']!='') 
                          print '<a title="'._T('Go To').': '.$subvoice['name'].'" href="'.$subvoice['url'].'">'.$subvoice['name'].'</a>'; ?>
              </li>
<?php
            }
?>
            </ul>
            </div>
<?php
          }
?>
          </div>
<?php
        }
      }
?>
<div class="col-lg-4 col-md-4 pb-2">
            <h4>
              <a href="#" title="Vai alla pagina: Seguici su">Seguici su</a>
            </h4>
            <ul class="list-inline text-start social">
              <li class="list-inline-item">
                <a title="Designers Italia" class="p-2 text-white" href="#" target="_blank">
                  <svg class="icon icon-sm icon-white align-top">
                    <use
                      xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-designers-italia"
                    ></use>
                  </svg>
                  </a>
              </li>
              <li class="list-inline-item">
                <a title="Twitter" class="p-2 text-white" href="#" target="_blank">
                  <svg class="icon icon-sm icon-white align-top">
                    <use
                      xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-twitter"
                    ></use>
                  </svg>
                </a>
              </li>
              <li class="list-inline-item">
                <a title="Medium" class="p-2 text-white" href="#" target="_blank">
                  <svg class="icon icon-sm icon-white align-top">
                    <use
                      xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-medium"
                    ></use>
                  </svg>
                </a>
              </li>
              <li class="list-inline-item">
                <a title="Behance" class="p-2 text-white" href="#" target="_blank">
                  <svg class="icon icon-sm icon-white align-top">
                    <use
                      xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-behance"
                    ></use>
                  </svg>
                  </a>
              </li>
            </ul>
          </div>
        
        </div>
        </div>
      </section>
    </div>
  </div>
  <div class="it-footer-small-prints clearfix">
    <div class="container">
      <h3 class="sr-only">Sezione Link Utili</h3>
      <ul class="it-footer-small-prints-list list-inline mb-0 d-flex flex-column flex-md-row">
        <li class="list-inline-item"><a href="/#" title="Note Legali">Media policy</a></li>
        <li class="list-inline-item"><a href="/#" title="Note Legali">Note legali</a></li>
        <li class="list-inline-item"><a href="/#" title="Privacy-Cookies">Privacy policy</a></li>
        <li class="list-inline-item"><a href="/#" title="Mappa del sito">Mappa del sito</a> </li>
      </ul>
    </div>
  </div>
</footer>

    

<script>window.__PUBLIC_PATH__ = "/frameworks/bootstrap-italia/dist/fonts"</script>

<script src="/frameworks/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js"></script>

<!-- script src="/assets/js/dressapi.js"></script -->

<!-- Notification per tornare alla pagina principale -->
<div class="notification dismissable with-icon" role="alert" id="liveToast1" aria-labelledby="liveToast1-title">
  <h5 id="liveToast1-title">
    <svg class="icon">
      <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-info-circle"></use></svg>Esempio di utilizzo
  </h5>
  <p>
    <a href="/frameworks/bootstrap-italia/docs/esempi/">Torna alla pagina principale degli esempi</a>
  </p>
  <button type="button" class="btn notification-close">
    <svg class="icon">
      <use xlink:href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-close"></use>
    </svg>
    <span class="sr-only">Chiudi notifica: Titolo notification</span>
  </button>
</div>


<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast2" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <strong class="me-auto">DressApi</strong>
        <small>Just now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body text-white">
        Welcome to DressApi!
      </div>
    </div>
  </div>    

<script>
 // $(document).ready(function() {
 //   notificationShow('notification-esempi')
 // })
</script>

<script>
    (function () {
        if (!localStorage.token)
            document.location('/signin/');

        // document.getElementById('token').innerHTML = localStorage.token;
        document.getElementById('username_viewer').innerHTML = localStorage.username;


        // createMenuTables();

        // console.log(localStorage.token);

        // GetList('comment');
        // GetTableStructure('comment');
        // GetAllTableStructures();

        // popolate a page components
        // setPage();
    })();
</script>
    <!-- [[INLINE-END-JS]] -->

  </body>
</html>
