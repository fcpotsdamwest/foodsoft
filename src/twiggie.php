<?php
require_once(__DIR__ . '/vendor/autoload.php');
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment(
    $loader,
    ['cache' => __DIR__ . 'templates/cache',]
);

echo $twig->render('hello.twig', ['pageTitle' => "The world is mine!"]);
echo "OK!";
