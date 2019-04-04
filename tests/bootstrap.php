<?php

define('ROOT', dirname(__DIR__));
ini_set('session.save_path',realpath(ROOT . '/session'));
require ROOT . '/app/App.php';
App::load();

