<?php

declare(strict_types=1);

namespace bofoiii\parkour;

use bofoiii\parkour\commands\CommandExecutor;
use bofoiii\parkour\manager\ParkourManager;
use bofoiii\parkour\provider\YamlProvider;
use CortexPE\Commando\PacketHooker;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class Parkour extends PluginBase {

    private static Parkour $instance;
    private ParkourManager $parkourManager;
    private YamlProvider $provider;

    protected function onEnable(): void
    {
        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        $this->parkourManager = new ParkourManager($this);
        $this->provider = new YamlProvider($this);
        Server::getInstance()->getCommandMap()->register("Utimate Parkour", new CommandExecutor($this, "parkour", "A command for parkour",[ "pk"]), "parkour");
        Server::getInstance()->getPluginManager()->registerEvents(new ParkourListener(), $this);
        $this->getScheduler()->scheduleRepeatingTask(new ParkourTask(), 1);
    }

    protected function onLoad(): void
    {
        self::$instance = $this;
    }

    public static function getInstance(): Parkour
    {
        return self::$instance;
    }

    public function getManager(): ParkourManager
    {
        return $this->parkourManager;
    }

    public function getProvider(): YamlProvider
    {
        return $this->provider;
    }

}
