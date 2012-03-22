<?php

Class Div_into_pages {
  private
  $item_on_page,
  $visible_pages,
  $current_page,
  $keyname='page';


  function __construct($item_on_page, $visible_pages, $current_page) {
    $this->item_on_page=$item_on_page;
    $this->visible_pages=$visible_pages;
    $this->current_page=intval($current_page);
    if ($this->current_page==0) {
      $this->current_page=1;
    }
  }


  function set_keyname($keyname) {
    $this->keyname=$keyname;
  }

  function get_limit() {
    // вычисление диапазона элементов для текущей страницы
    $start=($this->current_page-1)*$this->item_on_page;
    return array($start,$this->item_on_page);

  }

  function get_pages($count_item) {

    // вычисление количества страниц
    $count_pages=intval($count_item/$this->item_on_page);

    if ($count_item%$this->item_on_page!=0) {
      $count_pages++;
    }


    if ($this->current_page>$count_pages) {
      $this->current_page=$count_pages;
    }

    ($this->current_page==$count_pages) ? $next=NULL : $next=$this->current_page+1;

    if ($this->current_page<1) {
      $this->current_page=1;
    }
    ($this->current_page==1) ? $prev=NULL : $prev=$this->current_page-1;

    // вычисление диапазона видимых страниц, относительно текущей
    $half=intval($this->visible_pages/2);
    if ($this->visible_pages%2==0) {
      $lhalf=$half;
      $rhalf=$half-1;
    } else {
      $lhalf=$rhalf=$half;
    }

    $start=$this->current_page-$lhalf;
    $end=$this->current_page+$rhalf;


    // корректировка диапазона

    $ldiff=$start-1;
    if ($ldiff<0) {
      $start=1;
      $end+=abs($ldiff);
    }

    $rdiff=$end-$count_pages;
    if ($rdiff>0) {
      $end=$count_pages;
      if ($ldiff>=$rdiff) {
        $start-=$rdiff;
      }
    }

    $pages=range($start, $end);
    ($end==$count_pages) ? $block_next=NULL : $block_next=$end+1;
    ($start==1) ? $block_prev=NULL : $block_prev=$start-1;

    $current_page=$this->current_page;

    return array($this->keyname, $pages, compact('block_prev', 'prev', 'current_page', 'next', 'block_next', 'count_pages'), array('count_item'=>$count_item));
  }
}
