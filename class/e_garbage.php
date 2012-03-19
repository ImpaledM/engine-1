<?php
class e_Garbage {
  function clearTempFile($delta = 1, $exclude=array()) {
    $dir = ROOT . 'temp/';
    if (@ $handle = opendir ( $dir )) {
      while ( false !== ($file_c = readdir ( $handle )) ) {
        if ($file_c != '.' && $file_c != '..' && ((time () - ($delta * 3600 * 24) - filemtime ( $dir . $file_c )) / 3600 / 24) > 0 && !in_array($file_c, $exclude)) {
          Utils::delete ( $dir . $file_c );
        }
      }
      closedir ( $handle );
    }
  }
}