<?php

require_once __DIR__ . '/vendor/autoload.php';

$application = new \Symfony\Component\Console\Application();

$application
    ->add(new \App\Commands\ParseChannel(
        \App\Services\ServiceContainer::getInstance()->getTelegramApi())
    )
;

try {
    $application->run();
} catch (Exception $e) {
    echo 'Error ' . $e->getCode() . ': ' . $e->getMessage();
}
