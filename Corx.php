<?php
/*
 * @name Corx.php
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

namespace Corx;
/*
 * @name CorxInterface
 * @namespace Corx
 */
interface CorxInterface 
{
    function load($folder, array $names, $extension);
    function loadRequiredFiles(array $types);
    function loadDatatypes();
    function setFiles(array $files);
    function setDatatypeFiles(array $datatype_files);
}

define("nil", null);
define("CORX_PHP_VERSION", phpversion());

/*
 * @name CorxTrait
 * @namespace Corx
 */
trait CorxTrait 
{
    /*
     * @void
     * @param string $folder, array $names, string $extension
     * @throws:
     * * [InvalidArgumentException, message => $folder must be a string.]
     * * [InvalidArgumentException, message => $extension must be a string.]
     */
    private function load($folder, array $names, $extension)
    {
        if (!is_string($folder))
        {
            throw new \InvalidArgumentException("$folder must be a string.");
        }
        if (!is_string($extension))
        {
            throw new \InvalidArgumentException("$extension must be a string.");
        }
        $i = 0; 
        while($i < count($names)) 
        {
            if ($folder == "datatypes")
            {
                require_once $folder."/".$names[$i].$extension;
            } else {
                require_once $folder.$names[$i].$extension;
            }
            ++$i;
        }
    }
    /*
     * @void
     * @param array $types
     * @description:
     * * This method performs a file load.
     * * The class have to contain or inherit the load method and $files variable.
     */
    private function loadRequiredFiles(array $types)
    {
        foreach ($types as $key => $value) 
        {
            self::load($this->files[$types[$key]]['folder'], $this->files[$types[$key]]['names'], $this->files[$types[$key]]['extension']);
        }
    }
    /*
     * @void
     * @description:
     * * This method performs a file load.
     * * The class have to contain or inherit the load method and $datatype_files variable.
     */
    private function loadDatatypes()
    {
        self::load($this->datatype_files['folder'], $this->datatype_files['names'], $this->datatype_files['extension']);
    }
    /*
     * @void
     * @param: array $files
     * @description:
     * * Sets the $files variable in a class.
     */
    private function setFiles(array $files)
    {
        $this->files = $files;
    }
    
    /*
     * @void
     * @param: array $datatype_files
     * @description:
     * * Sets the $datatype_files variable in a class.
     */
    protected function setDatatypeFiles(array $datatype_files)
    {
        $this->datatype_files = $datatype_files;
    }
    
}
/*
 * @name CorxConstructTrait
 * @namespace Corx
 */
trait CorxConstructTrait
{
    /*
     * @void
     * @param: [array $files], [array $datatype_files]
     * @description:
     * * The parameters have to be array, otherwise the method is unuseful.
     * * The method is a class initializer. The class have to contain or inherit the setFiles and/or setDatatypeFiles methods.
     */
    private function __construct($files = nil, $datatype_files = nil) 
    {
        if (is_array($files)) 
        {
            self::setFiles($files);
        }
        
        if (is_array($datatype_files))
        {
            self::setDatatypeFiles($datatype_files);
        }
    }
}

trait CorxBaseCallbackTrait
{
    /*
     * @void
     * @param: [callable $fn], [mixed $first_param = null], [mixed $data = null]
     */
    private function __callback(callable $fn, $first_param = nil, $data = nil)
    {
        if (is_array($data)) 
        {
            if ($first_param !== nil)
            {
                call_user_func_array($fn, [$first_param, $data]);
            } else {
                call_user_func_array($fn, [$data]);
            }
        } else if ($data !== nil) {
            if ($first_param !== nil)
            {
                $fn($first_param, $data);
            } else {
                $fn();
            }
        } else {
            if ($first_param !== nil)
            {
                $fn($first_param);
            } else {
                $fn();
            }
        }
    }
    /*
     * @void
     * @param: [object $this = null], [callable $fn], [mixed $data = null]
     */
    private function callback($thiz = nil, callable $fn, $data = nil)
    {
        if ($thiz === nil)
        {
            throw new \InvalidArgumentException("\$thiz can\'t be null!");
        }
        
        if (!is_object($thiz))
        {
            throw new \InvalidArgumentException("\$thiz must be a object!");
        }
        self::__callback($fn, $thiz, $data);
    }
}

/*
 * @name CorxConstructTraitFn
 * @namespace Corx
 * @description:
 * * Defines the CorxConstructTraitFn
 */
