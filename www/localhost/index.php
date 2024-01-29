<?php

$redis = new Redis();
$redis->connect('redis', 6379);
$auth = $redis->auth('123123'); 
var_dump($auth);