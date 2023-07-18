<?php

declare(strict_types=1);

namespace bofoiii\parkour\manager;

use bofoiii\parkour\Parkour;
use Exception;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\world\Position;

class ParkourManager
{

    const PARKOUR_DIR = 'parkour';


    /** @var ParkourData[] $parkoursData */
    private array $parkoursData = [];

    /** @var PlayerSession[] $sessions */
    private array $sessions = [];

    public function __construct(private Plugin $plugin)
    {
        $this->__init();
        $this->loads();
    }

    private function __init(): void
    {
        if (!is_dir($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if (!is_dir($this->getDataFolder() . self::PARKOUR_DIR)) {
            @mkdir($this->getDataFolder() . self::PARKOUR_DIR);
        }
    }

    private function loads(): void
    {
        $parkours = glob($this->getDataFolder() . self::PARKOUR_DIR . DIRECTORY_SEPARATOR . "*.yml");
        if (!$parkours) {
            Parkour::getInstance()->getLogger()->info("§cParkour data is empty");
            return;
        }

        foreach ($parkours as $data) {
            $config = new Config($data, Config::YAML);
            try {
                /**
                 * @var array{
                 * displayName: string,
                 * world: string,
                 * hub: null|string,
                 * bottom: float|int,
                 * checkPointSlots: int,
                 * checkPoints: array<int, string>
                 * } $value
                 */
                $value = $config->getAll();
                $pData = new ParkourData($value);
                $pData->enable();
                $this->createData($pData);
            } catch (Exception $e) {
                Parkour::getInstance()->getLogger()->info("§c" . $e->getMessage());
            }
        }
    }

    public function createData(ParkourData $data): void
    {
        $this->parkoursData[$data->getDisplayName()] = $data;
    }

    public function getData(string $dName): ?ParkourData
    {
        return isset($this->parkoursData[$dName]) ? $this->parkoursData[$dName] : null;
    }

    /**
     * @return ParkourData[]
     */
    public function getDatas(): array
    {
        return $this->parkoursData;
    }

    public function getDataByCheckPoint(Position $pos): ?ParkourData
    {
        foreach ($this->parkoursData as $data) {
            if (!$data->isEnable()) {
                continue;
            }

            if ($data->getWorld() !== $pos->getWorld()->getFolderName()) {
                continue;
            }

            $checkPoint = $data->getCheckPoint(1);
            if ($checkPoint == null) {
                continue;
            }

            if ($pos->distance($checkPoint) > 1) {
                continue;
            }

            return $data;
        }

        return null;
    }

    /**
     * @return PlayerSession[]
     */
    public function getSessions(): array
    {
        return $this->sessions;
    }

    public function getSession(Player $player): ?PlayerSession
    {
        return $this->isInSession($player) ? $this->sessions[$player->getName()] : null;
    }

    public function isInSession(Player $player): bool
    {
        return isset($this->sessions[$player->getName()]);
    }

    public function addSession(Player $player, ParkourData $data): void
    {
        $this->sessions[$player->getName()] = new PlayerSession($player, $data);
    }

    public function quitSession(Player $player): void
    {
        unset($this->sessions[$player->getName()]);
    }

    public function getDataFolder(): string
    {
        return $this->plugin->getDataFolder();
    }
}
