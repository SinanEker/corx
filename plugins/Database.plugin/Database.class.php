<?php
/*
 * @name Database.class.php
 * @package Database
 * 
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

namespace Plugins\Database;
use \Plugins\Database\DatabaseException;
use \RuntimeException;
use \mysqli;
use \Helpers\Helpers as Helpers;
/*
 * @package Database
 * @author Sinan Eker <selsyourself@gmail.com>
 * @category SQL / MYSQLI
 * @tutorial:
 * $db = new Database();
   $result = $db->prepare(
        String("id FROM test WHERE id = ?"),
        String("value FROM test2 WHERE id = ?"),
        String("id, value FROM test2 WHERE value = ?")
    )
    ->bind(
        ["i" => 1],
        ["i" => 2],
        ["s" => "testVal123456789"]
    )
    ->execute()
    ->close()
    ->getResult(Database::FREE_MEMORY);
    var_dump($result);
 */
class Database 
{
    const FREE_MEMORY = true; // using this will free the result. Equal to Database::free()
    
    /*
     * @category Plugin name
     */
    const PLUGIN_NAME = "Database.plugin";
        
    /*
     * @internal
     */
    var $required = [
        "AUTH",
        "DATABASE",
        "ERROR_MESSAGES",
        "CHARSETS",
        "ERROR_NUMBERS"
    ];
    
    /*
     * @internal
     */
    var $ini;
    
    protected $mysqli;
    
    protected $stmt;
    
    protected $prepare = [
        "num" => 0,
        "prepare" => []
    ];
    
    protected $bind;
    
    protected $querys;
    
    protected $types = [];
    
    protected $result;
    
    /*
     * @param [void]
     * @return void
     */
    public function __construct()
    {

        if (isset($GLOBALS["PARSED_INI_FILES"][self::PLUGIN_NAME]))
        {
            $this->ini = $GLOBALS["PARSED_INI_FILES"][self::PLUGIN_NAME];
        } else {
            throw new RuntimeException("The ini files isn't loaded.");
        }
        
        $i = 0;
        while ($i < count($this->required))
        {
            if (!isset($this->ini[$this->required[$i]]))
            {
                throw new RuntimeException("Required entrie(s) in the ini files are missing. Missing: ".$this->required[$i]);
            }
            ++$i;
        }
        
        $this->mysqli = new mysqli($this->ini["AUTH"]["host"], $this->ini["AUTH"]["user"], $this->ini["AUTH"]["password"], $this->ini["DATABASE"]["db_name"], $this->ini["AUTH"]["port"]);
        
        if (!$this->mysqli)
        {
            throw new DatabaseException($this->ini["ERROR_MESSAGES"]["UNABLE_TO_CONNECT"]);
        }

        $this->mysqli->set_charset($this->ini["CHARSETS"]["mysqli_charset"]);
        
        $this->mysqli->autocommit(false);
        
        if ($this->mysqli->errno !== 0)
        {
            throw new DatabaseException(sprintf($this->ini["ERROR_MESSAGES"]["ERROR_OCCURED"], $this->mysqli->errno, $this->mysqli->error));
        }
    }

    protected static function detectFn($query)
    {
        $type = [
            "select" => "SELECT",
            "insert-into" => "INSERT INTO",
            "drop" => "DROP",
            "update" => "UPDATE",
            "delete" => "DELETE FROM",
            "alter-table" => "ALTER TABLE"
        ];
        $fn = "";
        foreach ($type as $key => $value)
        {
            if (Helpers::contains($query, $value))
            {
                $fn = $key;
            }
        }
        return $fn;
    }
    
    /*
     * @param [object String $query1, object String $query2, ...]
     * @return object Database
     */
    public function prepare()
    {
        $args = func_get_args();
        $count = count($args);
        Helpers::noArgsException($count);
        $this->prepare["num"] = $count;
        $i = 0;
        while ($i < $count)
        {
            if (!is_str_o($args[$i]))
            {
                throw new RuntimeException($this->ini["ERROR_MESSAGES"]["NOT_STR_OBJECT"]);
            }
            $args[$i] = $args[$i]->getValue();
            $this->querys = $args;
            $this->types[$i] = static::detectFn($args[$i]);
            if (!($this->prepare["prepare"][$i] = $this->mysqli->prepare($args[$i])))
            {
                if ($this->mysqli->errno === (int) $this->ini["ERROR_NUMBERS"]["SQL_SYNTAX_ERROR"])
                {
                    throw new DatabaseException(sprintf($this->ini["ERROR_MESSAGES"]["QUERY_ERROR"], $this->mysqli->error));  
                }
            }
            ++$i;
        }
        return $this;
    }
    
