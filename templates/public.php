<?php
	/** @var \OCA\Events\Db\Event $event */
	$event = $_['event'];
?>
<div id="event">
	<table>
		<tr>
			<td>Name:</td>
			<td><?php p($event->getEventName()); ?></td>
		</tr>
		<tr>
			<td>Organizer:</td>
			<td><?php p($event->getUid()); ?></td>
		</tr>
		<tr>
			<td>Start:</td>
			<td><?php p($event->getStart()); ?></td>
		</tr>
		<tr>
			<td>End:</td>
			<td><?php p($event->getEnd()); ?></td>
		</tr>
		<tr>
			<td><a href="ncevent://<?php p($_['link']); ?>">Open in app!</a> </td>
		</tr>
	</table>
</div>
