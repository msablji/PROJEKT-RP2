<?php
session_start();
require_once 'index.php';



// Klasa za pristup bazi (singleton)
class DB
{
	private static $db = null;

	final private function __construct() {}
	final private function __clone() {}

	public static function getConnection()
	{
		// Ako još nismo spajali na bazu, uspostavi konekciju.
		if( DB::$db === null )
		{

			try
			{
				DB::$db = new PDO('mysql:host=rp2.studenti.math.hr; dbname=martinek; charset=utf8', 'student', 'pass.mysql');
				// Ako želimo da funkcije poput prepare, execute bacaju exceptione:
				DB::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    	DB::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			}
			catch( PDOException $e ) { exit( 'Greška prilikom spajanja na bazu:' . $e->getMessage() ); }
		}

		// Vrati novostvorenu ili od ranije postojeću konekciju.
		return DB::$db;
	}
};



?>
