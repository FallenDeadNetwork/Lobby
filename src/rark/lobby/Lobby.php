<?php
declare(strict_types = 1);

namespace rark\lobby;

use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\World;

class Lobby{
	const CONF_LOBBY_WORLD_NAME = 'lobby_world.folder_name';
	const CONF_ALLOW_PVP = 'allow_pvp';
	const CONF_SPAWN = 'spawn';
	const CONF_EFFECTS = 'effects';
	const CONF_CANCEL_EXHAUST = 'cancel_exhaust';
	const CONF_CANCEL_KILL = 'cancel_kill';
	const CONF_CANCEL_DROP = 'cancel_drop';

	protected static ?self $instance = null;
	protected World $level;
	protected bool $allow_pvp;
	protected Vector3 $spawn;
	protected bool $cancel_exhaust;
	protected bool $cancel_kill;
	protected bool $cancel_drop;

	public function __construct(Config $conf){
		if(self::$instance !== null) throw new \RuntimeException('another instance is already created');
		$this->level = $this->checkLevel($conf->get(self::CONF_LOBBY_WORLD_NAME, null));
		$this->allow_pvp = $this->checkBool($conf->get(self::CONF_LOBBY_WORLD_NAME, false));
		$this->spawn = $this->checkSpawn((array) $conf->get(self::CONF_SPAWN, []));
		$this->cancel_exhaust = $this->checkBool($conf->get(self::CONF_CANCEL_EXHAUST, true));
		$this->cancel_kill = $this->checkBool($conf->get(self::CONF_CANCEL_KILL, true));
		$this->cancel_drop = $this->checkBool($conf->get(self::CONF_CANCEL_DROP, true));
		self::$instance = $this;
	}

	protected function checkLevel(mixed $level_name):World{
		if($level_name === null) throw new KeyNotFoundException(self::CONF_LOBBY_WORLD_NAME);
		$level = Server::getInstance()->getWorldManager()->getWorldByName((string) $level_name);

		if($level === null) throw new \ErrorException('world name "'.$level_name.'" was not found');
		return $level;
	}

	protected function checkBool(mixed $bool):bool{
		$boolval = is_string($bool)? filter_var($bool, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE): (bool) $bool;
		return $boolval === null? false: $boolval;
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

	public static function isLobby(?World $level):bool{
		if($level === null or self::$instance === null) return false;
		return $level->getId() === self::$instance->getLevel()->getId();
	}

	public function getLevel():World{
		return $this->level;
	}

	public function isAllowedPvP():bool{
		return $this->allow_pvp;
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

	public function isCancelledDorpItem():bool{
		return $this->cancel_drop;
	}
}