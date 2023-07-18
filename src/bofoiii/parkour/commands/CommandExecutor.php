<?php

declare(strict_types=1);

namespace bofoiii\parkour\commands;

use bofoiii\parkour\commands\setup\Save;
use bofoiii\parkour\commands\setup\SetCheckPoint;
use bofoiii\parkour\commands\setup\SetCPSlots;
use bofoiii\parkour\commands\setup\Create;
use bofoiii\parkour\commands\setup\SetBottom;
use bofoiii\parkour\commands\setup\SetHub;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;

class CommandExecutor extends BaseCommand {


    protected function prepare(): void
    {
        $this->registerSubCommand(new Create("create", "Create new parkor arena", ["pc"]));
        $this->registerSubCommand(new SetCPSlots("setslots", "Set check point slot parkor arena", ["pcs"]));
        $this->registerSubCommand(new SetCheckPoint("setcheckpoint", "Set check point parkor arena", ["psc"]));
        $this->registerSubCommand(new SetBottom("setbottom", "Set bottom parkor arena", ["psh"]));
        $this->registerSubCommand(new SetHub("sethub", "Set hub/leave spawn parkor arena", ["psb"]));
        $this->registerSubCommand(new Save("save", "Save parkur arena", ["psave"]));
        $this->registerSubCommand(new SetLeaderboard("setlead", "Set leader parkur arena", ["plead"]));
        $this->setPermission("ultimateparkour.command");
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param array<string> $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        
    }


}