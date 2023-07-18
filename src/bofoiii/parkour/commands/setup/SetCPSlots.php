<?php

declare(strict_types=1);

namespace bofoiii\parkour\commands\setup;

use bofoiii\parkour\Parkour;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class SetCPSlots extends BaseSubCommand {

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
        $this->registerArgument(1, new IntegerArgument("slot"));
        $this->setPermission("ultimateparkour.command.set");
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array{
     * name: string,
     * slot: int
     * } $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        $parkourData = Parkour::getInstance()->getManager()->getData($args["name"]);

        if ($parkourData == null) {
            $sender->sendMessage(TextFormat::RED . "Parkour with arena name " . $args["name"] . " not exist");
            return;
        }

        $parkourData->setCheckPointSlot($args["slot"]);
        $sender->sendMessage(TextFormat::GREEN . $args["slot"] . " slots check points set for " . $args["name"]);
    }
}