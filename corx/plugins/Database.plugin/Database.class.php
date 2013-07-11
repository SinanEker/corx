<?php

class Database 
{ 
    
    const PLUGIN_NAME = "Database.plugin";
    
    private $required = [
        "AUTH",
        "DATABASE",
        "ERROR_MESSAGES",
        "CHARSETS"
    ];
    
    protected $ini;
    
    public function __construct()
    {
        
        if (isset($GLOBALS["PARSED_INI_FILES"][self::PLUGIN_NAME]))
        {
            $this->ini = $GLOBALS["PARSED_INI_FILES"][self::PLUGIN_NAME];
        } else {
            throw new RuntimeException("The ini files isn't loaded.");
        }
        
        var_dump($this->ini);
        $i = 0;
        while ($i < count($this->required))
        {
            if (!isset($this->ini[$this->required[$i]]))
            {
                throw new RuntimeException("Required entrie(s) in the ini files are missing. Missing: ".$this->required[$i]);
            }
            ++$i;
        }
        
        /*
        if($port === nil)
        {
            $port = ini_get('mysqli.default_port');
        }
        */
        #$this->mysqli = new mysqli($host, $username, $password, $db, $port);
        #if (!$this->mysqli)
        #{
        #    throw new DatabaseException("Unable to connect to mysql server. ");
        #}

        #$this->mysqli->set_charset("MYSQLI_CHARSET");

        #self::$instance = $this;
    }
}