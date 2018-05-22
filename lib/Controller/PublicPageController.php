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

namespace OCA\Events\Controller;

use OCA\Events\Mapper\EventMapper;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\NotFoundResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IURLGenerator;

class PublicPageController extends Controller {

	/** @var EventMapper */
	private $eventMapper;

	/** @var IURLGenerator */
	private $urlGenerator;

	public function __construct(string $appName,
								IRequest $request,
								EventMapper $eventMapper,
								IURLGenerator $urlGenerator) {
		parent::__construct($appName, $request);

		$this->eventMapper = $eventMapper;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * @PublicPage
	 * @NoCSRFRequired
	 */
	public function index(string $token) {
		try {
			$event = $this->eventMapper->getEvent($token);
		} catch (DoesNotExistException $e) {
			return new NotFoundResponse();
		}

		$url = $this->urlGenerator->linkToRouteAbsolute('events.api.getEventInfo', ['token' => $token]);
		
		//Remove http:// or https://
		$loc = strpos($url, '://');
		$url = substr($url, $loc+3);

		return new TemplateResponse('events', 'public', ['event' => $event, 'link' => $url], 'guest');
	}
}
