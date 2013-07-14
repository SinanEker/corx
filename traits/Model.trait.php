<?php

/*
 * @name Model.trait.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Model.namespace.php
 */
namespace Model\traits;
trait Model
{
    /*
     * @const CORX_VERSION_NUMBER
     * @param void
     * @return string CORX_VERSION_NUMBER | void
     * @throws LogicException
     * @description:
     * * The method returns the corx version number.
     * * If the constant CORX_VERSION_NUMBER isn't defined, 
     * * the method throws a LogicException exception.
     */
    private static function getVersion()
    {
        if (defined("CORX_VERSION_NUMBER"))
        {
            return CORX_VERSION_NUMBER;
        } else {
            throw new \LogicException("CORX_VERSION_NUMBER does not exists.");
        }
    }
    
    /*
     * @const CORX_AUTHOR
     * @param void
     * @return string CORX_AUTHOR | void
     * @throws LogicException
     * @description:
     * * The method returns the corx author.
     * * If the constant CORX_AUTHOR isn't defined, 
     * * the method throws a LogicException exception.
     */
    private static function getAuthor()
    {
        if (defined("CORX_AUTHOR"))
        {
            return CORX_AUTHOR;
        } else {
            throw new \LogicException("CORX_AUTHOR does not exists.");
        }
    }
    
    /*
     * @const BOOL_AS_UPPER 
     * @param [mixed $out]
     * @return void
     * @throws LogicException
     * @description:
     * * The method returns the corx author.
     * * If the constant CORX_AUTHOR isn't defined, 
     * * the method throws a LogicException exception.
     * @example
     * * Model::out(1, 1.0, true, false, [1, 1.0, [true, false], "str", String("string")], "str", String("string"));
     * * //  echos 11TRUEFALSE11TRUEFALSEstrstringstrstring
     */
    private static function out()
    {    
        $args = func_get_args();
        $count = count($args);
        \Helpers\Helpers::noArgsException($count);
        
        $i = 0;

        $true = "true";
        $false = "false";
        
        $me = [ "\Model\Model", "out" ];
        
        if (BOOL_AS_UPPER === true)
        {
            $true = "TRUE";
            $false = "FALSE";
        }

        while ($i < $count)
        {
            if ($args[$i] === void)
            {
                echo "void";
            }
            if (is_array($args[$i]))
            {
                call_user_func_array($me, $args[$i]);
            } else if (is_str_o($args[$i]) || is_bool_o($args[$i]) || is_float_o($args[$i]) || is_int_o($args[$i]))
            {
                echo $args[$i]->getValue();
            } else if (is_bool($args[$i]))
            {
                if ($args[$i] === true)
                {
                    echo $true;
                } else {
                    echo $false;
                }
            } else if (is_callable($args[$i])) {
                echo "{closure}";
            }
            ++$i;
        }
        
        return void;
    }
    
    /* MAIN */
    
    private function __construct()
    {
        
    }
}