<?php

class KettleException extends Exception
{

}

class NotEnoughEmptyCapacityException extends KettleException
{
    protected $message = 'Not enough space for capacity';
}

class KettleDoesNotContainThisAmountOfLiquidException extends KettleException
{
    protected $message = 'The kettle does not contain this amount of liquid';
}

class KettleIsOfflineException extends KettleException
{
    protected $message = 'The Kettle is offline';
}