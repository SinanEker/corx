<?php
namespace Plugins\Database;
use \Plugins\Database\DatabaseException;
use \RuntimeException;
use \mysqli;
use \Helpers\Helpers as Helpers;
class Database 
{
    const PLUGIN_NAME = "Database.plugin";
    
    var $required = [
        "AUTH",
        "DATABASE",
        "ERROR_MESSAGES",
        "CHARSETS"
    ];
    
    var $ini;
    
    protected $mysqli;
    
    protected $stmt;
    
    protected $prepare = [
        "num" => 0,
        "prepare" => []
    ];
    
    protected $bind;
    
    protected $querys;
    
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
        
        if ($this->mysqli->errno !== 0)
        {
            throw new DatabaseException(sprintf($this->ini["ERROR_MESSAGES"]["ERROR_OCCURED"], $this->mysqli->errno, $this->mysqli->error));
        }
    }

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
            if (!Helpers::contains($args[$i], "SELECT"))
            {
                $args[$i] = "SELECT ".$args[$i];
            }
            $this->querys = $args;
            $this->prepare["prepare"][$i] = $this->mysqli->prepare($args[$i]);
            ++$i;
        }
        return $this;
    }
    
    /*
     * @param [array &$bind]
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
    
    public function execute()
    {
        $fetch = [];
        $i = 0;
        while ($i < $this->prepare["num"])
        {
            $fetch[$i] = $this->fetch($this->prepare["prepare"][$i]);
            $this->prepare["prepare"][$i]->close();
            ++$i;
        }
        return $fetch;
    }
    
    public function fetch(\mysqli_stmt $stmt)
    {
        $vars = [];
        $data = [];
        foreach (Helpers::referenceValues((array) $stmt->result_metadata()->fetch_field()) as $key => $value)
        {
            if ($key === "name")
            {
                $vars[] = $value;
            }
        }
        return call_user_func_array([$stmt, "bind_result"], $vars);
        /*
        $stmt->store_result();
        
        $vars = [];
        $data = [];
        $meta = $stmt->result_metadata();
        
        while ($field = $meta->fetch_field())
        {
            $vars[] = &$data[$field->name];
        }
        call_user_func_array([$stmt, "bind_result"], $vars);
        
        $i = 0;
        while ($stmt->fetch())
        {
            $array[$i] = [];
            foreach ($data as $key => $value)
            {
                $array[$i][$key] = $value;
            }
            $i++;
        }
        */
    }
    
    public function getMysqli()
    {
        return $this->mysqli;
    }
}