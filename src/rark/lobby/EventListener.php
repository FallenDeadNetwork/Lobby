<?php
declare(strict_types = 1);

namespace rark\lobby;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\level\Position;
use pocketmine\Player;

class EventListener implements Listener{

	protected Lobby $lobby;

	public function __construct(Lobby $lobby){
		$this->lobby = $lobby;
	}

	public function onJoin(PlayerJoinEvent $ev):void{
		$player = $ev->getPlayer();
		$spawn = $this->lobby->getSpawn();
		$pos = new Position($spawn->x, $spawn->y, $spawn->z, $this->lobby->getLevel());
		$player->teleport($pos);
		$player->setSpawn($pos);

		if(!$player->isOp()) $player->setGamemode($this->lobby->getGamemode());
	}

	public function onExhaust(PlayerExhaustEvent $ev):void{
		if(!Lobby::isLobby($ev->getPlayer()->getLevel())) return;
		if(!$this->lobby->isCancelledExhaust()) return;
		$ev->setCancelled();
	}

	public function onDamage(EntityDamageEvent $ev):void{
		$player = $ev->getEntity();

		if(!$player instanceof Player) return;
		if(!Lobby::isLobby($player->getLevel())) return;
		if($ev instanceof EntityDamageByEntityEvent){
			if($ev->getDamager() instanceof Player and $this->lobby->isAllowedPvP()) return;
			$ev->setCancelled();
		}
	}

	public function onDeath(PlayerDeathEvent $ev):void{
		if($ev->getPlayer()->getLevel()?->getName() !== $this->lobby->getLevel()->getName()) return;
		$ev->setDrops([]);
	}

	public function onCommand(CommandEvent $ev):void{
		$sender = $ev->getSender();
		
		if(!$sender instanceof Player) return;
		if($ev->getCommand() === 'kill' and Lobby::isLobby($sender->getLevel())){
			if($this->lobby->isCancelledKillCommand()) $ev->setCancelled();
		}
	}
}