    /*
     * @param [array &$bind]
     * @return object Database
     * @description:
     * * Param:
     * * * ["bind_datatype" => $value]
     */
    public function bind()
    {
        $args = func_get_args();
        $count = count($args);
        Helpers::noArgsException($count);
        
        $this->bind = [];
        $i = 0;
        while ($i < $count)
        {
            if (!is_array($args[$i]))
            {
                throw new RuntimeException($this->ini["ERROR_MESSAGES"]["ARGS_MUST_BE_ARRAYS"]);
            }
            $x[$i] = Helpers::appendArray([
                0 => implode("", array_keys($args[$i])),
            ], array_values($args[$i]));
            ++$i;
        }
        
        $i = 0;
        while ($i < $this->prepare["num"])
        {
            if (!call_user_func_array([$this->prepare["prepare"][$i], "bind_param"], Helpers::referenceValues($x[$i])) )
            {
                throw new DatabaseException(sprintf($this->ini["ERROR_MESSAGES"]["BIND_ERROR"], $this->prepare["prepare"][$i]->errno, $this->prepare["prepare"][$i]->error));
            }
            ++$i;
        }
        return $this;
    }
    
    /*
     * @return object Database
     */
    public function execute()
    {
        $fetch = [];
        $i = 0;
        while ($i < $this->prepare["num"])
        {
            $this->prepare["prepare"][$i]->execute(); // important!
            if ($this->types[$i] === "select")
            {
                $fetch[$i] = $this->fetch($this->prepare["prepare"][$i]);
            } else if ($this->types[$i] === "insert-into")
            {
                $fetch[$i] = $this->prepare["prepare"][$i]->affected_rows;
            }
            $this->prepare["prepare"][$i]->close();
            ++$i;
        }
        $this->result = $fetch;
        return $this;
    }
    
    /*
     * @param [mysqli_stmt $stmt]
     * @return array $results
     */
    public function fetch(\mysqli_stmt $stmt)
    {
        $array = [];
        $fieldNames = [];
        $result = $stmt->get_result();
        foreach ((array) $stmt->result_metadata()->fetch_fields() as $key => $value)
        {
            foreach ($value as $key2 => $value2)
            {
                if ($key2 === "name")
                {
                    $fieldNames[] = $value2;
                }   
            }
        }
        $i = 0;
        while ($i < count($fieldNames))
        {
            while ($row = $result->fetch_array(MYSQLI_NUM))
            {
                $array[$fieldNames[$i]] = $row;
            }
            ++$i;
        }
        $stmt->free_result();
        return $array;
    }
    
    /*
     * @param [bool $free = false] Auto frees the result. Equal to Database::free(); @see const Database::FREE_MEMORY
     * @return array Database::$result
     */
    public function getResult($free = false)
    {
        if ($free === self::FREE_MEMORY)
        {
            $result = $this->result;
            $this->free();
            return $result;
        } else {
            return $this->result;
        }
    }
    
    /*
     * @return object Database
     */
    public function free()
    {
        $this->result = null;
        return $this;
    }
    
    public function next()
    {
        $this->stmt = null;
        $this->prepare = [
            "num" => 0,
            "prepare" => []
        ];
        $this->bind = null;
        $this->querys = null;
        $this->types = [];
        return $this->free();
    }
    
    /*
     * @return object Database
     */
    public function close()
    {
        $this->mysqli->close();
        return $this;
    }
    
    /*
     * @return mysqli Database::$mysqli
     */
    public function getMysqli()
    {
        return $this->mysqli;
    }

    /*
     * @tutorial
     * $db = (new Database)->killThread();
     * $db->getMysqli()->query("SELECT * FROM ..."); // will produce the error: MySQL server has gone away
     */
    public function killThread()
    {
        $this->mysqli->kill($this->mysqli->thread_id);
    }
}