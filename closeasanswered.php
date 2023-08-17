<?php

require_once(INCLUDE_DIR.'class.signal.php');
require_once(INCLUDE_DIR.'class.plugin.php');

class CloseAsAnsweredPlugin extends Plugin {

	var $log_activity = true;

	var $config_class = 'CloseAsAnsweredConfig';

	function bootstrap() {
		$this->log_activity = $this->getConfig()->get('log_activity', true);
		
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
					if ($this->log_activity) {
						$ticket->logActivity(sprintf(__('Ticket Marked %s'), 'Answered'), 
							sprintf(__('Ticket flagged as %s by %s'), 'Answered', 'CloseAsAnsweredPlugin'));
					}
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
			'log_activity' => new BooleanField(array(
				'label' => __('Log Activity'),
				'default' => true,
				'configuration' => array(
					'desc' => __('Add a message to the activity log')
				)
			)),
		);
	}

	function pre_save(&$config, &$errors) {
		global $msg;
		
		if (!$errors)
			$msg = __('Configuration updated successfully');
		return true;
	}

}