trait CorxConstructTraitFn
{
    /*
     * @void
     * @param [callable $fn = null], [mixed $data = null]
     * @note: The class that uses this trait must extends a class that uses CorxBaseCallbackTrait or have to use the CorxBaseCallbackTrait trait!
     * @description:
     * * This method constructs a class. It can be used in many ways, like constructing with a callback.
     * * The $fn param is the callback that is fired when the function is called. The $data param can be anything.
     * * If you use a array as the second parameter int the function, the callback will be called over call_user_func_array.
     * * That means the array values are fired as single parameters to the callback function.
     */
    private function __construct($fn = nil, $data = nil)
    {
        if ($fn !== nil)
        {
            self::callback($this, $fn, $data); // from CorxBaseCallbackTrait
        }
    }
}

/*
 * @description: 
 * * Defines the Corx\CorxLoader namespace.
 * * The namespace is used to structure the code.
 */
namespace Corx\CorxLoader;

/* use declarations */
use \Corx\CorxConstruct;
use \Corx\CorxInterface;
use \Corx\CorxTrait;
use \Corx\CorxConstructTrait;
use \Corx\CorxBaseCallbackTrait;
use \Corx\CorxConstructTraitFn;

/*
 * @name Corx
 * @namespace Corx\CorxLoader
 * @implements Corx\CorxInterface
 */
class Corx implements CorxInterface 
{
    use CorxTrait, CorxBaseCallbackTrait, CorxConstructTraitFn
    {
        /* from private to public */
        callback as public;
        __construct as public; // from CorxConstructTraitFn
        loadRequiredFiles as public; 
        loadDatatypes as public;
    }
}

/*
 * @name CorxAutoLoad
 * @namespace Corx\CorxAutoloader
 * @extends Corx
 * @description:
 * * This class inherits the $files and $datatype_files defention from Corx. 
 * * The variables are protected and have to be setted if you want to use the CorxAutoLoad class.
 */
class CorxAutoLoad extends Corx
{
    /*
     * @protected
     * @description:
     * * A array with the following required keys:
     * * namespace => [ names => [ ... ], folder => ..., extension => ... ],
     * * interface => [ names => [ ... ], folder => ..., extension => ... ],
     * * trait => [ names => [ ... ], folder => ..., extension => ... ],
     * * class => [ names => [ ... ], folder => ..., extension => ... ]
     */
    protected $files = [
        'namespace' => [
            'names' => [
                'Helpers',
                'Model',
                'View',
                'Datatype',
                'Headers',
                'Request',
                'Gzip',
                'Plugins'
            ],
            'folder' => 'namespaces/',
            'extension' => '.namespace.php'
        ],
        'interface' => [
            'names' => [
                'Helpers',
                'Model',
                'View',
                'Datatype',
                'Headers',
                'Request',
                'Gzip'
            ],
            'folder' => 'interfaces/',
            'extension' => '.interface.php'
        ],
        'trait' => [
            'names' => [
                'Helpers',
                'Model',
                'View',
                'Datatype',
                'Headers',
                'Request',
                'Gzip'
            ],
            'folder' => 'traits/',
            'extension' => '.trait.php'
        ],
        'class' => [
            'names' => [
                'Helpers',
                'Model',
                'View',
                'Datatype',
                'Headers',
                'Request',
                'Gzip'
            ],
            'folder' => 'classes/',
            'extension' => '.class.php'
        ]
    ];
    /*
     * @protected
     * @description:
     * * A array with the following required keys:
     * * names => [ ..., ..., ... ], folder => ..., extension => ...
     */
    protected $datatype_files = [
        'names' => [
            'Bool',
            'Float',
            'Int',
            'String'
        ],
        'folder' => 'datatypes',
        'extension' => '.type.php'
    ];
}

/*
 * @name CorxSetAndLoad
 * @namespace Corx\CorxAutoloader
 */
class CorxSetAndLoad
{
    use CorxTrait, CorxConstructTrait
    {
        /* from private to public */
        __construct as public;
        loadRequiredFiles as public;
        loadDatatypes as public;
    }
}

/*
 * @description: 
 * * Defines the Corx\CorxTest namespace.
 * * The namespace is used to test the current version of the code and other stuff.
 */
namespace Corx\CorxTest;

/* use declarations */
use \Corx\CorxBaseCallbackTrait;

/*
 * @name CorxTest
 * @namespace CorxTest
 * @uses Corx\CorxConstructTraitFn => __construct
 */
class CorxTest
{
    use CorxBaseCallbackTrait 
    {
        /* from private to public */
        __callback as public;
    }
    
    /*
     * @void
     * @param: [callable $callback], [mixed $data = null]
     * @static
     * @description: Tests the php version and executes a callback
     */
    public static function test(callable $callback, $data = nil)
    {   
        if (!version_compare(CORX_PHP_VERSION, "5.4", ">="))
        {
            throw new \RuntimeException("You run a incompatible PHP version on you os. Current version: ".CORX_PHP_VERSION);
        }
        (new CorxTest())->__callback($callback, nil, $data);
    }
}