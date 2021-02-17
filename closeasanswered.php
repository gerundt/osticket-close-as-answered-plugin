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
					$ost->logInfo('Mark ticket as answered with CloseAsAnsweredPlugin',
						sprintf('Mark ticket %s as answered', $ticket->getNumber()));
				}
			} catch(Exception $e) {
				$ost->logError('CloseAsAnsweredPlugin Exception', $e->getMessage());
			}
		}
	}

}
