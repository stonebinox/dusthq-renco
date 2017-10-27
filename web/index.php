<?php
ini_set('display_errors', 1);
require_once __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/prod.php';
require __DIR__.'/../src/controllers.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stderr',
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
      'driver' => 'pdo_mysql',
      'dbname' => 'heroku_39d7e99ed979163',
      'user' => 'bc12f859a39e69',
      'password' => 'cd584872',
      'host'=> "us-cdbr-iron-east-05.cleardb.net",
    )
));
$app->register(new Silex\Provider\SessionServiceProvider, array(
    'session.storage.save_path' => dirname(__DIR__) . '/tmp/sessions'
));
$app->before(function(Request $request) use($app){
    $request->getSession()->start();
});
$app->get("/",function() use($app){
    $app['twig']->render("index.html.twig");
});
$app->get('/getItems', function() use($app){
    require("../classes/itemMaster.php");
    $item=new itemMaster;
    $items=$item->getItems();
    if(is_array($items))
    {
        return json_encode($items);
    }
    else
    {
        return $items;
    }
});
$app->post("/book",function(Request $request) use($app){
    if(($request->get("range"))&&($request->get("user_email"))&&($request->get("disc")))
    {
        require("../itemMaster.php");
        require("../discountMaster.php");
        require("../bookingMaster.php");
        require("../bookingItemMaster.php");
        $bookingItem=new bookingItemMaster;
        $mobile="";
        if($request->get("user_mobile"))
        {
            $mobile=$request->get("user_mobile");
        }
        $bookingResponse=$bookingItem->makeBooking($request->get("user_email"),$mobile,$request->get("disc"));
        if(strpos($bookingResponse,'BOOKING_MADE_')!==false)
        {
            $e=explode("BOOKING_MADE_");
            $bookingID=trim($e[1]);
            $range=$request->get("range");
            return json_encode($range);
        }
        else
        {
            return $bookingResponse;
        }
    }
    else
    {
        return $app->redirect('/');
    }
});
$app->run();
