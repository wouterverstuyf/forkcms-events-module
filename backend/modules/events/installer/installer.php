<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the Events module
 *
 * @author Wouter Verstuyf <info@webflow.be>
 */
class EventsInstaller extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// install the module in the database
		$this->addModule('events');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'events');

		$this->setActionRights(1, 'events', 'index');
		$this->setActionRights(1, 'events', 'add');
		$this->setActionRights(1, 'events', 'edit');
		$this->setActionRights(1, 'events', 'delete');
		$this->setActionRights(1, 'events', 'categories');
		$this->setActionRights(1, 'events', 'add_category');
		$this->setActionRights(1, 'events', 'edit_category');
		$this->setActionRights(1, 'events', 'delete_category');
		$this->setActionRights(1, 'events', 'sequence_categories');

		$this->insertExtra('events', 'block', 'EventsCategory', 'category', null, 'N', 1002);
		$this->insertExtra('events', 'widget', 'Categories', 'categories', null, 'N', 1003);
		$this->insertExtra('events', 'widget', 'UpcomingEvents', 'upcoming_events', null, 'N', 1004);

		$this->makeSearchable('events');

		// add extra's
		$subnameID = $this->insertExtra('events', 'block', 'Events', null, null, 'N', 1000);
		$this->insertExtra('events', 'block', 'EventsDetail', 'detail', null, 'N', 1001);

		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationEventsId = $this->setNavigation($navigationModulesId, 'Events');
		$this->setNavigation(
			$navigationEventsId, 'Events', 'events/index',
			array('events/add', 'events/edit')
		);
		$this->setNavigation(
			$navigationEventsId, 'Categories', 'events/categories',
			array('events/add_category', 'events/edit_category')
		);
	}
}
