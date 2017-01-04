<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// line bot
$container['bot'] = function ($c) {
    $settings = $c->get('settings');
    $channelSecret = $settings['bot']['channelSecret'];
    $channelToken = $settings['bot']['channelToken'];
    $apiEndpointBase = $settings['apiEndpointBase'];
    $bot = new \LINE\LINEBot(new \LINE\LINEBot\HTTPClient\CurlHTTPClient($channelToken), [
        'channelSecret' => $channelSecret,
        'endpointBase' => $apiEndpointBase, // <= Normally, you can omit this
    ]);
    return $bot;
};