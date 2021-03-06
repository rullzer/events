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

use OCA\Events\Db\Event;
use OCA\Events\Mapper\EventMapper;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\Security\ISecureRandom;
use OCP\Share\IManager as ShareManager;

class ApiController extends Controller {

	/** @var EventMapper */
	private $eventMapper;

	/** @var ShareManager */
	private $shareManager;

	/** @var IRootFolder */
	private $rootFolder;

	/** @var IUserManager */
	private $userManager;

	/** @var IUserSession */
	private $userSession;

	/** @var ISecureRandom */
	private $random;

	/** @var IURLGenerator */
	private $urlGenerator;

	public function __construct(string $appName,
								IRequest $request,
								EventMapper $eventMapper,
								ShareManager $sharemanager,
								IRootFolder $rootFolder,
								IUserManager $userManager,
								IUserSession $userSession,
								ISecureRandom $random,
								IURLGenerator $urlGenerator) {
		parent::__construct($appName, $request);

		$this->eventMapper = $eventMapper;
		$this->shareManager = $sharemanager;
		$this->rootFolder = $rootFolder;
		$this->userManager = $userManager;
		$this->userSession = $userSession;
		$this->random = $random;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function createEvent(string $eventName, int $start, int $end) {
		$user = $this->userSession->getUser();

		if ($user === null) {
			return new JSONResponse([], Http::STATUS_UNAUTHORIZED);
		}

		$event = new Event();
		$event->setUid($user->getUID());

		$token = $this->random->generate(6, ISecureRandom::CHAR_HUMAN_READABLE);
		$event->setToken($token);
		$event->setStart($start);
		$event->setEnd($end);
		$event->setEventName($eventName);

		$eventFolder = $this->getFolder($event);

		$share = $this->shareManager->newShare();
		$share->setShareType(\OCP\Share::SHARE_TYPE_LINK);
		$share->setSharedBy($user->getUID());
		$share->setPermissions(\OCP\Constants::PERMISSION_READ);
		$share->setNode($eventFolder);
		$share = $this->shareManager->createShare($share);

		$event->setFolderId($eventFolder->getId());
		$this->eventMapper->insert($event);

		return new JSONResponse([
			'token' => $token,
		]);
	}


	/**
	 * @PublicPage
	 * @NoCSRFRequired
	 */
	public function getEventInfo(string $token): JSONResponse {
		try {
			$event = $this->eventMapper->getEvent($token);
		} catch (DoesNotExistException $e) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}

		$folder = $this->getFolder($event);

		$shares = $this->shareManager->getSharesBy($event->getUid(), \OCP\Share::SHARE_TYPE_LINK, $folder);
		if (empty($shares)) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}

		$share = array_pop($shares);

		$user = $this->userManager->get($event->getUid());

		return new JSONResponse([
			'eventName' => $event->getEventName(),
			'organizer' => $user->getDisplayName(),
			'start' => $event->getStart(),
			'end' => $event->getEnd(),
			'readToken' => $share->getToken(),
			'url' => $this->urlGenerator->getAbsoluteURL(
				$this->urlGenerator->linkTo('', 'public.php') . '/webdav'
			)
		]);
	}

	/**
	 * @PublicPage
	 * @NoCSRFRequired
	 */
	public function createUpload(string $token, string $username): JSONResponse {
		try {
			$event = $this->eventMapper->getEvent($token);
		} catch (DoesNotExistException $e) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		}

		$folder = $this->getFolder($event);

		try {
			$userFolder = $folder->get($username);
			return new JSONResponse([], Http::STATUS_PRECONDITION_FAILED);
		} catch (NotFoundException $e) {
			$userFolder = $folder->newFolder($username);
		}
		
		$share = $this->shareManager->newShare();
		$share->setShareType(\OCP\Share::SHARE_TYPE_LINK);
		$share->setNode($userFolder);
		$share->setSharedBy($event->getUid());
		$share->setPermissions(\OCP\Constants::PERMISSION_READ | \OCP\Constants::PERMISSION_CREATE | \OCP\Constants::PERMISSION_UPDATE | \OCP\Constants::PERMISSION_DELETE);

		$share = $this->shareManager->createShare($share);

		return new JSONResponse([
			'privateToken' => $share->getToken(),
			'url' => $this->urlGenerator->getAbsoluteURL(
				$this->urlGenerator->linkTo('', 'public.php') . '/webdav'
			)
		]);
	}

	private function getFolder(Event $event): Folder {
		$userFolder = $this->rootFolder->getUserFolder($event->getUid());

		$folders = $userFolder->getById($event->getFolderId());

		if (empty($folders)) {
			//Create it
			try {
				$eventsFolder = $userFolder->get('Events');
			} catch (NotFoundException $e) {
				$eventsFolder = $userFolder->newFolder('Events');
			}

			try {
				$eventFolder = $eventsFolder->get($event->getEventName());
			} catch (NotFoundException $e) {
				$eventFolder = $eventsFolder->newFolder($event->getEventName());
			}

			return $eventFolder;
		}

		return array_pop($folders);
	}
}
