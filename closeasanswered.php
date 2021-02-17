<?php

require_once(INCLUDE_DIR.'class.signal.php');
require_once(INCLUDE_DIR.'class.plugin.php');

class CloseAsAnsweredPlugin extends Plugin {

	function bootstrap() {
		Signal::connect('object.edited', array($this, 'onObjectEdited'), 'Ticket');
	}

	/**
	 * @global $ost
	 * @param Ticket $ticket
	 * @param Array $data
	 */
	function onObjectEdited($ticket, $data) {
		global $ost;
		
		if (isset($data['type']) && $data['type'] == 'closed') {
			try {
				if (!$ticket->isAnswered()) {
					$ticket->markAnswered();
					$ticket->logActivity(sprintf(__('Ticket Marked %s'), 'Answered'), 
						sprintf(__('Ticket flagged as %s by %s'), 'Answered', 'CloseAsAnsweredPlugin'));
				}
			} catch(Exception $e) {
				$ost->logError('CloseAsAnsweredPlugin Exception', $e->getMessage());
			}
		}
	}

}
