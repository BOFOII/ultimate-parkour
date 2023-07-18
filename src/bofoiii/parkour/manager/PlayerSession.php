<?php

declare(strict_types=1);

namespace bofoiii\parkour\manager;

use bofoiii\parkour\event\PlayerFinishEvent;
use bofoiii\parkour\Parkour;
use bofoiii\parkour\ParkourTask;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PlayerSession
{

    private int $timer = 0;
    private int $checkPoint = 1;

    public function __construct(private Player $player, private ParkourData $parkourData)
    {
        $player->getInventory()->setItem(7, VanillaItems::EMERALD()->setCustomName(TextFormat::YELLOW . "Reset"));
        $player->getInventory()->setItem(8, VanillaBlocks::BED()->setColor(DyeColor::RED())->asItem()->setCustomName(TextFormat::YELLOW . "Leave"));
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getParkourData(): ParkourData
    {
        return $this->parkourData;
    }

    public function getTimer(): int
    {
        return $this->timer;
    }

    public function incrementTimer(): void
    {
        $this->timer++;
    }

    public function getCompleted(): int
    {
        return $this->checkPoint;
    }

    public function setCompleted(int $checkPoint): void
    {
        if($checkPoint <= $this->checkPoint) {
            return;
        }

        if ($checkPoint == $this->parkourData->getCheckPointSlots()) {
            $this->player->sendMessage(TextFormat::GREEN . "Finish with " . $this->timer / 20 . " seconds");
            $ev = new PlayerFinishEvent($this->player, $this->parkourData, $this->timer);
            $ev->call();
            $this->save();
        } else {
            $this->player->sendMessage(TextFormat::GREEN . "Spawn point set to checkpoint #" . $checkPoint);
        }
        
        $this->checkPoint = $checkPoint;
    }

    public function save(): void
    {
        $this->player->getInventory()->clearAll();
        $this->player->teleport($this->parkourData->getHub());
        Parkour::getInstance()->getManager()->quitSession($this->player);
    }
}