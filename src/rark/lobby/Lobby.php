<?php
declare(strict_types = 1);

namespace rark\lobby;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\Config;

function is_true(mixed$val){
    $boolval = is_string($val)? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE): (bool) $val;
    return $boolval === null? false: $boolval;
}

class Lobby{

	const CONF_LOBBY_WORLD_NAME = 'lobby_world.folder_name';
	const CONF_ALLOW_PVP = 'allow_pvp';
	const CONF_GAMEMODE = 'gamemode';
	const CONF_SPAWN = 'spawn';

	protected static ?self $instance = null;
	protected static Level $level;
	protected static bool $allow_pvp;
	protected static int $gamemode;
	protected static Vector3 $spawn;

	public function __construct(Config $conf){
		if(self::$instance !== null) throw new \RuntimeException('another instance is already created');
		self::$level = $this->checkLevel((string) $conf->get(self::CONF_LOBBY_WORLD_NAME, null));
		self::$allow_pvp = $this->checkAllowPvP($conf->get(self::CONF_LOBBY_WORLD_NAME, false));
		self::$gamemode = $this->checkGamemode((int) $conf->get(self::CONF_GAMEMODE, null));
		self::$spawn = $this->checkSpawn((array) $conf->get(self::CONF_SPAWN));
		self::$instance = $this;
	}

	protected function checkLevel(?string $level_name):Level{
		if($level_name === null) throw new KeyNotFoundException(self::CONF_LOBBY_WORLD_NAME);
		$level = Server::getInstance()->getLevelByName($level_name);

		if($level === null) throw new \ErrorException('world name "'.$level_name.'" was not found');
		return $level;
	}

	protected function checkAllowPvP(mixed $allow_pvp):bool{
		$boolval = is_string($allow_pvp)? filter_var($allow_pvp, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE): (bool) $allow_pvp;
		return $boolval === null? false: $boolval;
	}

	protected function checkGamemode(?int $gamemode):int{
		if($gamemode === null) throw new KeyNotFoundException(self::CONF_GAMEMODE);
		if($gamemode < 0 or $gamemode > 3) throw new \ErrorException($gamemode.' is not a valid game mode');
		return $gamemode;
	}

	protected function checkSpawn(?array $spawn):Vector3{
		if($spawn === null) throw new KeyNotFoundException(self::CONF_SPAWN);
		if(count($spawn) !== 3) throw new \ErrorException('');
		$spawn = array_filter(array_values($spawn));
		return new Vector3(floor((float) $spawn[0]), floor((float) $spawn[1]), floor((float) $spawn[2]));
	}

	public static function getInstance():?self{
		return self::$instance;
	}

	public function getLevel():Level{
		return $this->level;
	}

	public function isAllowedPvP():bool{
		return $this->allow_pvp;
	}

	public function getGamemode():int{
		return $this->gamemode;
	}

	public function getSpawn():Vector3{
		return $this->spawn;
	}
}