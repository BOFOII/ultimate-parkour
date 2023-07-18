<?php

declare(strict_types=1);

namespace bofoiii\parkour\provider;

use bofoiii\parkour\Parkour;
use bofoiii\parkour\utils\Utils;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\FloatingTextParticle;

class YamlProvider
{

    const PLAYER_DATA = 'players.yml';

    /** @var array<string, float|int> $playersData */
    private array $playersData = [];
    private FloatingTextParticle $leaderboard;

    public function __construct(private Parkour $plugin)
    {
        $this->__init();
        $this->loads();
    }

    private function __init(): void
    {
        if (!is_file($this->getDataFolder() . self::PLAYER_DATA)) {
            $this->plugin->saveResource(self::PLAYER_DATA);
        }

        $this->plugin->saveDefaultConfig();
    }

    private function loads(): void
    {
        $config = new Config($this->getDataFolder() . self::PLAYER_DATA, Config::YAML);
        /* @phpstan-ignore-next-line */
        $this->playersData = $config->getAll();
    }

    public function hasPlayerData(Player $player): bool
    {
        return isset($this->playersData[$player->getName()]);
    }

    public function savePlayerData(Player $player, float|int $timer): void
    {
        if ($timer < $this->playersData[$player->getName()]) {
            $this->playersData[$player->getName()] = $timer;
            $config = new Config($this->getDataFolder() . self::PLAYER_DATA, Config::YAML);
            $config->set($player->getName(), $timer);
            $config->save();
        }
    }

    /**
     * @return array<string, float|int>
     */
    private function getTop10Players(): array
    {
        asort($this->playersData);
        return array_slice($this->playersData, 0, 10, true);
    }

    public function updateLoaderboard(): void
    {
        /** @var null|string $strPos */
        $strPos = $this->plugin->getConfig()->get("leaderboardPos");

        if ($strPos == null) {
            return;
        }

        $position = Utils::strTPos(":", $strPos);
        $text = TextFormat::YELLOW . TextFormat::BOLD . "LEADERBOARD\n";

        $rank = 1;
        foreach ($this->getTop10Players() as $name => $time) {
            $text .= "#" . $rank . " " .$name . " with " . $time . " seconds\n";
            $rank++;
        }

        if (isset($this->leaderboard)) {
            $this->leaderboard->setText($text);
        } else {
            $this->leaderboard = new FloatingTextParticle($text);
        }
        $position->getWorld()->addParticle($position, $this->leaderboard, $position->getWorld()->getPlayers());
    }

    public function getDataFolder(): string
    {
        return $this->plugin->getDataFolder();
    }
}
