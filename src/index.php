<?php
/**
 * indeX.php
 *
 * Global entrypoint and base of every subpage of Foodsoft.
 *
 * @param string $download
 *   if set, we render the page specified by $download 'as is', without any HTTP headers
 * @param int|string $readonly
 *   if set to a truthy value, the page is displayed in readonly mode
 * @param string $window
 *   label of the page to render, is mapped to a file in the windows/ subfolder
 * @param string $window_id
 *   ID of the browser window the page should be rendered in (main|...)
 */



// preliminary settings for the login script or very early errors
// the values will usually be replaced with the ones from GET parameters afterwards
$window = 'menu';
$window_id = 'main';

require_once('code/common.php');
// this sets:
// $foodsoftdir
// $mysqljetzt

require_once( 'code/login.php' );
// this sets:
// $angemeldet
// $login_gruppen_id

if( ! $angemeldet ) {
  div_msg( 'warn', "Bitte erst <a href='/foodsoft/index.php'>Anmelden...</a>" );
  exit();
}

if( get_http_var( 'download','W' ) ) {
  /* display special 'page: file download - don't set HTTP-headers! */
  global $download;
  $window = $download;
  $self_fields['download'] = $download;
  include( "windows/$download.php" );
  exit();
}

get_http_var( 'window', 'w', 'menu', true );         // eigentlich: name des skriptes
get_http_var( 'window_id', 'w', 'main', true );   // ID des browserfensters
setWikiHelpTopic( "foodsoft:$window" );

switch( $window_id ) {
  case 'main':   // anzeige im hauptfenster des browsers
    include('head.php');
    switch( $window ) {
      case "wiki":
        reload_immediately( "$foodsoftdir/../wiki/doku.php?do=show" );
        break;
      case 'menu':
      case "bestellen":
        // if( hat_dienst(0) )
          if( dienst_liste( $login_gruppen_id, 'bestätigen lassen' ) )
            break;
      default:
        if( is_readable( "windows/$window.php" ) ) {
          include( "windows/$window.php" );
        } else {
          div_msg( 'warn', "Ungültiger Bereich: $window" );
          include('windows/menu.php');
        }
    }
    open_table( 'footer', "width='100%'" );
      open_td( '', '', "aktueller Server: <kbd>" .gethostname(). "</kbd>" );
      $version = "unknown";
      if (file_exists("version.txt")) {
        $version = file_get_contents("version.txt");
      }
      open_td( '', '', "Version: <kbd>$version</kbd>");
      open_td( 'right' );
        echo $mysqljetzt;
        if( $readonly ) {
          echo "<span style='font-weight:bold;color:#440000;'> --- !!! Datenbank ist schreibgeschützt !!!</span>";
        }
    close_table();
    close_div(); // payload
    open_div('layout', 'id="footbar" style="display: none;"');
    close_div(); // layout: footbar

    $js_on_exit[] = "document.observe('dom:loaded', window.updateWindowHeight );";
    $js_on_exit[] = "Event.observe(window, 'resize', window.updateWindowHeight );";
    $js_on_exit[] = "window.scroller.register(document);";

    break;
  default:   // anzeige in einem unterfenster
    require_once( 'windows/head.php' );
    if( is_readable( "windows/$window.php" ) ) {
      include( "windows/$window.php" );
    } else {
      div_msg( 'warn', "Ungültiger Bereich: $window" );
    }
    break;
}

// force new iTAN (this form must still be submittable after any other):
//
get_itan( true );
open_form( 'name=update_form', 'action=nop,message=' );
