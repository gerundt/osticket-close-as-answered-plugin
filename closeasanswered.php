<?php

require_once(INCLUDE_DIR.'class.signal.php');
require_once(INCLUDE_DIR.'class.plugin.php');

class CloseAsAnsweredPlugin extends Plugin {

	var $config_class = 'CloseAsAnsweredConfig';

	function bootstrap() {
		Signal::connect('object.edited', array($this, 'onObjectEdited'), 'Ticket');
	}

	function isMultiInstance() {
		return false;
	}

	/**
	 * @global $ost
	 * @param Ticket $ticket
	 * @param Array $data
	 */
	function onObjectEdited($ticket, $data) {
		global $ost;
		
		if (isset($data['key']) && $data['key'] == 'status_id') {
			try {
				if ($ticket->isClosed() && !$ticket->isAnswered()) {
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

class CloseAsAnsweredConfig extends PluginConfig {

    function getOptions() {
        return array(
            'dummy' => new BooleanField(array(
                'label' => __('Dummy option'),
                'default' => true,
                'configuration' => array(
                    'desc' => __('Unfortunately we need options to install a plugin. So here is a dummy option! ;-)')
                )
            )),
        );
    }

}