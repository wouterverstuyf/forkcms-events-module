<?php

/**
 * Alters the sequence of Events articles
 *
 * @author Wouter Verstuyf <info@webflow.be>
 */
class BackendEventsAjaxSequenceCategories extends BackendBaseAJAXAction
{
	public function execute()
	{
		parent::execute();

		// get parameters
		$newIdSequence = trim(SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

		// list id
		$ids = (array) explode(',', rtrim($newIdSequence, ','));

		// loop id's and set new sequence
		foreach($ids as $i => $id)
		{
			$item['id'] = $id;
			$item['sequence'] = $i + 1;

			// update sequence
			if(BackendEventsModel::existsCategory($id)) BackendEventsModel::updateCategory($item);
		}

		// success output
		$this->output(self::OK, null, 'sequence updated');
	}
}
