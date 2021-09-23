<?php
declare(strict_types = 1);

namespace rark\lobby;

class KeyNotFoundException extends \Exception{

	public function __construct(string $key){
		parent::__construct('key "'.$key.'" was not found');
	}
}