<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with the Events-categories
 *
 * @author Wouter Verstuyf <info@webflow.be>
 */
class FrontendEventsWidgetCategories extends FrontendBaseWidget
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
		// get categories
		$categories = FrontendEventsModel::getAllCategories();

		// any categories?
		if(!empty($categories))
		{
			// build link
			$link = FrontendNavigation::getURLForBlock('events', 'category');

			// loop and reset url
			foreach($categories as &$row) $row['url'] = $link . '/' . $row['url'];
		}

		// assign comments
		$this->tpl->assign('widgetEventsCategories', $categories);
	}
}
