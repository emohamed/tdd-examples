<?php

$baseTime = time();

$time1 = $baseTime;
$time1 += 86400;
var_dump(date('Y-m-d', $time1));

$time2 = $baseTime;
$time2 += 86400;
var_dump(date('Y-m-d', $time2));
