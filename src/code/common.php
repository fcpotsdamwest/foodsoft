<?php
/**
 * - check configuration (and fail on problems)
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $foodsoftdir;   // nötig wenn aufruf aus wiki

if( ! isset( $foodsoftdir ) ) {
  $foodsoftdir = preg_replace( '#/[^/]+$#', '', $_SERVER['SCRIPT_NAME'] );
  // ausnahme: aufruf aus dem wiki heraus:
  $foodsoftdir = preg_replace( '#/wiki$#', '/foodsoft', $foodsoftdir );
}

require_once('code/config.php');
// this sets:
// $allow_setup_from
// $db_name
// $db_pwd
// $db_server
// $db_user
// $foodsoftdir

if( $allow_setup_from ) {
  ?>
  <html>
    <body>
      Fehler: bitte <code>setup.php</code> deaktivieren in <code>code/config.php</code>!
    </body>
  </html>
  <?php
  exit(1);
}

/* import low-level modules not requiring a DB connection */
require_once('code/err_functions.php');
require_once('code/html.php');

/* establish DB connection */
global $db_handle;

if(
  ! ( $db_handle = mysqli_connect($db_server,$db_user,$db_pwd ) ) ||
  ! mysqli_select_db( $db_handle, $db_name )
) {
  echo "<html><body><h1>Datenbankfehler!</h1>Konnte keine Verbindung zur Datenbank herstellen... Bitte später nochmal versuchen.</body></html>";
  exit();
}

// die restliche konfiguration können wir aus der leitvariablen-tabelle lesen
// (skripte können dann persistente variable einfach speichern, ändern, und
//  an slave (im keller) übertragen)
//
global $leitvariable;

require_once('leitvariable.php');

foreach( $leitvariable as $name => $props ) {
  global $$name;
  $result = mysqli_query( $db_handle, "SELECT * FROM leitvariable WHERE name='$name'" );
  if( $result && ( $row = mysqli_fetch_array( $result ) ) ) {
    $$name = $row['value'];
  } else {
    $$name = $props['default'];
  }
}

// the above code is setting:
// $basar_id
// $bulletin_board
// $database_version
// $motd (message of the day)
// $muell_id
// ...and more, see leitvariable.php

global
  $mysqlheute,
  $mysqljetzt;

// $mysqljetzt: Alternative zu NOW(), Vorteile:
//  - kann quotiert werden
//  - in einem Skriptlauf wird garantiert immer dieselbe Zeit verwendet
$now = explode( ',' , date( 'Y,m,d,H,i,s' ) );
$mysqlheute = $now[0] . '-' . $now[1] . '-' . $now[2];  // 2020-11-15
$mysqljetzt = $mysqlheute . ' ' . $now[3] . ':' . $now[4] . ':' . $now[5];  // 2020-11-15 12:04:15

// gruppen mit sonderbedeutung merken:
global
  $specialgroups;

$specialgroups = array();
$basar_id or error( "Spezielle Basar-Gruppe nicht gesetzt (in tabelle leitvariablen!)" ); 
$muell_id or error( "Spezielle Müll-Gruppe nicht gesetzt (in tabelle leitvariablen!)" );
$specialgroups[] = $basar_id;
$specialgroups[] = $muell_id;

// $self_(post_)fields: for storing GET/POST parameters */
global
  $self_fields,
  $self_post_fields;

$self_fields = array();
$self_post_fields = array();

// Benutzerdaten:
global
  $angemeldet,
  $coopie_name,
  $dienstkontrollblatt_id,
  $login_dienst,
  $login_gruppen_id,
  $login_gruppen_name,
  $session_id;

$angemeldet = false;

require_once('structure.php');
// this sets: $tables

require_once('code/views.php');
// this sets:
// PR_COL_* constants
// PR_ROW_* constants

require_once('code/inlinks.php');
// this imports just functions

require_once('code/zuordnen.php');
// this sets:
// $from_dokuwiki
// $itan
// $masseinheiten
// $wochentage
// BESTELLZUORDNUNG_ART_* constants
// GRUPPEN_OPT_* constants
// PFAND_OPT_* constants
// STATUS_* constants (indicating order status)

require_once('code/forms.php');
// this imports just functions

require_once('code/katalogsuche.php');
// this imports just functions

update_database($database_version);

?>
