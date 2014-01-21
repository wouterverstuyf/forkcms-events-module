<?php
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Wouter Verstuyf <info@webflow.be>
 */
class BackendEventsAdd extends BackendBaseActionAdd
{
	/**
	 * Execute the actions
	 */
	public function execute()
	{
		parent::execute();

		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		$this->frm = new BackendForm('add');

		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('text');
		$this->frm->addEditor('introduction');
		$this->frm->addDate('begin_date_date');
		$this->frm->addTime('begin_date_time');
		$this->frm->addDate('end_date_date');
		$this->frm->addTime('end_date_time');
		$this->frm->addImage('image');

		// get categories
		$categories = BackendEventsModel::getCategories();
		$this->frm->addDropdown('category_id', $categories);

		// meta
		$this->meta = new BackendMeta($this->frm, null, 'title', true);

	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();

		// get url
		$url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
		$url404 = BackendModel::getURL(404);

		// parse additional variables
		if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);
		$this->record['url'] = $this->meta->getURL();

	}

	/**
	 * Validate the form
	 */
	protected function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			// validation
			$fields = $this->frm->getFields();

			$fields['title']->isFilled(BL::err('FieldIsRequired'));
			$fields['begin_date_date']->isFilled(BL::err('FieldIsRequired'));
			$fields['begin_date_time']->isFilled(BL::err('FieldIsRequired'));
			$fields['begin_date_date']->isValid(BL::err('DateIsInvalid'));
			$fields['begin_date_time']->isValid(BL::err('TimeIsInvalid'));

			if($fields['end_date_date']->isFilled() || $fields['end_date_time']->isFilled()) {
				$fields['end_date_date']->isFilled(BL::err('FieldIsRequired'));
				$fields['end_date_time']->isFilled(BL::err('FieldIsRequired'));
				$fields['end_date_date']->isValid(BL::err('DateIsInvalid'));
				$fields['end_date_time']->isValid(BL::err('TimeIsInvalid'));
			}

			$fields['category_id']->isFilled(BL::err('FieldIsRequired'));

			// validate the image
			if($this->frm->getField('image')->isFilled())
			{
				// image extension and mime type
				$this->frm->getField('image')->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), BL::err('JPGGIFAndPNGOnly'));
				$this->frm->getField('image')->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), BL::err('JPGGIFAndPNGOnly'));
			}

			// validate meta
			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				// build the item
				$item['language'] = BL::getWorkingLanguage();
				$item['title'] = $fields['title']->getValue();
				$item['text'] = $fields['text']->getValue();
				$item['introduction'] = $fields['introduction']->getValue();
				$item['begin_date'] = BackendModel::getUTCDate(
					null,
					BackendModel::getUTCTimestamp(
						$this->frm->getField('begin_date_date'),
						$this->frm->getField('begin_date_time')
					)
				);
				if($fields['end_date_date']->isFilled() || $fields['end_date_time']->isFilled()) {
					$item['end_date'] = BackendModel::getUTCDate(
						null,
						BackendModel::getUTCTimestamp(
							$this->frm->getField('end_date_date'),
							$this->frm->getField('end_date_time')
						)
					);
				}
				$item['category_id'] = $this->frm->getField('category_id')->getValue();
				$item['meta_id'] = $this->meta->save();

				// the image path
				$imagePath = FRONTEND_FILES_PATH . '/events';

				// create folders if needed
				$fs = new Filesystem();
				if(!$fs->exists($imagePath . '/source')) $fs->mkdir($imagePath . '/source');
				if(!$fs->exists($imagePath . '/128x128')) $fs->mkdir($imagePath . '/128x128');

				// image provided?
				if($this->frm->getField('image')->isFilled())
				{
					// build the image name
					$item['image'] = $this->meta->getURL() . '.' . $this->frm->getField('image')->getExtension();

					// upload the image & generate thumbnails
					$this->frm->getField('image')->generateThumbnails($imagePath, $item['image']);
				}

				// insert it
				$item['id'] = BackendEventsModel::insert($item);

				// add search index
				BackendSearchModel::saveIndex(
					$this->getModule(), $item['id'],
					array('title' => $item['title'], 'text' => $item['text'])
				);

				BackendModel::triggerEvent(
					$this->getModule(), 'after_add', $item
				);
				$this->redirect(
					BackendModel::createURLForAction('index') . '&report=added&highlight=row-' . $item['id']
				);
			}
		}
	}
}
