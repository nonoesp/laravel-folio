@extends('folio::admin.layout')

@section('title', 'Template stats')

@section('content')

<?php

$template_groups = Folio::templates();
    foreach($template_groups as $templates) {

      if(is_array ($templates)) {
        foreach($templates as $key=>$template) {
          $name = str_replace(" Template", "", $template);
          $count = Item::withTrashed()->where('template','=',$key)->count();
          if(!$count) {
            $count = "";
          } else {
            $count = "— ".$count;
          }
          echo $name.' '.$count.'<br>';
        }
      } else {
        $count = Item::withTrashed()->where('template','=',null)->count();
        echo $templates." — ".$count;
        echo '<br>';
      }
      echo '<br>';

    }

    echo '<strong>Items</strong><br>';
    $items = Item::withTrashed()->get();
    foreach($items as $item) {
      echo '<a href="/admin/item/edit/'.$item->id.'">'.$item->title.'</a>';
      if($item->template) echo ' — <b>'. $item->template .'</b>';
      echo '<br>';
    }

?>
@stop