<?
class Message {

  static function error( $str, $area='content' ){
    if (!isset($_SESSION['messages'][$area]['error']) || !in_array($str,$_SESSION['messages'][$area]['error'])) {
      $_SESSION['messages'][$area]['error'][]=$str;
    }
  }

  static function success( $str, $area='content' ){
    if (!isset($_SESSION['messages'][$area]['success']) || !in_array($str,$_SESSION['messages'][$area]['success'])) {
      $_SESSION['messages'][$area]['success'][]=$str;
    }
  }

  static function notice( $str, $area='content' ){
    if (!isset($_SESSION['messages'][$area]['notice']) || !in_array($str,$_SESSION['messages'][$area]['notice'])) {
      $_SESSION['messages'][$area]['notice'][]=$str;
    }
  }

  static function get(){
    if ( isset( $_SESSION['messages'] ) ){
      $messages=array(

      'messages' => $_SESSION['messages']
      );
      unset( $_SESSION['messages'] );
    }else{
      $messages=array();
    }
    return $messages;
  }

  static function db( $db ){
    if ( $db->error ){
      Message::success( 'Информация успешно сохранена в БД' );
    }else{
      Message::error( 'ОШИБКА при обращении к БД, Информацию НЕ УДАЛОСЬ сохранить.' );
    }
  }

  static function errorState( $area=null ){
    $result = false;
    if (isset($_SESSION['messages'])) {
      if (!is_null($area)) {
        if (isset($_SESSION['messages'][$area]['error'])) $result=true;
      } else {
        foreach ($_SESSION['messages'] as $key=>$value) {
          if (key_exists('error', $value))
          $result=true;
        }
      }
    }
    return $result;
  }
}
?>