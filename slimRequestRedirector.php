<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require './vendor/autoload.php';
require './requestHandlers.php';
 
$app = new \Slim\App;

$app -> post('/login', 'login');
$app -> post('/register', 'register');
$app -> get('/logout', 'logout');
$app -> post('/newEvent', 'newEvent');
$app -> post('/joinEvent', 'joinEvent');
$app -> post('/event', 'restEvents');
$app -> post('/userEvents', 'userEvents');
$app -> post('/newReview', 'newReview');
$app -> post('/restReviews', 'restReviews');

$app -> run();
?>