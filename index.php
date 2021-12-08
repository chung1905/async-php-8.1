<?php
require_once __DIR__ . "/src/HttpClient.php";

$urls = [
    "http://httpbin.org/delay/8",
    "http://httpbin.org/delay/7",
    "http://httpbin.org/delay/5",
    "http://httpbin.org/delay/2",
    "http://httpbin.org/delay/4",
    "http://httpbin.org/delay/6",
    "http://httpbin.org/delay/1",
];

$client = new HttpClient();
$startTime = microtime(true);
foreach ($urls as $url) {
    (new Fiber(function () use ($client, $url) {
        print(sprintf("Start %s\n", $url));
        $response = Async::async($client->fetch($url));
        print(sprintf("Done %s\n", json_decode($response)->url));
    }))->start();
}

Async::wait();
printf("Done in %fs\n", microtime(true) - $startTime);
