<?php

$currentIP = gethostbyname(trim(`hostname`));

define('DOMAIN', 'localhost');

define('MOCK_DATA', false);

define('PROD', false);
define('TEST', false);
define('DEV', true);
