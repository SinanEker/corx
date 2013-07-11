<?php
/*
 * @name Int.type.php
 * @copyright (c) 2013 sinan eker
 * */
class Int extends \Datatype\Datatype 
{
    public function __construct($int)
    {
        if (is_int($int))
        {
            parent::__construct($int);
        }
    }
}