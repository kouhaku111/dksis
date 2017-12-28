<?php
$conn_string = "host=localhost port=5432 dbname=dksis user=postgres password=postgres";
define("DB", pg_connect($conn_string));
if(!DB) {
  echo("Could not connect");
  exit;
}
?>
