<?php

/*
 * @name Model.interface.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Model.namespace.php
 */
namespace Model\interfaces;
interface Model {
    static function getVersion();
    static function getAuthor();
    
    static function out();
}