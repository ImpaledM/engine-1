<?
class Menu {    

  function show () { 
    $client_sec = new Sections();
    XML::from_array('/', $client_sec->ar,'list');  
  }
}