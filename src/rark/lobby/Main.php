<?php
declare(strict_types = 1);

namespace rark\lobby;

use pocketmine\entity\Effect;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\Config;

class Main extends PluginBase{

	public function onEnable(){
		$lobby = new Lobby(
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
					],
					Lobby::CONF_EFFECTS => [
						Effect::SPEED
					],
					Lobby::CONF_EXHAUST => false
				]
			)
		);
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($lobby), $this);
		$this->getScheduler()->scheduleRepeatingTask(
			new ClosureTask(
				function() use($lobby):void{
					if($lobby === null){
						$lobby = Lobby::getInstance();

						if($lobby === null) return;
					}

					/** @var Player $player */
					foreach(Server::getInstance()->getOnlinePlayers() as $player){
						if($player->getLevel()?->getName() !== $this->lobby->getLevel()->getName()) continue;

						/** @var \pocketmine\entity\EffectInstance $effect*/
						foreach($lobby->getEffects() as $effect){
							$player->addEffect($effect);
						}
					}
				}
			),
			20
		);
	}
}