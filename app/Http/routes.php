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

use App\Http\Controllers\AuthenticationController;
use App\User;

$app->group(['prefix' => 'api'], function () use ($app) {
    $app->post('authenticate', [
        'uses' => AuthenticationController::class . '@authenticate',
        'as' => 'sign_in'
    ]);
    $app->post('customAuthenticate',[
        'uses' => AuthenticationController::class. '@customAuthenticate',
        'as' => 'customAuthenticate'
    ]);
});

$app->group(['prefix' => 'api','middleware' =>'jwt.auth'],function() use($app){
   $app->get('/user',function() use ($app){
       return response()->json(['user' => User::first()]);
   }) ;
});



$app->group(['prefix' => 'api', 'middleware' => ['before' => 'jwt-auth']], function () use ($app) {
    $app->get('/todo', function () use ($app) {
        $user = $app['tymon.jwt.auth']->toUser();
        return ['todos' => [
            'items' => ['Code awesome stuff', 'Feed the cat'],
            'owner' => $user->id,
            'name' => $user->name,
        ]];
    });
});

$app->get('/', function () {
    $url = route('sign_in');

    return <<<HTML
<form method="post" action="$url">
    <input type="email" name="email">
    <input type="text" name="password">
    <input type="submit" value="Submit">
</form>
HTML;

});