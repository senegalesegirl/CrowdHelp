<?php
require APP . "/libraries/notorm/NotORM.php";
class DB
{
    private static $db;

	private static $dsn = "mysql:host=localhost;dbname=crowdhelp";
	private static $usr = "root";
	private static $pwd = "root";

    public static function getInstance()
    {
    	if (!self::$db){
			$pdo = new PDO(self::$dsn, self::$usr, self::$pwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
            
            $structure = new NotORM_Structure_Convention(
                $primary = "id", // id_$table
                $foreign = "%s_id", // id_$table
                $table = "%s", // {$table}s
                $prefix = "" // wp_$table
            );

			self::$db = new NotORM($pdo);
    	}

    	return self::$db;
    }
}

?>