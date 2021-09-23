<?php
declare(strict_types = 1);

namespace rark\lobby;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase{

	public function onEnable(){
		new Lobby(
			new Config(
				$this->getDataFolder().'config.conf',
				Config::YAML,
				[
					Lobby::CONF_LOBBY_WORLD_NAME => 'world',
					Lobby::CONF_ALLOW_PVP => false,
					Lobby::CONF_GAMEMODE => Player::ADVENTURE,
					Lobby::CONF_SPAWN => [
						0,
						70,
						0
					]
				]
			)
		);
	}
}