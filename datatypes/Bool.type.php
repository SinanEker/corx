<?php
/*
 * @name Bool.type.php
 * @copyright (c) 2013 sinan eker
 * */
class Bool extends \Datatype\Datatype 
{
    public function __construct($bool)
    {
        if (is_bool($bool))
        {
            parent::__construct($bool);
        }
    }
}