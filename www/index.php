<?php

require_once('code/common.php');

$window = 'menu';     // preliminary settings for login script
$window_id = 'main';
require_once( 'code/login.php' );
if( ! $angemeldet ) {
  div_msg( 'warn', "Bitte erst <a href='/foodsoft/index.php'>Anmelden...</a>" );
  exit();
}

if( get_http_var( 'download','w' ) ) {  // Spezialfall: Datei-Download (.pdf, ...): ohne HTTP-header!
  $window = $download;
  $self_fields['download'] = $window;
  include( "windows/$download.php" );
  exit();
}

get_http_var( 'window', 'w', 'menu', true );     // eigentlich: name des skriptes
get_http_var( 'window_id', 'w', 'main', true );  // ID des browserfensters
setWikiHelpTopic( "foodsoft:$window" );
switch( $window_id ) {
  case 'main':   // anzeige im hauptfenster des browsers
    include('head.php');
    if( hat_dienst(0) ) { // dienst 5 kommt hier sonst nicht vorbei!
      get_http_var( 'dienst_rueckbestaetigen', 'u', 0 );
      if( ! sql_dienst_info( $dienst_rueckbestaetigen ) ) {
        break;
      }
    }
    switch( $window ) {
      case "wiki":
        reload_immediately( "$foodsoftdir/../wiki/doku.php?do=show" );
        break;
      case "bestellen":
        if( hat_dienst(0)
             and sql_dienste( "(dienste.gruppen_id = $login_gruppen_id) and (status = 'Vorgeschlagen')" ) ) {
         //darf nur bestellen, wenn Dienste akzeptiert
         ?> <h2> Vor dem Bestellen bitte Dienstvorschl&auml;ge akzeptieren </h2> <?
         include('windows/dienstplan.php');
         break;
        }
      case 'menu':
        setWikiHelpTopic( "foodsoft" );
      default:
        if( is_readable( "windows/$window.php" ) ) {
          include( "windows/$window.php" );
        } else {
          div_msg( 'warn', "Ung&uuml;ltiger Bereich: $window" );
          include('windows/menu.php');
        }
    }
    open_table( 'footer', "width='100%'" );
      open_td( '', '', "aktueller Server: <kbd>$foodsoftserver</kbd>" );
      open_td( 'right' );
        echo $mysqljetzt;
        if( $readonly ) {
          echo "<span style='font-weight:bold;color:440000;'> --- !!! Datenbank ist schreibgeschuetzt !!!</span>";
        }
    close_table();
    break;
  default:   // anzeige in einem unterfenster
    require_once( 'windows/head.php' );
    if( is_readable( "windows/$window.php" ) ) {
      include( "windows/$window.php" );
    } else {
      div_msg( 'warn', "Ung&uuml;ltiger Bereich: $window" );
    }
    break;
}

// force new iTAN (this form must still be submittable after any other):
//
get_itan( true );
open_form( 'name=update_form', 'action=,message=' );

?>
