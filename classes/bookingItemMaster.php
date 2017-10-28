<?php
/*--------------------------------------
Author: Anoop Santhanam
Date created: 27/10/17 22:45
Last Modified: 27/10/17 22:45
Comments: Main class file for 
booking_item_master table.
------------------------------------*/
class bookingItemMaster extends bookingMaster
{
    public $app=NULL;
    public $bookingItemValid=false;
    private $booking_item_id=false;
    function __construct($bookingItemID=NULL)
    {
        $this->app=$GLOBALS['app'];
        if($bookingItemID!=NULL)
        {
            $this->booking_item_id=addslashes(htmlentities($bookingItemID));
            $this->bookingItemValid=$this->verifyBookingItem();
        }
    }
    function verifyBookingItem()
    {
        if($this->booking_item_id!=NULL)
        {
            $app=$this->app;
            $bookingItemID=$this->booking_item_id;
            $bim="SELECT booking_master_idbooking_master,item_master_iditem_master FROM booking_item_master WHERE stat='1' AND idbooking_item_master='$bookingItemID'";
            $bim=$app['db']->fetchAssoc($bim);
            if(($bim!="")&&($bim!=NULL))
            {
                $bookingID=$bim['booking_master_idbooking_master'];
                $itemID=$bim['item_master_iditem_master'];
                bookingMaster::__construct($bookingID);
                if($this->bookingValid)
                {
                    itemMaster::__construct($itemID);
                    if($this->itemValid)
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
        else
        {
            return false;
        }
    }
    function storeBookingItem($bookingID,$itemID,$itemQuantity=1)
    {
        $app=$this->app;
        $bookingID=addslashes(htmlentities($bookingID));
        bookingMaster::__construct($bookingID);
        if($this->bookingValid)
        {
            $itemID=addslashes(htmlentities($itemID));
            itemMaster::__construct($itemID);
            if($this->itemValid)
            {
                $itemQuantity=addslashes(htmlentities($itemQuantity));
                if(($itemQuantity!="")&&($itemQuantity!=NULL)&&(is_numeric($itemQuantity))&&($itemQuantity>0))
                {
                    $bim="SELECT idbooking_item_master FROM booking_item_master WHERE stat='1' AND booking_master_idbooking_master='$bookingID' AND item_master_iditem_master='$itemID'";
                    $bim=$app['db']->fetchAssoc($bim);
                    if(($bim=="")||($bim==NULL))
                    {
                        $in="INSERT INTO booking_item_master (timestamp,booking_master_idbooking_master,item_master_iditem_master,item_quantity) VALUES (NOW(),'$bookingID','$itemID','$itemQuantity')";
                        $in=$app['db']->executeQuery($in);
                        $bim="SELECT idbooking_item_master FROM booking_item_master WHERE stat='1' AND booking_master_idbooking_master='$bookingID' AND item_master_iditem_master='$itemID' AND item_quantity='$itemQuantity' ORDER BY idbooking_item_master DESC LIMIT 1";
                        $bim=$app['db']->fetchAssoc($bim);
                        $bookingItemID=$bim['idbooking_item_master'];
                        return "BOOKING_ITEM_ADDED_".$bookingItemID;
                    }
                    else
                    {
                        return "BOOKING_ITEM_ALREADY_ADDED";
                    }
                }
                else
                {
                    return "INVALID_ITEM_QUANTITY";
                }
            }
            else
            {
                return "INVALID_ITEM_ID";
            }
        }
        else
        {
            return "INVALID_BOOKING_ID";
        }
    }
    function getBookingItem()
    {
        if($this->bookingItemValid)
        {
            $app=$this->app;
            $bookingItemID=$this->booking_item_id;
            $bim="SELECT * FROM booking_item_master WHERE idbooking_item_master='$bookingItemID'";
            $bim=$app['db']->fetchAssoc($bim);
            if(($bim!="")&&($bim!=NULL))
            {
                $bookingID=$bim['booking_master_idbooking_master'];
                bookingMaster::__construct($bookingID);
                $booking=bookingMaster::getBooking();
                if(is_array($booking))
                {
                    $bim['booking_master_idbooking_master']=$booking;
                }
                $itemID=$bim['item_master_iditem_master'];
                itemMaster::__construct($itemID);
                $item=itemMaster::getItem();
                if(is_array($item))
                {
                    $bim['item_master_iditem_master']=$item;
                }
                return $bim;
            }
            else
            {
                return "INVALID_BOOKING_ITEM_ID";
            }
        }
        else
        {
            return "INVALID_BOOKING_ITEM_ID";
        }
    }
    function getBookingItems($bookingID)
    {
        $bookingID=addslashes(htmlentities($bookingID));
        $app=$this->app;
        bookingMaster::__construct($bookingID);
        if($this->bookingValid)
        {
            $bim="SELECT idbooking_item_master FROM booking_item_master WHERE stat='1' AND booking_master_idbooking_master='$bookingID'";
            $bim=$app['db']->fetchAll($bim);
            $bookingItemArray=array();
            for($i=0;$i<count($bim);$i++)
            {
                $bookingItem=$bim[$i];
                $bookingItemID=$bookingItem['idbooking_item_master'];
                $this->__construct($bookingItemID);
                $bookingItemRow=$this->getBookingItem();
                if(is_array($bookingItemRow))
                {
                    array_push($bookingItemArray,$bookingItemRow);
                }
            }
            if(count($bookingItemArray)>0)
            {
                return $bookingItemArray;
            }
            else
            {
                return "NO_BOOKING_ITEMS_FOUND";
            }
        }
        else
        {
            return "INVALID_BOOKING_ID";
        }
    }
}
?>