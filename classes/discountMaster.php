<?php
/*--------------------------------
Author: Anoop Santhanam
Date Created: 27/10/17 11:56
Last Modified: 27/10/17 11:56
Comments: Main class file for 
discount_master table.
------------------------------*/
class discountMaster 
{
    public $app=NULL;
    public $discountValid=false;
    private $discount_id=NULL;
    function __construct($discountID=NULL)
    {
        $this->app=$GLOBALS['app'];
        if($discountID!=NULL)
        {
            $this->discount_id=addslashes(htmlentities($discountID));
            $this->discountValid=$this->verifyDiscount();
        }
    }
    function verifyDiscount()
    {
        if($this->discount_id!=NULL)
        {
            $discountID=$this->discount_id;
            $app=$this->app;
            $dm="SELECT iddiscount_master FROM discount_master WHERE stat='1' AND iddiscount_master='$discountID'";
            $dm=$app['db']->fetchAssoc($dm);
            if(($dm!="")&&($dm!=NULL))
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
    function getDiscount()
    {
        if($this->discountValid)
        {
            $app=$this->app;
            $discountID=$this->discount_id;
            $dm="SELECT * FROM discount_master WHERE iddiscount_master='$discountID'";
            $dm=$app['db']->fetchAssoc($dm);
            if(($dm!="")&&($dm!=NULL))
            {
                return $dm;
            }
            else
            {
                return "INVALID_DISCOUNT_ID";
            }
        }
        else
        {
            return "INVALID_DISCOUNT_ID";
        }
    }
    function getDiscounts()
    {
        $app=$this->app;
        $dm="SELECT iddiscount_master FROM discount_master WHERE stat='1'";
        $dm=$app['db']->fetchAll($dm);
        $discoutnArray=array();
        for($i=0;$i<count($dm);$i++)
        {
            $discount=$dm[$i];
            $discountID=$discount['iddiscount_master'];
            $this->__construct($discountID);
            $discountRow=$this->getDiscount();
            if(is_array($dicountRow))
            {
                array_push($discountArray,$discountRow);
            }
        }
        if(count($discountArray)>0)
        {
            return $discountArray;
        }
        else
        {
            return "NO_DISCOUNTS_FOUND";
        }
    }
}
?>