<?php


namespace pocketmine\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class PingCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"Получить свой пинг",
			"/ping"
		);
		$this->setPermission("pocketmine.command.ping");
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		

		if(!($sender instanceof Player)){
			return true;
		}		
		$sender->sendPing();
		return true;
	}
}
