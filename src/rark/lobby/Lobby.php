<?php
declare(strict_types = 1);

namespace rark\lobby;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\Config;

class Lobby{
	const CONF_LOBBY_WORLD_NAME = 'lobby_world.folder_name';
	const CONF_ALLOW_PVP = 'allow_pvp';
	const CONF_GAMEMODE = 'gamemode';
	const CONF_SPAWN = 'spawn';
	const CONF_EFFECTS = 'effects';
	const CONF_CANCEL_EXHAUST = 'cancel_exhaust';
	const CONF_CANCEL_KILL = 'cancel_kill';

	protected static ?self $instance = null;
	protected Level $level;
	protected bool $allow_pvp;
	protected int $gamemode;
	protected Vector3 $spawn;
	protected bool $cancel_exhaust;
	protected bool $cancel_kill;

	public function __construct(Config $conf){
		if(self::$instance !== null) throw new \RuntimeException('another instance is already created');
		$this->level = $this->checkLevel($conf->get(self::CONF_LOBBY_WORLD_NAME, null));
		$this->allow_pvp = $this->checkBool($conf->get(self::CONF_LOBBY_WORLD_NAME, false));
		$this->gamemode = $this->checkGamemode($conf->get(self::CONF_GAMEMODE, null));
		$this->spawn = $this->checkSpawn((array) $conf->get(self::CONF_SPAWN, []));
		$this->cancel_exhaust = $this->checkBool($conf->get(self::CONF_CANCEL_EXHAUST, true));
		$this->cancel_kill = $this->checkBool($conf->get(self::CONF_CANCEL_KILL, true));
		self::$instance = $this;
	}

	protected function checkLevel(mixed $level_name):Level{
		if($level_name === null) throw new KeyNotFoundException(self::CONF_LOBBY_WORLD_NAME);
		$level = Server::getInstance()->getLevelByName((string) $level_name);

		if($level === null) throw new \ErrorException('world name "'.$level_name.'" was not found');
		return $level;
	}

	protected function checkBool(mixed $bool):bool{
		$boolval = is_string($bool)? filter_var($bool, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE): (bool) $bool;
		return $boolval === null? false: $boolval;
	}

	protected function checkGamemode(mixed $gamemode):int{
		if($gamemode === null) throw new KeyNotFoundException(self::CONF_GAMEMODE);
		if((int) $gamemode < 0 or (int) $gamemode > 3) throw new \ErrorException($gamemode.' is not a valid game mode');
		return $gamemode;
	}

	protected function checkSpawn(array $spawn):Vector3{
		if(count($spawn) === 0) throw new KeyNotFoundException(self::CONF_SPAWN);
		if(count($spawn) !== 3) throw new \ErrorException('please enter only 3 numbers in the key '.self::CONF_SPAWN);
		$spawn = array_filter(array_values($spawn));
		return new Vector3(floor((float) $spawn[0]), floor((float) $spawn[1]), floor((float) $spawn[2]));
	}

	public static function getInstance():?self{
		return self::$instance;
	}

	public static function isLobby(?Level $level):bool{
		if($level === null or self::$instance === null) return false;
		return $level->getId() === self::$instance->getLevel()->getId();
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

	public function isCancelledExhaust():bool{
		return $this->cancel_exhaust;
	}

	public function isCancelledKillCommand():bool{
		return $this->cancel_kill;
	}
}