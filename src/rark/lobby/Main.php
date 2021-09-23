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
					'lobby_world.folder_name' => 'world',
					'allow_pvp' => false,
					'gamemode' => Player::ADVENTURE,
					'spawn' => [
						0,
						70,
						0
					]
				]
			)
		);
	}
}