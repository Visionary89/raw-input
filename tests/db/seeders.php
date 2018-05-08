<?php
require_once __DIR__ . '/../../bootstrap/autoload.php';

//это можно получать при запуске из командной строки

$count = 10000;

$faker = \Faker\Factory::create();

$pdo = new \PDO(
    $_ENV['db_host'] ?: 'mysql:dbname=raw_input;unix_socket=/tmp/mysql.sock',
    $_ENV['db_username'] ?: 'raw_input',
    $_ENV['db_password'] ?: 'password',
    $_ENV['db_options'] ?: null
);

$service = new \RawInput\Services\OrderService($pdo);


for ($i = 0; $i < $count; $i++) {
    $order = [
        'numbers' => $faker->numerify('##########'),
        'text'    => $faker->paragraph(4, true),
    ];
    $phoneCollection = new \RawInput\Types\PhoneNumberCollection($order['numbers']);
    $text = new \RawInput\Types\Text($order['numbers']);
    $service->createOrder($phoneCollection, $text);
}