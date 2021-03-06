<?php
/**
 * This is the index-action (default), it will display the overview of Events posts
 *
 * @author Wouter Verstuyf <info@webflow.be>
 */
class FrontendEventsDetail extends FrontendBaseBlock
{
	/**
	 * The record
	 *
	 * @var	array
	 */
	private $record;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->tpl->assign('hideContentTitle', true);
		$this->loadTemplate();
		$this->getData();
		$this->parse();
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		// validate incoming parameters
		if($this->URL->getParameter(1) === null) $this->redirect(FrontendNavigation::getURL(404));

		// get record
		$this->record = FrontendEventsModel::get($this->URL->getParameter(1));

		// check if record is not empty
		if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{

		// build Facebook  OpenGraph data
		$this->header->addOpenGraphData('title', $this->record['meta_title'], true);
		$this->header->addOpenGraphData('type', 'article', true);
		$this->header->addOpenGraphData(
			'url',
			SITE_URL . FrontendNavigation::getURLForBlock('events', 'detail') . '/' . $this->record['url'],
			true
		);
		$this->header->addOpenGraphData(
			'site_name',
			FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE),
			true
		);
		$this->header->addOpenGraphData('description', $this->record['meta_title'], true);

		// add into breadcrumb
		$this->breadcrumb->addElement($this->record['meta_title']);

		// hide action title
		$this->tpl->assign('hideContentTitle', true);

		// show title linked with the meta title
		$this->tpl->assign('title', $this->record['title']);

		// set meta
		$this->header->setPageTitle($this->record['meta_title'], ($this->record['meta_description_overwrite'] == 'Y'));
		$this->header->addMetaDescription($this->record['meta_description'], ($this->record['meta_description_overwrite'] == 'Y'));
		$this->header->addMetaKeywords($this->record['meta_keywords'], ($this->record['meta_keywords_overwrite'] == 'Y'));

		// advanced SEO-attributes
		if(isset($this->record['meta_data']['seo_index']))
		{
			$this->header->addMetaData(
				array('name' => 'robots', 'content' => $this->record['meta_data']['seo_index'])
			);
		}
		if(isset($this->record['meta_data']['seo_follow']))
		{
			$this->header->addMetaData(
				array('name' => 'robots', 'content' => $this->record['meta_data']['seo_follow'])
			);
		}

		$this->tpl->assign("dateFormat", "d M");

		// assign item
		$this->tpl->assign('item', $this->record);
	}
}
