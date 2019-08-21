<?php

/*
 * _      _ _        _____               
 *| |    (_) |      / ____|              
 *| |     _| |_ ___| |     ___  _ __ ___ 
 *| |    | | __/ _ \ |    / _ \| '__/ _ \
 *| |____| | ||  __/ |___| (_) | | |  __/
 *|______|_|\__\___|\_____\___/|_|  \___|
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author genisyspromcpe
 * @link https://github.com/genisyspromcpe/LiteCore
 *
 *
*/

namespace pocketmine\network\protocol;

#include <rules/DataPacket.h>


class TextPacket extends DataPacket {

	const NETWORK_ID = Info::TEXT_PACKET;

	const TYPE_RAW = 0;
	const TYPE_CHAT = 1;
	const TYPE_TRANSLATION = 2;
	const TYPE_POPUP = 3;
	const TYPE_TIP = 4;
	const TYPE_SYSTEM = 5;
	const TYPE_WHISPER = 6;

	public $type;
	public $source;
	public $message;
	public $parameters = [];

	/**
	 *
	 */
	public function decode(){
		$this->type = $this->getByte();
		switch($this->type){
			case self::TYPE_CHAT:
				$this->source = $this->getString();
				$this->message = $this->getString();
				break;
		}
	}

	/**
	 *
	 */
	public function encode(){
		$this->reset();
		$this->putByte($this->type);
		switch($this->type){
			case self::TYPE_POPUP:
			case self::TYPE_CHAT:
				/** @noinspection PhpMissingBreakStatementInspection */
			case self::TYPE_WHISPER:
				$this->putString($this->source);
			case self::TYPE_RAW:
			case self::TYPE_TIP:
			case self::TYPE_SYSTEM:
				$this->putString($this->message);
				break;

			case self::TYPE_TRANSLATION:
				$this->putString($this->message);
				$this->putUnsignedVarInt(count($this->parameters));
				foreach($this->parameters as $p){
					$this->putString($p);
				}
		}
	}

	/**
	 * @return PacketName|string
	 */
	public function getName(){
		return "TextPacket";
	}

}
