<?php

declare(strict_types=1);

namespace bofoiii\parkour;

use bofoiii\parkour\event\PlayerFinishEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\FloatingTextParticle;

class ParkourListener implements Listener {

    public function onJoin(PlayerJoinEvent $event): void
    {
        if(!Parkour::getInstance()->getProvider()->hasPlayerData($event->getPlayer())) {
            Parkour::getInstance()->getProvider()->createPlayerData($event->getPlayer());
        }

        foreach(Parkour::getInstance()->getManager()->getDatas() as $data) {
            $world = Server::getInstance()->getWorldManager()->getWorldByName($data->getWorld());

            if($world == null) {
                $data->disable();
                continue;
            }

            $checkpoints = $data->getCheckPoints();
            foreach($checkpoints as $slot => $pos) {

                if($slot == 1) {
                    $world->addParticle($pos->add(0, 1, 0), new FloatingTextParticle(TextFormat::GREEN . "START"), [$event->getPlayer()]);
                    continue;
                }
                
                if($pos->equals(end($checkpoints))) {
                    $world->addParticle($pos->add(0, 1, 0), new FloatingTextParticle(TextFormat::GREEN . "FINISH"), [$event->getPlayer()]);
                    continue;
                }
            
                $world->addParticle($pos->add(0, 1, 0), new FloatingTextParticle(TextFormat::GREEN . "SPAWN POINT #" . $slot));
            }
        }

        Parkour::getInstance()->getProvider()->updateLoaderboard();
    }

    public function onMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        $session = Parkour::getInstance()->getManager()->getSession($player);
        if ($session !== null) {
            $pkData = $session->getParkourData();

            foreach($pkData->getCheckPoints() as $slot => $cp) {
                if ($slot == $session->getCompleted()) {
                    continue;
                }

                if($cp->distance($player->getPosition()) < 2) {
                    $session->setCompleted($slot);
                    
                    return;
                }
            }

            if($player->getPosition()->getY() <= $pkData->getBottom()) {
                /**
                 * Igone error because check point cannot null
                 * please dont remove check point or setup if player in game
                 * @phpstan-ignore-next-line */
                $player->teleport($pkData->getCheckPoint($session->getCompleted()));
            }

            return;
        }

        $parkour = Parkour::getInstance()->getManager()->getDataByCheckPoint($player->getPosition());
        if($parkour == null) {
            return;
        }
        
        Parkour::getInstance()->getManager()->addSession($player, $parkour);
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $session = Parkour::getInstance()->getManager()->getSession($player);
        if ($session == null) {
            return;
        }

        $session->save();
    }

    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();

        $session = Parkour::getInstance()->getManager()->getSession($player);
        if ($session == null) {
            return;
        }

        if($event->getItem()->getCustomName() == TextFormat::YELLOW . "Leave") {
            $session->save();
            $event->cancel();
            return;
        }

        if($event->getItem()->getCustomName() == TextFormat::YELLOW . "Reset") {
            $session->save();
            Parkour::getInstance()->getManager()->addSession($player, $session->getParkourData());
            $event->cancel();
        }
    }

    public function onFinish(PlayerFinishEvent $event): void
    {
        Parkour::getInstance()->getProvider()->savePlayerData($event->getPlayer(), $event->getFinishTime() / 20);
        Parkour::getInstance()->getProvider()->updateLoaderboard();
    }
}