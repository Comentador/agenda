<?php


spl_autoload_register(function($class){

	if(stripos($class, "Controller") > -1){


		if(file_exists("Controllers/".$class.".php")){

			include "Controllers/".$class.".php";
		}

	}elseif(file_exists("Models/".$class.".php")){

		include "Models/".$class.".php";
		
	}else{

		include "Core/".$class.".php";
	}


});

$run = new Core();
$run->run();
?>