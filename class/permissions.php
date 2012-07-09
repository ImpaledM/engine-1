<?php
class Permissions {
  private $db;

  function __construct( $obj ){
    if ( UPDATE_PERMISSION_SESSION == 1 || ! isset( $_SESSION['permission'] ) ){
      $_SESSION['permission']['view']=array();
      $_SESSION['permission']['add']=array();

      $this->db=new Db( );
      $rs=$this->db->query( 'SELECT * FROM `permissions` WHERE ("' .intval(@$_SESSION['user']['role']) . '" & `role`)=`role`' );

      while ( $row=$this->db->fetch( $rs ) ){
        $ar=(array)$obj->get_childrens( $row['id_section'] );
        if ( $row['view'] == 1 ) $_SESSION['permission']['view']=array_merge( $_SESSION['permission']['view'], $ar );
        if ( $row['add'] == 1 ) $_SESSION['permission']['add']=array_merge( $_SESSION['permission']['add'], $ar );
      }
      if ( isset( $_SESSION['permission']['view'] ) ) $_SESSION['permission']['view']=array_unique( $_SESSION['permission']['view'] );
      if ( isset( $_SESSION['permission']['add'] ) ) $_SESSION['permission']['add']=array_unique( $_SESSION['permission']['add'] );
    }
  }
}