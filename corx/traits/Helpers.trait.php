<?php

/*
 * @name Helpers.trait.php
 * @copyright (c) 2013 sinan eker
 * @required:
 *      Helpers.namespace.php
 */
namespace Helpers\traits;
trait Helpers 
{
    /*
     * @param [int $count = 0]
     * @return void
     * @exception InvalidArgumentException when $count = 0
     * @description
     * * This function throws a InvalidArgumentException if the $count param is 0.
     * * It can be used to check if a function has at least one parameter given.
     */
    private static function noArgsException($count = 0)
    {
        if (ARGUMENTS_EXCEPTION === false)
        {
            return void;
        } else {
            if ($count === 0)
            {
                throw new \InvalidArgumentException("Not enaught arguments given!");
            }
        }
    }

    /*
     * @param [mixed &$var]
     * @return array $dump | string $dump
     * @exception InvalidArgumentException when no arguments are given
     * @description
     * * The function is like the var_dump function, except this function returns the result of the var_dump using output buffering (ob).
     * * When you pass just one parameter to the function, it will only return the dump string not the one dump string in a array.
     * * Passing two parameters or more to the function will return a array with the dump strings.
     */
    private static function varDump()
    {
        $args = func_get_args();
        $count = count($args);
        $i = 0;
        $dump = [];
        static::noArgsException($count);
        while ($i < $count)
        {
            ob_start();
            var_dump($args[$i]);
            $dump[$i] = ob_get_clean();
            ++$i;
        }
        if (count($dump) === 1)
        {
            return $dump[0];
        }
        return $dump;
    }
    /*
     * @name contains
     * @param [string $haystack, string | array $needle]
     * @return bool $str_contains
     * @exception
     * * \InvalidArgumentException when $haystack isn't a string
     * * \InvalidArgumentException when $needle isn't a array or string
     */
    private static function contains($haystack, $needle)
    {
        if (!is_string($haystack))
        {
            throw new \InvalidArgumentException("\$haystack must be a string!");
        }
        if (!is_string($needle) || !is_array($needle))
        {
            throw new \InvalidArgumentException("\$needle must be a string or a array! Given type: ".gettype($needle)."; Dump: ".static::varDump($needle));
        }
        
        foreach ((array) $needle as $x)
        {
            if (strpos($haystack, $x) !== false)
            {
                return true;
            }
        }
        
        return false;
    }
    
    /*
     * @name getMainNamespaceName
     * @param [string $namespace = __NAMESPACE__]
     * @return string $main_namespace
     */
    private static function getMainNamespaceName($namespace = __NAMESPACE__)
    {
        if (!static::contains($namespace, "\\"))
        {
            return $namespace;
        } else {
            $exp = explode(BACKSLASH, $namespace);
            return $exp[0];
        }
    }
    
    /*
     * @example
     * * var_dump( Helpers::getAllArrayValuesExcept(1, [0 => true, 1 => "foo", 2 => "bar"]) );
     * * array(2) {
     * *  [0]=>
     * *  bool(true)
     * *  [1]=>
     * *  string(3) "bar"
     * * }
     */
    private static function getAllArrayValuesExcept($key, array $array)
    {
        $newArray = [];
        foreach ($array as $k => $val)
        {
            if ($k === $key)
            {
                continue;
            }
            $newArray[] = $val;
        }
        return $newArray;
    }
    
    /*
     * @name appendArray
     * @param [array $appendTo, array &$arrays]
     * @return array $appendTo | void
     * @exception InvalidArgumentException @see \Helpers\Helpers::noArgsException
     * @example
     * * var_dump( Helpers::appendArray(["first" => "foo", "second" => "bar"], ["third" => "baz"], ["fourth" => "bat"]) );
     * * /*
     * * array(4) {
     * *    ["first"]=>
     * *    string(3) "foo"
     * *    ["second"]=>
     * *    string(3) "bar"
     * *    ["third"]=>
     * *    string(3) "baz"
     * *    ["fourth"]=>
     * *    string(3) "bat"
     * * }
     * *
     */
    private static function appendArray()
    {
        $args = func_get_args();
        if (count($args) < 2)
        {
            static::noArgsException();
        }
        return array_merge($args[0], call_user_func_array("array_merge", static::getAllArrayValuesExcept(0, $args)));
    }
    
    /*
     * @name shiftKeyUp
     * @param [array $array]
     * @return array $newArray
     * @description
     * * This function shifts every array key up by 1.
     * * Before doing that the array will be converted to a numerical array.
     */
    private static function shiftKeyUp(array $array)
    {
        $array = array_values($array); // convert to numerical array
        $newArray = [];
        $i = 0;
        while ($i < count($array))
        {
            $newArray[$i+1] = $array[$i];
            ++$i;
        }
        return $newArray;
    }
    
    /*
     * @name exceptionToJson
     * @param [(Exception $e, array &$add]
     * @return string $json_string
     */
    private static function exceptionToJson(\Exception $e)
    {
        $args = func_get_args();
        $exception = [
            $e->getMessage()."\n",
            $e->getFile()."\n",
            $e->getCode()."\n",
            $e->getLine()."\n",
            "trace" => []
        ];
        $trace = $e->getTrace();
        $i = 0;
        while ($i < count($trace))
        {
            $exception["trace"][$i] = $trace[$i];
            ++$i;
        }
        $i = 0;
        while ($i < 4)
        {
            $exception[$i] = str_replace(['"', "\n"], ["'", ""], $exception[$i]);
            ++$i;
        }

        $json_array = array_combine(["message","file","code","line","trace"], array_values($exception));
        
        if (count($args) > 1)
        {
            $arg = static::shiftKeyUp( static::getAllArrayValuesExcept(0, $args) ); // shift key up to reserve key one for the first argument | getting all other function arguments
            $arg[0] = $json_array; // first function parameter
            $json_array = call_user_func_array([ "\Helpers\Helpers", "appendArray" ], $arg);
        }
        return json_encode($json_array);
    }
}