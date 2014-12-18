<?php

if (class_exists('\\Subscribo\\ApiChecker\\ApiCheckerServiceProvider')) {
    App::register('\\Subscribo\\ApiChecker\\ApiCheckerServiceProvider');
}

if (class_exists('\\Subscribo\\Api0\\Api0ServiceProvider')) {
    App::register('\\Subscribo\\Api0\\Api0ServiceProvider');
}

//