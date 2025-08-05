<?php

use Nomreh\Core\Logger;

function nomreh_log($message){
    $logger = new Logger();
    $logger->logEvent($message);
}