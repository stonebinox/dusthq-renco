<?php
/*---------------------------------
Author: Anoop Santhanam
Date Created: 27/1017 11:37
Last Modified: 27/1017 11:37
Comments: Main class file for 
item_master table.
--------------------------------*/
class itemMaster
{
    public $app=NULL;
    public $itemValid=false;
    private $item_id=NULL;
    function __construct($itemID=NULL) 
    {
        $this->app=$GLOBALS['app'];
        if($itemID!=NULL)
        {
            $this->item_id=addslashes(htmlentities($itemID));
            $this->itemValid=$this->verifyItem();
        }
    }
    function verifyItem() // to verify an item
    {
        if($this->item_id!=NULL)
        {
            $itemID=$this->item_id;
            $app=$this->app;
            $im="SELECT iditem_master FROM item_master WHERE stat='1' AND iditem_master='$itemID'";
            $im=$app['db']->fetchAssoc($im);
            if(($im!="")&&($im!=NULL))
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
    function getItem() //to get an item row
    {
        if($this->itemValid)
        {
            $app=$this->app;
            $itemID=$this->item_id;
            $im="SELECT * FROM item_master WHERE iditem_master='$itemID'";
            $im=$app['db']->fetchAssoc($im);
            if(($m!="")&&($im!=NULL))
            {
                return $im;
            }
            else
            {
                return "INVALID_ITEM_ID";
            }
        }
        else
        {
            return "INVALID_ITEM_ID";
        }
    }
    function getItems() //to get all valid items
    {
        $app=$this->app;
        $im="SELECT iditem_master FROM item_master WHERE stat='1'";
        $im=$app['db']->fetchAll($im);
        $itemArray=array();
        for($i=0;$i<count($im);$i++)
        {
            $item=$im[$i];
            $itemID=$item['iditem_master'];
            $this->__construct($itemID);
            $itemRow=$this->getItem();
            if(is_array($itemRow))
            {
                array_push($itemArray,$itemRow);
            }
        }
        if(count($itemArray)>0)
        {
            return $itemArray;
        }
        else
        {
            return "NO_ITEMS_FOUND";
        }
    }
}
?>