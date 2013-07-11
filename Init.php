<?php
/*
 * @name Init.php
 * @copyright:
 * * Copyright (c) 2013, Sinan Eker, Selsyourself, inc.
 * * All rights reserved.
 * * --------------------------------------------------------------------------------
 * * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"    +
 * * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE      +
 * * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE +
 * * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE    +
 * * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL     +
 * * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR     +
 * * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER     +
 * * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,  +
 * * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE  +
 * * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.           +
 * * --------------------------------------------------------------------------------
 * */

# set_include_path(".:/Applications/mappstack-5.4.16-0/apache2/htdocs/corx/");
 
/*
 * @description:
 * * Defenition of required constants.
 */ 
define("CORX_VERSION_NUMBER", "1.0");
define("USE_CORX_MIN", true);
define("CORX_ERROR_REPORTING", -1);
define("DEFINE_DATATYPE_FUNCTIONS", true);
define("LOAD_VIA_SPL", false);
define("DEFAULT_MIME_TYPE", "html");
define("DEFAULT_GZIP_CHARSET", "UTF-8");
define("DEFAULT_CHARSET", "UTF-8");
define("DEFAULT_CACHE_CONTROL", "must-revalidate");
define("UNCAUGHT_EXEPTION_MESSAGE", "Uncaught exception thrown:\t\n Message:\t");
define("BOOL_AS_UPPER", false);
define("EXCEPTION_ALWAYS_AS_JSON", true);

define("ARGUMENTS_EXCEPTION", true);
define("DISPLAY_UNCAUGHT_EXEPTIONS", true);

define("ENABLE_PLUGINS", true);
define("PLUGIN_DIR", "corx/plugins");
/*
 * @description:
 * * void is the same as nil / null. It's there to make it simpler to understand the code.
 */
define("void", null);


define("BACKSLASH", "\\");

/*
 * @description:
 * * Sets the error reporting level by CORX_ERROR_REPORTING
 * @uses const CORX_ERROR_REPORTING
 */
error_reporting(CORX_ERROR_REPORTING);

/*
 * @param void
 * @return void
 * @uses const USE_CORX_MIN
 * @description:
 * * This function loads the Corx library
 * * * THIS IS A INTERNAL MAIN FUNCTION.
 * * * DONT MOVE ODER MODIFY IT!
 */
function LOAD_CORX()
{
    if (USE_CORX_MIN === true)
    {
        require_once "corx/Corx.min.php";
    } else {
        require_once "corx/Corx.php";
    }
}

LOAD_CORX(); // load it!

/*
 * @description:
 * * Use declerations
 */
use \Corx\CorxLoader\CorxAutoLoad as CorxAutoLoad;
use \Corx\CorxLoader\CorxSetAndLoad as CorxSetAndLoad;
use \Corx\CorxTest\CorxTest as CorxTest;

/*
 * @description:
 * * The following code tests the Corx library.
 */
try {
    CorxTest::test(function(){}, void);
} catch (\RuntimeException $e) {
    die($e->getMessage());
    exit;
}

/*
 * @param void
 * @returns (object) CorxAutoLoad
 * @name CLASS_FILE_LOAD
 * @description:
 * * * THIS IS A INTERNAL MAIN FUNCTION.
 * * * DONT MOVE ODER MODIFY IT!
 */
function CLASS_FILE_LOAD()
{
    return new CorxAutoLoad(function($thiz){ // load all classes that are defined
        $thiz->loadRequiredFiles([
            "namespace",
            "interface",
            "trait",
            "class"
        ]);
    });
}

/*
 * use spl class loader?
 */
if (LOAD_VIA_SPL === true)
{
    if (!extension_loaded("SPL"))
    {
        throw new \RuntimeException("Extension SPL isn't loaded. You can't use spl_autoload_register.");
    } else {
        spl_autoload_register(function ($class) {
            return CLASS_FILE_LOAD();
        });
    }
} else {
    CLASS_FILE_LOAD();
}

/*
 * sets the exception handler
 */#
