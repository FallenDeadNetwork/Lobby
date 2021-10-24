<?php
declare(strict_types = 1);

namespace rark\lobby;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\GameMode;
use pocketmine\world\Position;
use pocketmine\player\Player;

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
		$player->setGamemode(GameMode::ADVENTURE());
	}

	public function onExhaust(PlayerExhaustEvent $ev):void{
		if(!Lobby::isLobby($ev->getPlayer()->getWorld())) return;
		if(!$this->lobby->isCancelledExhaust()) return;
		$ev->cancel();
	}

	public function onDamage(EntityDamageEvent $ev):void{
		$player = $ev->getEntity();

		if(!$player instanceof Player) return;
		if(!Lobby::isLobby($player->getWorld())) return;
		if($player->getHealth()-$ev->getFinalDamage() < 1){
			$ev->cancel();
			$player->setHealth(20.0);
			$spawn = $this->lobby->getSpawn();
			$pos = new Position($spawn->x, $spawn->y, $spawn->z, $this->lobby->getLevel());
			$player->teleport($pos);
			return;
		}
		if($ev instanceof EntityDamageByEntityEvent){
			if($ev->getDamager() instanceof Player and $this->lobby->isAllowedPvP()) return;
			$ev->cancel();
		}
	}

	public function onDeath(PlayerDeathEvent $ev):void{
		if(!Lobby::isLobby($ev->getPlayer()->getWorld())) return;
		$ev->setDrops([]);
	}

	public function onCommand(CommandEvent $ev):void{
		$sender = $ev->getSender();
		
		if(!$sender instanceof Player) return;
		if($ev->getCommand() === 'kill' and Lobby::isLobby($sender->getWorld())){
			if($this->lobby->isCancelledKillCommand()) $ev->cancel();
		}
	}

	public function onItemDrop(PlayerDropItemEvent $ev):void{
		if(!Lobby::isLobby($ev->getPlayer()->getWorld())) return;
		if($this->lobby->isCancelledDorpItem()) $ev->cancel();
	}
}