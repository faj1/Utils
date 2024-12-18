<?php
require 'vendor/autoload.php';
$RevoltUnixServer = new \Faj1\Utils\Socket\ReactUnixServer();
$RevoltUnixServer->start();
