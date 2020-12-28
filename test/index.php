<?php

use Cacher\Cacher;

require "../vendor/autoload.php";
$Ch = new Cacher();
$Ch ->callback(function (Cacher $ch){
    $ch->add(Cacher::Js, file_get_contents("https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.0/dropzone.js"));
})
    ->setCachDirectory("cach")
    ->setName("Arura");

$Ch->Minify();


var_dump($Ch->getMinifyedFiles());
