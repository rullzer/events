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

namespace OCA\Events\Migration;

use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version10000Date20180522200536 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param \Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, \Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		$table = $schema->createTable('events');
		$table->addColumn('id', 'integer', [
			'autoincrement' => true,
			'notnull' => true,
			'length' => 4,
			'unsigned' => true,
		]);
		$table->addColumn('token', 'string', [
			'notnull' => true,
			'length' => 64,
		]);
		$table->addColumn('event_name', 'string', [
			'notnull' => true,
			'length' => 255,
		]);
		$table->addColumn('start', 'integer', [
			'notnull' => true,
			'length' => 4,
			'unsigned' => true,
		]);
		$table->addColumn('end', 'integer', [
			'notnull' => true,
			'length' => 4,
			'unsigned' => true,
		]);
		$table->addColumn('uid', 'string', [
			'notnull' => true,
			'length' => 64,
		]);
		$table->addColumn('folder_id', 'integer', [
			'notnull' => true,
			'length' => 4,
			'unsigned' => true,
		]);
		$table->setPrimaryKey(['id']);
		$table->addUniqueIndex(['token'], 'events_token_index');
		$table->addIndex(['uid'], 'events_uid_index');

		return $schema;
	}
}
