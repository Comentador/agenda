<?php


class tugaController extends Controller{

	public $token;
	public $api_url;
	public $agenda;

	public function __construct(){

		$this->token = "1368095051:AAHbDL4Avt2ELUG6SBWFNVwbM0jPicSskSk";
		$this->api_url = "https://api.telegram.org/bot".$this->token."/";
		$this->agenda = new Agenda();
	}


	public function index(){

		$update = json_decode(file_get_contents("php://input"), true);
		$message = $update["message"];

		if(isset($update["callback_query"])){

			$this->callback($update["callback_query"]);
			return;
		}

		$opc = [

			"chat_id"=>$message["chat"]["id"],
			"message_id"=>$message["message_id"],
			"user_id"=>$message["from"]["id"],
			"texto"=>$message["text"],
			"username"=>$message["from"]["username"],
			"first_name"=>$message["from"]["first_name"],
			"chat_type"=>$message["chat"]["type"]
		];


		if($opc["chat_id"] == ""){

			if($opc["chat_type"] == "group"){

				if($opc["texto"] == "/start" || $opc["texto"] == "/start@AgentedeBot"){

					//Opcoes para inserir grupo na base de dados;
					$this->sendMessage($opc, "Grupo adicionado ao Bot com sucesso, pode configurar as opções do grupo no privado");
				}

			}else{


				if($opc["texto"] == "/start" || $opc["texto"] == "/start@AgentedeBot"){

					$group = json_decode($this->sendMessage($opc, Strings::$texto["principal"], Strings::$buttons["principal"]), true);

					$this->agenda->delete_message($opc["user_id"]);
					$this->agenda->insert_message($opc["user_id"], $group["result"]["message_id"]);
					$this->agenda->delete_now($opc["user_id"]);

					return;

				}else{

					$now = $this->agenda->get_now($opc["user_id"]);
					$message_id = $this->agenda->get_message($opc["user_id"]);
					$this->deleteMessage($opc);
					$opc["message_id"] = $message_id;


					if(is_array($now)){

						if($now["now"] == "novo"){


							$this->agenda->add_agenda($opc["texto"]);
							$this->agenda->update_now($opc["user_id"], "data");
							$this->editMessage($opc, Strings::$texto["foto"], Strings::$buttons["confirm"]);

							$some = $this->agenda->get_id("mensagem", $opc["texto"]);
							$this->agenda->update_now($opc["user_id"], $some["id"]);

						}elseif($now["now"] == "data"){

							$now = $this->agenda->get_now($opc["user_id"]);
							$sep = explode("/", $opc["texto"]);

							if(count($sep) != 3){

								$this->editMessage($opc, Strings::$texto["data_fail"], Strings::$buttons["cancelar"]);
								return;

							}else{

								$this->agenda->update_agenda($opc["texto"], "data", $now["agenda_id"]);
								$this->agenda->update_now($opc["user_id"], "hora");
								$this->editMessage($opc, Strings::$texto["hora"], Strings::$buttons["cancelar"]);
								return;
							}

						}elseif($now["now"] == "hora"){
							
							$now = $this->agenda->get_now($opc["user_id"]);
							$explode = explode(":", $opc["texto"]);

							if(count($explode) != 2){

								$this->editMessage($opc, Strings::$texto["hora_fail"], Strings::$buttons["cancelar"]);
								return;

							}else{


								$agendei = $this->agenda->get_id("id", $now["agenda_id"]);

								$data = $agendei["data"];
								$data = str_replace("/", "-", $data);

								$date = new DateTime($data);
								$seconds = $date->getTimestamp();

								$hours = intval($explode[0]) * 3600;
								$minutes = intval($explode[1]) * 60;

								$total = $seconds + $hours + $minutes;
								$this->agenda->update_agenda($opc["texto"], "hora", $now["agenda_id"]);
								$this->agenda->update_agenda($total, "time", $now["agenda_id"]);
								$this->agenda->delete_now($opc["user_id"]);
								$this->editMessage($opc, "mensagem temporaria para avisar que o agendamento foi concluido com sucesso");
								$this->sendPhoto($opc, $agendei["foto"], "nada para ver aqui", false, true);
							}

						}elseif($now["now"] == "foto"){

							if(isset($message["photo"])){

								if(isset($message["photo"][2])){

									$file_id = $message["photo"][2];
								}else{

									$file_id = $message["photo"][1];
								}

								$this->agenda->update_agenda($file_id, "foto", $now["agenda_id"]);
								$this->agenda->update_now($opc["user_id"], "data");
								$this->sendMessage($opc, Strings::$texto["data"], Strings::$buttons["cancelar"]);

							}else{

								$this->deleteMessage($opc);
								$this->sendMessage($opc, "Por favor envie uma foto, não outra coisa", Strings::$buttons["cancelar"]);
							}
						}
					}
				}


			}
		}

	}

	public function callback($callback){

		$cb_id = $callback["id"];
		$cb_data = $callback["data"];

		$opc = [

			"chat_id"=>$callback["message"]["chat"]["id"],
			"message_id"=>$callback["message"]["message_id"],
			"user_id"=>$callback["from"]["id"],
			"username"=>$callback["from"]["username"],
			"first_name"=>$callback["from"]["first_name"],
			"texto"=>$callback["message"]["text"],
			"chat_type"=>$callback["message"]["chat"]["type"],
		];

		switch($cb_data){

			case "criar":

				$now = $this->agenda->get_now($opc["user_id"]);

				if(is_array($now)){

					$this->agenda->update_now($opc["user_id"], "novo");
				}else{

					$this->agenda->insert_now($opc["user_id"], "novo");
				}

				$this->editMessage($opc, Strings::$texto["data"], Strings::$buttons["cancelar"]);
				break;

			case "cancel":

				$this->agenda->delete_now($opc["user_id"]);

				if($opc["chat_type"] == "private"){


				}else{

					$this->editMessage($opc, Strings::$texto["principal"], Strings::$buttons["principal"]);
				}
				break;

			case "yes":

				$this->agenda->update_now($opc["user_id"], "foto");
				$this->editMessage($opc, Strings::$texto["foto"], Strings::$buttons["cancelar"]);
				break;

			case "no":	

				$this->agenda->update_now($opc["user_id"], "data");
				$this->editMessage($opc, Strings::$texto, Strings::$buttons["cancelar"]);
				break;
		}

	}

	public function testando(){

		echo "estou vivo";
	}
}



?>