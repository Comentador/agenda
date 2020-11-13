<?php


class Model{


	public $pdo;
	const DB = ["user"=>"comentador", "pass"=>"humdados123456", "host"=>"db4free.net", "name"=>"sendtomyemail"];

	public function __construct(){


		try{

			$this->pdo = new PDO("mysql:dbname=".self::DB["name"].";host=".self::DB['host'], self::DB["user"], self::DB["pass"]);

		}catch(exception $e){

			echo $e->getMessage();
		}
	}

}


?>