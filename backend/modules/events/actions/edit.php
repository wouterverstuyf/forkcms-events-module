<?php
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author Wouter Verstuyf <info@webflow.be>
 */
class BackendEventsEdit extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->loadData();
		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the item data
	 */
	protected function loadData()
	{
		$this->id = $this->getParameter('id', 'int', null);
		if($this->id == null || !BackendEventsModel::exists($this->id))
		{
			$this->redirect(
				BackendModel::createURLForAction('index') . '&error=non-existing'
			);
		}

		$this->record = BackendEventsModel::get($this->id);
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');

		$this->frm->addText('title' ,$this->record['title'], null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('text', $this->record['text']);
		$this->frm->addEditor('introduction', $this->record['introduction']);
		$this->frm->addDate('begin_date_date', $this->record['begin_date']);
		$this->frm->addTime('begin_date_time', date('H:i', $this->record['begin_date']));
		$this->frm->addDate('end_date_date', $this->record['end_date']);
		$this->frm->addTime('end_date_time', date('H:i', $this->record['end_date']));
		$this->frm->addImage('image');
		$this->frm->addCheckbox('delete_image');

		// get categories
		$categories = BackendEventsModel::getCategories();
		$this->frm->addDropdown('category_id', $categories, $this->record['category_id']);

		// meta
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
		$this->meta->setUrlCallBack('BackendEventsModel', 'getUrl', array($this->record['id']));

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


		$this->tpl->assign('item', $this->record);
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
			$fields['end_date_date']->isFilled(BL::err('FieldIsRequired'));
			$fields['end_date_time']->isFilled(BL::err('FieldIsRequired'));
			$fields['end_date_date']->isValid(BL::err('DateIsInvalid'));
			$fields['end_date_time']->isValid(BL::err('TimeIsInvalid'));
			$fields['category_id']->isFilled(BL::err('FieldIsRequired'));

			// validate meta
			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				$item['id'] = $this->id;
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
				$item['end_date'] = BackendModel::getUTCDate(
					null,
					BackendModel::getUTCTimestamp(
						$this->frm->getField('end_date_date'),
						$this->frm->getField('end_date_time')
					)
				);
				$item['category_id'] = $this->frm->getField('category_id')->getValue();
				$item['meta_id'] = $this->meta->save();

				$item['image'] = $this->record['image'];

				// the image path
				$imagePath = FRONTEND_FILES_PATH . '/events';

				// create folders if needed
				$fs = new Filesystem();
				if(!$fs->exists($imagePath . '/source')) $fs->mkdir($imagePath . '/source');
				if(!$fs->exists($imagePath . '/128x128')) $fs->mkdir($imagePath . '/128x128');

				// if the image should be deleted
				if($this->frm->getField('delete_image')->isChecked())
				{
					// delete the image
					$fs->remove($imagePath . '/source/' . $item['image']);
					BackendModel::deleteThumbnails($imagePath, $item['image']);

					// reset the name
					$item['image'] = null;
				}

				// new image given?
				if($this->frm->getField('image')->isFilled())
				{
					// delete the old image
					$fs->remove($imagePath . '/source/' . $this->record['image']);
					BackendModel::deleteThumbnails($imagePath, $this->record['image']);

					// build the image name
					$item['image'] = $this->meta->getURL() . '.' . $this->frm->getField('image')->getExtension();

					// upload the image & generate thumbnails
					$this->frm->getField('image')->generateThumbnails($imagePath, $item['image']);
				}

				// rename the old image
				elseif($item['image'] != null)
				{
					$image = new File($imagePath . '/source/' . $item['image']);
					$newName = $this->meta->getURL() . '.' . $image->getExtension();

					// only change the name if there is a difference
					if($newName != $item['image'])
					{
						// loop folders
						foreach(BackendModel::getThumbnailFolders($imagePath, true) as $folder)
						{
							// move the old file to the new name
							$fs->rename($folder['path'] . '/' . $item['image'], $folder['path'] . '/' . $newName);
						}

						// assign the new name to the database
						$item['image'] = $newName;
					}
				}

				BackendEventsModel::update($item);
				$item['id'] = $this->id;

				// add search index
				BackendSearchModel::saveIndex(
					$this->getModule(), $item['id'],
					array('title' => $item['title'], 'Text' => $item['text'])
				);

				BackendModel::triggerEvent(
					$this->getModule(), 'after_edit', $item
				);
				$this->redirect(
					BackendModel::createURLForAction('index') . '&report=edited&highlight=row-' . $item['id']
				);
			}
		}
	}
}
