<?php

  if ( isset ( $_POST['url'] ) && $_POST['url'] != '' ) {

    // Website url to open
    $daurl = $_POST['url'];

    // Get that website's content
    $handle = fopen($daurl, "r");

    // If there is something, read and return
    if ($handle) {
        while (!feof($handle)) {
            $buffer = fgets($handle, 4096);
            echo $buffer;
        }
        fclose($handle);
    }
    
  } else {
    
    echo 'Error! URL is not set.';
    
  }

?>