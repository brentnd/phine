<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Phine the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$app['router']->get('/', function() {
    return 'Home page';
});