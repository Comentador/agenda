<?php

class Agenda extends Model{



	public function add_agenda($message){

		$sql = $this->pdo->prepare("INSERT INTO agenda SET mensagem=:group");
		$sql->bindValue(":group", $message);
		$sql->execute();
	}

	public function get_agenda(){

		$sql = $this->pdo->prepare("SELECT * FROM agenda");
		$sql->execute();

		if($sql->rowCount() > 0){

			$array = $sql->fetchAll();
			return $array;
		}

		return false;
	}

	public function delete_agenda($id){

		$sql = $ths->pdo->prepare("DELETE FROM agenda WHERE id=:id");
		$sql->bindValue(":id", $id);
		$sql->execute();
	}

	public function update_agenda($info, $type, $where){


		$sql = $his->pdo->prepare("UPDATE agenda SET $type=:type WHERE id=:whe");
		$sql->bindValue(":type", $type);
		$sql->bindValue(":whe", $where);
		$sql->execute();
	}

	public function insert_now($user_id, $now){

		$sql = $this->pdo->prepare("INSERT INTO now SET user_id=:user, now=:no");
		$sql->bindValue(":user", $user_id);
		$sql->bindValue(":no", $now);
		
		$sql->execute();
	}

	public function update_now($user_id, $now){

		$sql = $his->pdo->prepare("UPDATE now SET now=:no WHERE user_id=:usr");
		$sql->bindValue(":usr", $user_id);
		$sql->bindValue(":usr", $now);
		$sql->execute();
	}

	public function get_now($user_id){

		$sql = $this->pdo->prepare("SELECT * FROM now WHERE user_id=:user");
		$sql->bindValue(":user", $user_id);
		$sql->execute();

		if($sql->rowCount() > 0){

			$array = $sql->fetch();
			return $array;
		}

		return false;
	}

	public function delete_now($user_id){

		$sql = $ths->pdo->prepare("DELETE FROM now WHERE user_id=:id");
		$sql->bindValue(":id", $user_id);
		$sql->execute();
	}

	public function insert_message($user_id, $id){

		$sql = $this->pdo->prepare("INSERT INTO message SET user_id=:user, message_id=:no");
		$sql->bindValue(":user", $user_id);
		$sql->bindValue(":no", $id);
		
		$sql->execute();
	}

	public function delete_message($user_id){

		$sql = $ths->pdo->prepare("DELETE FROM message WHERE user_id=:id");
		$sql->bindValue(":id", $user_id);
		$sql->execute();
	}

	public function get_message($user_id){

		$sql = $this->pdo->prepare("SELECT * FROM message WHERE user_id=:user");
		$sql->bindValue(":user", $user_id);
		$sql->execute();

		if($sql->rowCount() > 0){

			$array = $sql->fetch();
			return $array["message_id"];
		}

		return false;
	}

	public function get_id($field, $value){

		$sql = $this->pdo->prepare("SELECT * FROM agenda WHERE $field =:val");
		$sql->bindValue(":val", $value);
		$sql->execute();

		if($sql->rowCount() > 0){

			$array = $sql->fetch();
			return $array;
		}

		return false;
	}

	
}

class Strings{

	public static $texto = [

		//Private


		//public

		"principal"=>"Opções de agendamentos para este grupo",
		"data"=>"Introduza a data neste formato: `dia/mês/ano`\n*Exemplo:* 20/10/2020",
		"hora"=>"Introduza a hora neste formato: `hor:minutos`\n*Exemplo:* 17:30\n\n*Nota:*Tem de ser no formato 24 horas",
		"mensagem"=>"Introduza a mensagem",
		"foto"=>"Introduza a foto que deseja que seja enviada junto com a mensagem",
		"data_fail"=>"Não Introduziu a data no formato correcto, por favor introduza no seguinte formato: `dia/mês/ano`",
		"hora_fail"=>"Não introduziu a hora no formato correcto, por favor introduza no seguinte formato: `hora:minutos`",

	];

	public static $buttons = [

		"principal"=>[
			"inline_keyboard"=>[

				[["text"=>"Ver", "callback_data"=>"see"]],
				[["text"=>"Criar", "callback_data"=>"create"]],
				[["text"=>"Apagar", "callback_data"=>"delete"]]
			]
		],


		"editar"=>[
			"inline_keyboard"=>[

				[["text"=>"Hora", "callback_data"=>"see"], ["text"=>"Dia", "callback_data"=>"day"]],
				[["text"=>"Mensagem", "callback_data"=>"message"]],
				//no botao a baixo vamos ter o inicio ou paragem do bot dependendo do estado do grupo em questao
				[["text"=>"Voltar", "callback_data"=>"back"]]
			]
		],

		"confirm"=>[
			"inline_keyboard"=>[

				[["text"=>"Sim", "callback_data"=>"yes"]],
				[["text"=>"Não", "callback_data"=>"no"]],
			]
		],

		"cancelar"=>[
			"inline_keyboard"=>[

				[["text"=>"Cancelar", "callback_data"=>"cancel"]],
				
			]
		],

	];
}

?>