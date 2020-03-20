<?php

use Cacher\Cacher;

require "../vendor/autoload.php";
$Ch = new Cacher();

$Ch->add(Cacher::Js, file_get_contents("https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.0/dropzone.js"));
$Ch->setCachDirectorie("cach");
$Ch->setName("Arura");
var_dump($Ch->getMinifyedFiles());
