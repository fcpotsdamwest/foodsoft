<?php
require_once(__DIR__ . '/vendor/autoload.php');
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment(
    $loader,
    // this has to be adjusted for deployment (which value would work?)
    // make it a foodsoft ENV set in config.php
    ['cache' => '/tmp/twig-template-cache',]
);

echo $twig->render('hello.twig', ['pageTitle' => "The world is mine!"]);
