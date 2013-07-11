<?php

/*
 * @name Model.class.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Model.namespace.php
 *      Model.interface.php
 *      Model.trait.php
 */
namespace Model;
class Model implements \Model\interfaces\Model 
{
    use \Model\traits\Model 
    {
        getVersion as public;
        getAuthor as public;
        
        out as public;
    }
}