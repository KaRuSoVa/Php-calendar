<?php

  try {
    $auth = new PDO('mysql:host=localhost;dbname=offer;charset=utf8', 'root', '');
  }
  catch(Exception $error) {
    die('Error : ' . $error->getMessage());
  }

?>