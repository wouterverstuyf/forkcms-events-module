<?php
/**
 * This is the configuration-object for the Events module
 *
 * @author Wouter Verstuyf <info@webflow.be>
 */
final class FrontendEventsConfig extends FrontendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var string
	 */
	protected $defaultAction = 'index';

	/**
	 * The disabled actions
	 *
	 * @var array
	 */
	protected $disabledActions = array();
}
