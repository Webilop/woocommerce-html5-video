<?php

spl_autoload_register( function($classname) {
  // class name can have sub namespace
  $path = explode( '\\', $classname );
  switch (count($path)) {
    case 2:
      $namespace = null;
      $classname = $path[1];
      break;

    case 3:
      $namespace = $path[1];
      $classname = $path[2];
      break;

    default:
      return false;
  }

  // class files in classes folder into plugin
  $plugin_dir = plugin_dir_path(__FILE__);
  $fileName = $plugin_dir . DIRECTORY_SEPARATOR;
  if ( !is_null($namespace) ) {
    $fileName .= $namespace . DIRECTORY_SEPARATOR;
  }
  $fileName .=  $classname . '.php';

  if ( file_exists( $fileName ) ) {
      require $fileName;
      return true;
  }
  return false;
});
