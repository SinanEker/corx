<?php

/*
 * @name View.trait.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      View.namespace.php
 */
namespace View\traits;
trait View 
{
    private function __construct()
    {
        $this->request = (new RequestHeaders)->getRequest();
    }
    
    
}