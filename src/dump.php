<?php
// dump.php --- dump database structure
//
// This script must _not_ be accessible over the net during normal
// operation - it is for developers only, to dump the database
// structure in easily PHP-readable format.
//

header("Content-Type: text/plain");

exit(1);  // keep disabled when not needed

require_once('code/config.php');

$db_handle = mysqli_connect($db_server,$db_user,$db_pwd);
$db_selected = mysqli_select_db( $db_handle, $db_name );

$tables = array();
$result = mysqli_query( $db_handle, "SHOW TABLES; " );

while( $row = mysqli_fetch_array( $result ) ) {
  // var_export( $row );
  $tables[] = $row[0];
}

echo '<?

$tables = array(
';

$tkomma = ' ';
foreach( $tables as $table ) {
  echo "$tkomma '$table' => array(\n";
  $tkomma = ',';

  $result = mysqli_query( $db_handle, "SHOW COLUMNS FROM $table; " );
  echo "    'cols' => array(\n";
  $ckomma = ' ';
  while( $row = mysqli_fetch_array( $result ) ) {
    echo "    $ckomma '{$row['Field']}' => array(\n";
    echo "        'type' =>  \"{$row['Type']}\"\n";
    echo "      , 'null' => '{$row['Null']}'\n";
    echo "      , 'default' => '{$row['Default']}'\n";
    echo "      , 'extra' => '{$row['Extra']}'\n";
    echo "      )\n";
    $ckomma = ',';
  }
  echo "    )\n";
  echo "    , 'indices' => array(\n";
  $result = mysqli_query( $db_handle, "SHOW INDEX FROM $table; " );
  $ikomma = ' ';
  $i = 1;
  $iname = '';
  $icols = '';
  while( $row = mysqli_fetch_array( $result ) ) {
    // var_export( $row );
    if( $iname == $row['Key_name'] ) {
      $icols .= ", {$row['Column_name']}";
    } else {
      if( $iname ) {
        echo "      $ikomma '$iname' => array( 'unique' => $iunique, 'collist' => '$icols' )\n";
        $ikomma = ',';
      }
      $iname = $row['Key_name'];
      $icols = $row['Column_name'];
      $iunique = ( $row['Non_unique'] == '0' ? 1 : 0 );
    }
  }
  if( $iname ) {
    echo "      $ikomma '$iname' => array( 'unique' => $iunique, 'collist' => '$icols' )\n";
  }

  echo "    )\n";
  echo "  )\n";
}

echo ");\n";

echo '?' . '>';

?>
