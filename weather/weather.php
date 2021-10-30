<?php

    $city = "Новосибирск";
    $mode = "json";
    $units = "metric";
    $lang = "ru";
    $countDay = 1;
    $appID = "241fb0dba95bba393d5ffbb8521fabcf";

    $url = "http://api.openweathermap.org/data/2.5/forecast?q=Novosibirsk,Russia&cnt=1&lang=ru&units=metric&appid=241fb0dba95bba393d5ffbb8521fabcf";

    $data = file_get_contents($url);

    $jsonString = json_encode($data);
    file_put_contents("json.txt", $jsonString);
    $dataJson = json_decode( file_get_contents("json.txt") );
    $arrayDays = $dataJson->list;

    echo "$dataJson";
