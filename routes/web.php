<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/** @var Laravel\Lumen\Routing\Router $router */
$router->group(['prefix' => 'api'], function () use ($router) {

    $router->group(['prefix' => 'v1'], function () use ($router) {

        $router->get('/', function () use ($router) {
            return 'Synevo Booking API v1: ' . $router->app->version();
        });

        $router->group(['prefix' => 'spermogram'], function () use ($router) {

            $router->get('get-free-intervals', 'ReservationController@getFreeIntervals');
            $router->post('store-reservation', 'ReservationController@storeInterval');

            /**
             * Routes for ..., used in spermogram booking form.
             */
            $router->get('get-reservations', 'ReservationController@getExistingReservations');

            /**
             * Routes for ..., used to generate reports for nurses.
             */
            $router->get('get-grouped-reservations', 'ReservationController@getExistingReservationsGrouped');

            /**
             * Routes Synevo bot, used when user is filling out information for appointment (reservation).
             */
            $router->get('centers', 'CenterController@getSpermogramCenters');

        });

        $router->group(['prefix' => 'card'], function () use ($router) {

            $router->get('check-card', 'CardController@checkCard');

        });

    });

});



