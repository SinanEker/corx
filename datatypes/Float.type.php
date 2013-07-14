<?php
/*
 * @name Float.type.php
 * @copyright (c) 2013 sinan eker
 * */
class Float extends \Datatype\Datatype 
{
    public function __construct($float)
    {
        if (is_float($float))
        {
            parent::__construct($float);
        }
    }
}