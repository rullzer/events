<?php
declare(strict_types=1);
/**
 * @copyright Copyright (c) 2018 Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Events\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method void setToken(string $token)
 * @method string getToken()
 * @method void setEventName(string $name)
 * @method string getEventName()
 * @method void setStart(int $start)
 * @method int getStart()
 * @method void setEnd(int $end)
 * @method int getEnd()
 * @method void setUid(string $uid)
 * @method string getUid()
 * @method void setFolderId(int $id)
 * @method int getFolderId()
 */
class Event extends Entity {
	/** @var string */
	protected $token;
	
	/** @var string */
	protected $eventName;

	/** @var int */
	protected $start;

	/** @var int */
	protected $end;

	/** @var string */
	protected $uid;

	/** @var int */
	protected $folderId;

	public function __construct() {
		$this->addType('token', 'string');
		$this->addType('eventName', 'string');
		$this->addType('start', 'int');
		$this->addType('end', 'int');
		$this->addType('uid', 'string');
		$this->addType('folderId', 'int');
	}
}
