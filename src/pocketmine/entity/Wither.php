<?php

/*
 *
 *  _____            _               _____           
 * / ____|          (_)             |  __ \          
 *| |  __  ___ _ __  _ ___ _   _ ___| |__) | __ ___  
 *| | |_ |/ _ \ '_ \| / __| | | / __|  ___/ '__/ _ \ 
 *| |__| |  __/ | | | \__ \ |_| \__ \ |   | | | (_) |
 * \_____|\___|_| |_|_|___/\__, |___/_|   |_|  \___/ 
 *                         __/ |                    
 *                        |___/                     
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author GenisysPro
 * @link https://github.com/GenisysPro/GenisysPro
 *
 *
*/

namespace pocketmine\entity;

use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\item\Item as ItemItem;
use pocketmine\nbt\tag\{CompoundTag, IntTag, FloatTag, ListTag, StringTag, DoubleTag};

class Wither extends Animal {
	const NETWORK_ID = 52;

	public $width = 0.72;
	public $length = 6;
	public $height = 2;

	public $dropExp = [25, 50];
	private $boomTicks = 0;

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Wither";
	}

	public function initEntity(){
		$this->setMaxHealth(300);
		parent::initEntity();
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = Wither::NETWORK_ID;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

	//TODO: 添加出生和死亡情景

	/**
	 * @return array
	 */
	public function getDrops(){
		$drops = [ItemItem::get(ItemItem::NETHER_STAR, 0, 1)];
		return $drops;
	}
	
	public function getBombNBT() : CompoundTag{
		$nbt = new CompoundTag("", [
			"Pos" => new ListTag("Pos", [
				new DoubleTag("", $this->x),
				new DoubleTag("", $this->y + 2),
				new DoubleTag("", $this->z)
			]),
			"Motion" => new ListTag("Motion", [
				new DoubleTag("", 0),
				new DoubleTag("", 0),
				new DoubleTag("", 0)
			]),
			"Rotation" => new ListTag("Rotation", [
				new FloatTag("", $this->yaw),
				new FloatTag("", $this->pitch)
			]),
		]);
		return $nbt;
	}
	
	public function getBombRightNBT() : CompoundTag{
		$nbt = new CompoundTag("", [
			"Pos" => new ListTag("Pos", [
				new DoubleTag("", $this->x),
				new DoubleTag("", $this->y + 2),
				new DoubleTag("", $this->z)
			]),
			"Motion" => new ListTag("Motion", [
				new DoubleTag("", 0),
				new DoubleTag("", 0),
				new DoubleTag("", 0)
			]),
			"Rotation" => new ListTag("Rotation", [
				new FloatTag("", $this->yaw + 90),
				new FloatTag("", $this->pitch)
			]),
		]);
		return $nbt;
	}
	public function getBombLeftNBT() : CompoundTag{
		$nbt = new CompoundTag("", [
			"Pos" => new ListTag("Pos", [
				new DoubleTag("", $this->x),
				new DoubleTag("", $this->y + 2),
				new DoubleTag("", $this->z)
			]),
			"Motion" => new ListTag("Motion", [
				new DoubleTag("", 0),
				new DoubleTag("", 0),
				new DoubleTag("", 0)
			]),
			"Rotation" => new ListTag("Rotation", [
				new FloatTag("", $this->yaw - 90),
				new FloatTag("", $this->pitch)
			]),
		]);
		return $nbt;
	}
	
	public function onUpdate($currentTick){
		if($this->closed){
			return false;
		}

		$this->timings->startTiming();

		$hasUpdate = parent::onUpdate($currentTick);
		
		if($this->boomTicks < 40){
			$this->boomTicks++;
		}else{
			$nbt = $this->getBombNBT();
			$tnt = new WitherTNT($this->level, $nbt);
			$tnt->spawnToAll();
			
			$nbtright = $this->getBombRightNBT();
			$tntright = new WitherTNT($this->level, $nbtright);
			$tntright->spawnToAll();
			
			$nbtleft = $this->getBombLeftNBT();
			$tntleft = new WitherTNT($this->level, $nbtleft);
			$tntleft->spawnToAll();
			
			$this->close();
		}
		
		$this->timings->stopTiming();

		return $hasUpdate;
	}
}
