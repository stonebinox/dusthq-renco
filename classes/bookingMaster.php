<?php
/*-------------------------------------------
Author: Anoop Santhanam
Date Created: 27/10/17 17:55
Last Modified: 27/10/17 17:55
Comments: Class file for booking_master
table.
-------------------------------------------*/
class bookingMaster extends discountMaster
{
    public $app=NULL;
    public $bookingValid=false;
    private $booking_id=NULL;
    function __construct($bookingID=NULL) 
    {
        $this->app=$GLOBALS['app'];
        if($bookingID!=NULL)
        {
            $this->booking_id=addslashes(htmlentities($bookingID));
            $this->bookingValid=$this->verifyBooking();
        }
    }
    function verifyBooking()
    {
        if($this->booking_id!=NULL)
        {
            $app=$this->app;
            $bookingID=$this->booking_id;
            $bm="SELECT discount_master_iddiscount_master FROM booking_master WHERE stat='1' AND idbooking_master='$bookingID'";
            $bm=$app['db']->fetchAssoc($bm);
            if(($bm!="")&&($bm!=NULL))
            {
                $discountID=$bm['discount_master_iddiscount_master'];
                discountMaster::__construct($discountID);
                if($this->discountValid)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    function makeBooking($userEmail,$userMobile=NULL,$discount)
    {
        $app=$this->app;
        $userEmail=trim(addslashes(htmlentities($userEmail)));
        if(($userEmail!="")&&($userEmail!=NULL)&&(filter_var($userEmail, FILTER_VALIDATE_EMAIL)))
        {
            $userMobile=trim(addslashes(htmlentities($userMobile)));
            $discount=addslashes(htmlentities($discount));
            if(($discount!="")&&($discount!=NULL))
            {
                $in="INSERT INTO booking_master (stat,timestamp,user_email,user_mobile,base_discount_period) VALUES ('2',NOW(),'$userEmail','$userMobile','$discount')";
                $in=$app['db']->executeQuery($in);
                $bm="SELECT idbooking_master FROM booking_master WHERE stat='2' AND user_email='$userEmail' AND user_mobile='$userMobile' ORDER BY idbooking_master DESC LIMIT 1";
                $bm=$app['db']->fetchAssoc($bm);
                $bookingID=$bm['idbooking_master'];
                return "BOOKING_MADE_".$bookingID;
            }
            else
            {
                return "INVALID_PRE_DISCOUNT";
            }
        }
        else
        {
            return "INVALID_USER_EMAIL";
        }
    }
}
?>