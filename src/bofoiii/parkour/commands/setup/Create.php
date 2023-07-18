<?php

declare(strict_types=1);

namespace bofoiii\parkour\commands\setup;

use bofoiii\parkour\manager\ParkourData;
use bofoiii\parkour\Parkour;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Create extends BaseSubCommand {

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
        $this->registerArgument(1, new RawStringArgument("world"));
        $this->setPermission("ultimateparkour.command.set");
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array{
     * name: string,
     * world: string
     * } $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player) {
            return;
        }

        $parkourData = Parkour::getInstance()->getManager()->getData($args["name"]);

        if($parkourData != null) {
            $sender->sendMessage(TextFormat::RED . "Parkour with arena name " . $args["name"] . " already exist");
            return;
        }

        if (Server::getInstance()->getWorldManager()->isWorldGenerated($args["world"])) {
            Server::getInstance()->getWorldManager()->loadWorld($args["world"]);
        } else {
            $sender->sendMessage(TextFormat::RED . "World with name " . $args["world"] . " is not exist");
            return;
        }

        $world = Server::getInstance()->getWorldManager()->getWorldByName($args["world"]);
        /**
         * Igone error because world already generated and loaded on line 46
         * @phpstan-ignore-next-line */
        $sender->teleport($world->getSafeSpawn());
        $data = new ParkourData(null);
        $data->setDisplayName($args["name"]);
        /**
         * Igone error because world already generated and loaded on line 46
         * @phpstan-ignore-next-line */
        $data->setWorld($world);
        Parkour::getInstance()->getManager()->createData($data);
        $sender->sendMessage(TextFormat::GREEN . "Parkour with arena name ". $args["name"] . " created");
    }
}