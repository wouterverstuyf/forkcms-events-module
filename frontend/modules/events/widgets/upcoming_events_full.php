<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with the upcoming Events
 *
 * @author Wouter Verstuyf <info@webflow.be>
 */
class FrontendEventsWidgetUpcomingEventsFull extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->parse();
	}

	/**
	 * Parse
	 */
	private function parse()
	{
		
		// get events
		$events = FrontendEventsModel::getAllUpcomingEvents(3);
			
		// assign events
		$this->tpl->assign('widgetUpcomingEventsFull', $events);

	}
}
