<?php
/*
 * @name String.type.php
 * @copyright (c) 2013 sinan eker
 * */
class String extends \Datatype\Datatype 
{
    public function __construct($string)
    {
        if (is_string($string))
        {
            parent::__construct($string);
        }
    }
}