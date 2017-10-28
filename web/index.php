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
        require("../classes/itemMaster.php");
        require("../classes/discountMaster.php");
        require("../classes/bookingMaster.php");
        require("../classes/bookingItemMaster.php");
        $bookingItem=new bookingItemMaster;
        $mobile="";
        if($request->get("user_mobile"))
        {
            $mobile=$request->get("user_mobile");
        }
        $bookingResponse=$bookingItem->makeBooking($request->get("user_email"),$mobile,$request->get("disc"));
        if(strpos($bookingResponse,'BOOKING_MADE_')!==false)
        {
            $e=explode("BOOKING_MADE_",$bookingResponse);
            $bookingID=trim($e[1]);
            $app['session']->set('booking_id',$bookingID);
            $range=$request->get("range");
            $itemObj=new itemMaster;
            $items=$itemObj->getItems();
            for($i=0;$i<count($range);$i++)
            {
                $rangeValue=$range[$i];
                $item=$items[$i];
                $itemID=$item['iditem_master'];
                $bookingItemResponse=$bookingItem->storeBookingItem($bookingID,$itemID,$rangeValue);
            }
            return $app->redirect('/booking');
        }
        else
        {
            return $app->redirect('/?err='.$bookingResponse);
        }
    }
    else
    {
        return $app->redirect('/');
    }
});
$app->get("/booking",function() use($app){
    return $app['twig']->render("booking.html.twig");
});
$app->get("/getBookingDetails",function() use ($app){
    if($app['session']->get("booking_id"))
    {
        $bookingID=addslashes(htmlentities($app['session']->get("booking_id")));
        require("../classes/itemMaster.php");
        require("../classes/discountMaster.php");
        require("../classes/bookingMaster.php");
        require("../classes/bookingItemMaster.php");
        $bookingObj=new bookingMaster($bookingID);
        $booking=$bookingObj->getBooking();
        if(is_array($booking))
        {
            $bookingItemObj=new bookingItemMaster;
            $bookingItems=$bookingItemObj->getBookingItems($bookingID);
            if(is_array($bookingItems))
            {
                array_push($booking,$bookingItems);
            }
            return json_encode($booking);
        }
        else
        {
            return $booking;
        }        
    }
    else
    {
        return "INVALID_BOOKING_ID";
    }
});
$app->run();
