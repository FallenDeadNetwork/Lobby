<?php
declare(strict_types = 1);

namespace rark\lobby;

use pocketmine\utils\Config;

class Lobby{

	const CONF_LOBBY_WORLD_NAME = 'lobby_world.folder_name';
	const CONF_ALLOW_PVP = 'allow_pvp';
	const CONF_GAMEMODE = 'gamemode';
	const CONF_SPAWN = 'spawn';

	protected static ?self $instance = null;

	public function __construct(Config $conf){
		if(self::$instance !== null) throw new \RuntimeException('another instance is already created');
		self::$instance = $this;
	}
}