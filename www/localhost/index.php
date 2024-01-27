<?php


    $mysql = "mysql83";
    $pdo = new PDO("mysql:host={$mysql};dbname=mysql", 'root', '123123');

    var_dump($pdo);