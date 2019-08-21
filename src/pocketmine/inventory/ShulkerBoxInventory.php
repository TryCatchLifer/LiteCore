<?php

namespace pocketmine\inventory;

use pocketmine\level\Level;
use pocketmine\network\protocol\BlockEventPacket;
use pocketmine\network\protocol\LevelSoundEventPacket;
use pocketmine\network\protocol\types\InventoryNetworkIds;
use pocketmine\Player;
use pocketmine\tile\ShulkerBox;

class ShulkerBoxInventory extends ContainerInventory
{

    protected $holder;

    public function __construct(ShulkerBox $tile)
    {
        parent::__construct($tile, InventoryType::get(InventoryType::SHULKER_BOX));
    }

    public function getName(): string
    {
        return "Shulker Box";
    }

    public function getDefaultSize(): int
    {
        return 27;
    }

    /**
     * Returns the Minecraft PE inventory type used to show the inventory window to clients.
     * @return int
     */
    public function getNetworkType(): int
    {
        return InventoryNetworkIds::CONTAINER;
    }

    public function onClose(Player $who)
    {
        if(count($this->getViewers()) === 1){
            $pk = new BlockEventPacket();
            $pk->x = $this->getHolder()->getX();
            $pk->y = $this->getHolder()->getY();
            $pk->z = $this->getHolder()->getZ();
            $pk->case1 = 1;
            $pk->case2 = 0;
            if(($level = $this->getHolder()->getLevel()) instanceof Level){
                $level->broadcastLevelSoundEvent($this->getHolder(), LevelSoundEventPacket::SOUND_SHULKERBOX_CLOSED);
                $level->addChunkPacket($this->getHolder()->getX() >> 4, $this->getHolder()->getZ() >> 4, $pk);
            }
        }
        parent::onClose($who);
    }

    public function onOpen(Player $who)
    {
        parent::onOpen($who);
        if(count($this->getViewers()) === 1){
            $pk = new BlockEventPacket();
            $pk->x = $this->getHolder()->getX();
            $pk->y = $this->getHolder()->getY();
            $pk->z = $this->getHolder()->getZ();
            $pk->case1 = 1;
            $pk->case2 = 2;
            if(($level = $this->getHolder()->getLevel()) instanceof Level){
                $level->broadcastLevelSoundEvent($this->getHolder(), LevelSoundEventPacket::SOUND_SHULKERBOX_OPEN);
                $level->addChunkPacket($this->getHolder()->getX() >> 4, $this->getHolder()->getZ() >> 4, $pk);
            }
        }
    }

    protected function broadcastBlockEventPacket(bool $isOpen)
    {
        $holder = $this->getHolder();
        $pk = new BlockEventPacket();
        $pk->x = (int)$holder->x;
        $pk->y = (int)$holder->y;
        $pk->z = (int)$holder->z;
        $pk->eventType = 1;
        $pk->eventData = +$isOpen;
        $holder->getLevel()->addChunkPacket($holder->getX() >> 4, $holder->getZ() >> 4, $pk);
    }

    /**
     * @return ShulkerBox
     */
    public function getHolder()
    {
        return $this->holder;
    }
}