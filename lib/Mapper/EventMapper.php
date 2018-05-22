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

namespace OCA\Events\Mapper;

use OCA\Events\Db\Event;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Mapper;
use OCP\IDBConnection;

class EventMapper extends Mapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'events', Event::class);
	}
	
	public function getEvent(string $token): Event {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('events')
			->where(
				$qb->expr()->eq('token', $qb->createNamedParameter($token))
			);

		$cursor = $qb->execute();
		$data = $cursor->fetch();

		if ($data === false) {
			throw new DoesNotExistException('Event not found');
		}

		$cursor->closeCursor();

		return Event::fromRow($data);
	}
}