if (DISPLAY_UNCAUGHT_EXEPTIONS === false)
{
    set_exception_handler(function ($exception)
    {

    });
} else {
    set_exception_handler(function ($exception)
    {
        if (EXCEPTION_ALWAYS_AS_JSON === true)
        {
            if (LOAD_VIA_SPL === true)
            {
                die("Unable to do that. Disable LOAD_VIA_SPL to use this feature!");
            } else {
                echo \Helpers\Helpers::exceptionToJson($exception, ["type" => "UNCAUGHT_EXEPTION"]);
            }
        } else {
            die(UNCAUGHT_EXEPTION_MESSAGE.$exception->getMessage()."\n");
        }
    });
}
/*
 * @TODO: Datatype instructions
 * @description:
 * * This loads the datatype files, that provides following classes:
 * * - String
 * * - Bool
 * * - Int
 * * - Float
 * * -----------------------------------------------------------------
 * * Code example:
 * * $datatype_test = [
 * *    (new String("str"))->getValue(), // "str"
 * *    (new Bool(true))->getValue(),    // true
 * *    (new Float(1.324))->getValue(),  // 1.324
 * *    (new Int(2143))->getValue()      // 2143
 * * ];
 * * var_dump($datatype_test); // dumps the array
 * * -----------------------------------------------------------------
 * * For what are these datatype classes?
 * * You can use them as function parametes to avoid type validation.
 * * Code example:
 * * function sayName(String $myName)
 * * {
 * *    echo "my name is".$myString->getValue();
 * * }
 * *
 * * sayName(new String("John Doe")); // echos: my name is John Doe
 * * -----------------------------------------------------------------
 */
$corxAutoLoad = (new CorxAutoLoad)->loadDatatypes();

if (DEFINE_DATATYPE_FUNCTIONS === true)
{
    /*
     * @param [string $string = ""]
     */
    function String($str = "")
    {
        return new String($str);
    }
    
    /*
     * @param [bool $bool = false]
     */
    function Bool($bool = false)
    {
        return new Bool($bool);
    }
    
    /*
     * @param [float $float = 0]
     */
    function Float($float = 0)
    {
        return new Float($float);
    }
    
    /*
     * @param [int $int = 0]
     */
    function Int($int = 0)
    {
        return new Int($int);
    } 
}


/*
 * @name is_str_o
 * @param [object $object]
 */
function is_str_o($object)
{
    return ($object instanceof \String);
}
/*
 * @name is_bool_o
 * @param [object $object]
 */
function is_bool_o($object)
{
    return ($object instanceof \Bool);
}
/*
 * @name is_float_o
 * @param [object $object]
 */
function is_float_o($object)
{
    return ($object instanceof \Float);
}
/*
 * @name is_int_o
 * @param [object $object]
 */
function is_int_o($object)
{
    return ($object instanceof \Int);
}

/*
 * @TODO Request and Headers
 * @description:j
 * * The Headers and Request class are helpers for the Gzip class. 
 * * The Headers class provides the request headers for the Request class.
 * * The RequestHeaders class is a summary of Request and Headers.
 * * -----------------------------------------------------------------
 */

/*
 * @description:
 * * Use declerations
 */
use \Request\Request as Request;
use \Headers\Headers as Headers;

/*
 * @TODO: Request and Headers classes in one
 * @description:
 * * The class RequestHeaders combines Request and Headers classes
 * * Code example:
 * * $request = (new RequestHeaders)->getRequest(); // get the Request object
 */
class RequestHeaders
{
    /*
     * @protected
     */
    protected $request = void;

    /*
     * @var $request

     */
    public function __construct()
    {
        $this->request = new Request(new Headers());
    }
    
    /*
     * @param void
     * @return object Request 
     */
    public function getRequest()
    {
        return $this->request;
    }
}

/*
 * @description:
 * * Re-name Gzip for simple useage
 */
class Gzip extends \Gzip\Gzip {}
/*
 * @TODO Gzip
 * @description:
 * * The Gzip class gzips the webpage, sets the header and those stuff.
 * * Code example:
 * * $gzip = new Gzip($request);
 * * $gzip->gzip(String("html")); // gzips the page as text/html
 */


if (ENABLE_PLUGINS === true)
{
    function PLUGIN_LOADER()
    {
        $scan = array_values(array_diff(scandir(PLUGIN_DIR), ['..', '.']));
        $count = count($scan);
        if ($count === 0)
        {
            throw new \RuntimeException("Unable to load the plugin, because no plugins exists.");
        }
        $PARSED_INI = [];
        $i = 0;
        while ($i < $count)
        {
            $PARSED_INI[$scan[$i]] = parse_ini_file( PLUGIN_DIR."/" .$scan[$i] . "/Config.ini", true);
            require_once PLUGIN_DIR."/".$scan[$i]."/".trim($scan[$i], ".plugin").".class.php";
            ++$i;
        }
        return $PARSED_INI;
    }
    $GLOBALS["PARSED_INI_FILES"] = PLUGIN_LOADER();
}