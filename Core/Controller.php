<?php

class Controller{

	protected $token;
	protected $api_url;
	protected $webhook;

	private function apiRequest($metodo, $parametros, $id=false){

		$ch = curl_init();


		if($metodo === 'sendPhoto' || $metodo == "sendDocument"){

			if(stristr($parametros["photo"], "https://") == false || stristr($parametros["document"], "https://") == false){

				if($id){
					$params = http_build_query($parametros);
				}else{

					if($metodo == "sendPhoto"){

						$filepath = realpath($parametros["photo"]);
						$plano = "photo";

					}else{

						$filepath = realpath($parametros["document"]);
						$plano = "document";
					}

					$params = array(
						"chat_id"=>$parametros["chat_id"],
						$plano=>new CurlFile($filepath),
						"caption"=>$parametros["caption"],
					);

				}
			
			}else{

				$params = http_build_query($parametros);
			}

		}else{

			$params = http_build_query($parametros);
		}

	
		
		$options = [
			CURLOPT_URL=>$this->api_url.$metodo."?",
			CURLOPT_POST=>1,
			CURLOPT_POSTFIELDS=>$params,
			CURLOPT_SSL_VERIFYPEER=>false,
			CURLOPT_SSL_VERIFYHOST=>false,
			CURLOPT_RETURNTRANSFER=>true,
			CURLOPT_HTTPHEADER=>array(
				"Content-Type"=>"multipart/form-data"
			),
		];

		curl_setopt_array($ch, $options);
		$response = curl_exec($ch);
		$info = curl_getinfo($ch);

		return $response;
	}

	public function sendMessage($opc, $text, $button = false){

		if($button != false){

			$parametros = [
				"chat_id"=>$opc["chat_id"],
				"text"=>$text,
				"parse_mode"=>"Markdown",
				"disable_web_page_preview"=>true,
				"reply_markup"=>json_encode($button)
			];
		}else{

			$parametros = [
				"chat_id"=>$opc["chat_id"],
				"text"=>$text,
				"parse_mode"=>"Markdown",
				"disable_web_page_preview"=>true,
			];

		}

		return $this->apiRequest("sendMessage", $parametros);
	}

	public function editMessage($opc, $text, $button=null){

		if($button != null){

			$parametros = [
				"chat_id"=>$opc["chat_id"],
				"text"=>$text,
				"parse_mode"=>"Markdown",
				"disable_web_page_preview"=>true,
				"reply_markup"=>json_encode($button),
				"message_id"=>$opc["message_id"],
			];
		}else{

			$parametros = [
				"chat_id"=>$opc["chat_id"],
				"text"=>$text,
				"parse_mode"=>"Markdown",
				"disable_web_page_preview"=>true,
				"message_id"=>$opc["message_id"],
			];

		}

		return $this->apiRequest("editMessageText", $parametros);
	}

	public function deleteMessage($opc){

		$parametros = [
			"chat_id"=>$opc['chat_id'],
			"message_id"=>$opc["message_id"],
		];

		return $this->apiRequest("deleteMessage", $parametros);
	}

	public function editReplymarkup($opc, $button){

		$parametros = [
			"chat_id"=>$opc["chat_id"],
			"message_id"=>intval($opc["message_id"]),
			"reply_markup"=>json_encode($button),
		];

		return $this->apiRequest("editMessageReplyMarkup", $parametros);
	}

	public function sendChatAction($opc, $action){

		$parametros = [
			"chat_id"=>$opc["chat_id"],
			"action"=>$action,
		];

		return $this->apiRequest("sendChatAction", $parametros);
	}

	public function sendMediaGroup($opc, $files, $caption){

		$ficheiros = [];
		foreach($files as $file){
			$ficheiros[] = array("type"=>"photo", "media"=>$file); 
		}

		$ficheiros[0]["caption"] = $caption;
		$ficheiros[0]["parse_mode"] = "Markdown";
		$parametros = [
			"chat_id"=>$opc["chat_id"],
			"media"=>json_encode($ficheiros),
		];

		return $this->apiRequest("sendMediaGroup", $parametros);
	}


	public function sendPhoto($opc, $photo, $caption = null, $buttons=false, $id=false){


		$parametros = [
			"chat_id"=>$opc["chat_id"],
			"photo"=>$photo,
			"parse_mode"=>"Markdown",


		];

		if(empty($parametros["photo"])){

			$parametros["photo"] = $photo;
		}

		if($caption != null){

			$parametros["caption"] = $caption;
		}

		if($buttons != false){
			$parametros["reply_markup"] = json_encode($buttons);
		}

		return $this->apiRequest("sendPhoto", $parametros, $id);
	}

	protected function answerCallbackQuery($cb_id, $alert, $cache, $text){

		$parametros = [
			"callback_query_id"=>$cb_id,
			"text"=>$text,
			"show_alert"=>$alert,
			"cache_time"=>$cache
		];

		return $this->apiRequest("answerCallbackQuery", $parametros);
	}

	public function save($name, $content){

		file_put_contents("assets/".md5($name).".txt", $content);
	}

	public function get_saved($name){

		return file_get_contents("assets/".md5($name).".txt");
	}
	
	public function delete_file($name){

		unlink("assets/".md5($name).".txt");
	}


	public function characters($string){


		$string = str_replace("_", "\_", $string);
		//$string = str_replace("[", "\[", $string);
		//$string = str_replace("]", "\]", $string);
		//$string = str_replace("`", "\`", $string);
		//$string = str_replace("*", "\*", $string);
		return $string;
	}


	public function editCaption($opc, $caption, $buttons = false){

		$parametros = [
			"chat_id"=> $opc["chat_id"],
			"message_id"=>$opc["message_id"],
			"caption"=>$caption,
			"parse_mode"=>"Markdown",
		];

		if($buttons){

			$parametros["reply_markup"] = json_encode($buttons);
		}

		$this->apiRequest("editMessageCaption", $parametros);
	}

	public function editMessageMedia($opc, $media, $buttons = false){

		$parametros = [

			"chat_id"=>$opc["chat_id"],
			"message_id"=>$opc["message_id"],
			"media"=>json_encode($media),

		];

		if($buttons){

			$parametros["reply_markup"] = json_encode($buttons);
		}

		$this->apiRequest("editMessageMedia", $parametros);
	}


	public function sendDocument($opc, $document, $buttons = false){

		$parametros = [

			"chat_id"=>$opc["chat_id"],
			"document"=>$document,

		];

		if($buttons){

			$parametros["reply_markup"] = json_encode($buttons);
		}

		$this->apiRequest("sendDocument", $parametros);
	}


	
}

?>