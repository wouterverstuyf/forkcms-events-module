<?php
/**
 * In this file we store all generic functions that we will be using in the Events module
 *
 * @author Wouter Verstuyf <info@webflow.be>
 */
class BackendEventsModel
{
	const QRY_DATAGRID_BROWSE =
		'SELECT i.id, i.title, UNIX_TIMESTAMP(i.created_on) AS created_on
		 FROM events AS i
		 WHERE i.language = ?';

	const QRY_DATAGRID_BROWSE_CATEGORIES =
		'SELECT c.id, c.title, COUNT(i.id) AS num_items, c.sequence
		 FROM events_categories AS c
		 LEFT OUTER JOIN events AS i ON c.id = i.category_id AND i.language = c.language
		 WHERE c.language = ?
		 GROUP BY c.id
		 ORDER BY c.sequence ASC';

	/**
	 * Delete a certain item
	 *
	 * @param int $id
	 */
	public static function delete($id)
	{
		BackendModel::getContainer()->get('database')->delete('events', 'id = ?', (int) $id);
	}

	/**
	 * Delete a specific category
	 *
	 * @param int $id
	 */
	public static function deleteCategory($id)
	{
		$db = BackendModel::getContainer()->get('database');
		$item = self::getCategory($id);

		if(!empty($item))
		{
			$db->delete('meta', 'id = ?', array($item['meta_id']));
			$db->delete('events_categories', 'id = ?', array((int) $id));
			$db->update('events', array('category_id' => null), 'category_id = ?', array((int) $id));
		}
	}

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM events AS i
			 WHERE i.id = ?
			 LIMIT 1',
			array((int) $id)
		);
	}

	/**
	 * Does the category exist?
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function existsCategory($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM events_categories AS i
			 WHERE i.id = ? AND i.language = ?
			 LIMIT 1',
			array((int) $id, BL::getWorkingLanguage()));
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 * @return array
	 */
	public static function get($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*, UNIX_TIMESTAMP(i.begin_date) AS begin_date, UNIX_TIMESTAMP(i.end_date) AS end_date
			 FROM events AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Get all the categories
	 *
	 * @param bool[optional] $includeCount
	 * @return array
	 */
	public static function getCategories($includeCount = false)
	{
		$db = BackendModel::getContainer()->get('database');

		if($includeCount)
		{
			return (array) $db->getPairs(
				'SELECT i.id, CONCAT(i.title, " (",  COUNT(p.category_id) ,")") AS title
				 FROM events_categories AS i
				 LEFT OUTER JOIN events AS p ON i.id = p.category_id AND i.language = p.language
				 WHERE i.language = ?
				 GROUP BY i.id',
				 array(BL::getWorkingLanguage()));
		}

		return (array) $db->getPairs(
			'SELECT i.id, i.title
			 FROM events_categories AS i
			 WHERE i.language = ?',
			 array(BL::getWorkingLanguage()));
	}

	/**
	 * Is this category allowed to be deleted?
	 *
	 * @return	bool
	 * @param	int $id		The category id to check.
	 */
	public static function isCategoryAllowedToBeDeleted($id)
	{
		return ! (bool) BackendModel::getContainer()->get('database')->getVar('SELECT COUNT(i.id)
														FROM events AS i
														WHERE i.category_id = ?',
														array((int) $id));
	}

	/**
	 * Fetch a category
	 *
	 * @param int $id
	 * @return array
	 */
	public static function getCategory($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			 FROM events_categories AS i
			 WHERE i.id = ? AND i.language = ?',
			 array((int) $id, BL::getWorkingLanguage()));
	}

	/**
	 * Get the maximum sequence for a category
	 *
	 * @return int
	 */
	public static function getMaximumCategorySequence()
	{
		return (int) BackendModel::getContainer()->get('database')->getVar(
			'SELECT MAX(i.sequence)
			 FROM events_categories AS i
			 WHERE i.language = ?',
			 array(BL::getWorkingLanguage()));
	}

	/**
	 * Retrieve the unique URL for an item
	 *
	 * @param string $url
	 * @param int[optional] $id	The id of the item to ignore.
	 * @return string
	 */
	public static function getURL($url, $id = null)
	{
		$url = SpoonFilter::urlise((string) $url);
		$db = BackendModel::getContainer()->get('database');

		// new item
		if($id === null)
		{
			// already exists
			if((bool) $db->getVar(
				'SELECT 1
				 FROM events AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url)))
			{
				$url = BackendModel::addNumber($url);
				return self::getURL($url);
			}
		}
		// current item should be excluded
		else
		{
			// already exists
			if((bool) $db->getVar(
				'SELECT 1
				 FROM events AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url, $id)))
			{
				$url = BackendModel::addNumber($url);
				return self::getURL($url, $id);
			}
		}

		return $url;
	}

	/**
	 * Retrieve the unique URL for a category
	 *
	 * @param string $url
	 * @param int[optional] $id The id of the category to ignore.
	 * @return string
	 */
	public static function getURLForCategory($url, $id = null)
	{
		$url = SpoonFilter::urlise((string) $url);
		$db = BackendModel::getContainer()->get('database');

		// new category
		if($id === null)
		{
			if((bool) $db->getVar(
				'SELECT 1
				 FROM events_categories AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url)))
			{
				$url = BackendModel::addNumber($url);
				return self::getURLForCategory($url);
			}
		}
		// current category should be excluded
		else
		{
			if((bool) $db->getVar(
				'SELECT 1
				 FROM events_categories AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url, $id)))
			{
				$url = BackendModel::addNumber($url);
				return self::getURLForCategory($url, $id);
			}
		}

		return $url;
	}


	/**
	 * Insert an item in the database
	 *
	 * @param array $item
	 * @return int
	 */
	public static function insert(array $item)
	{
		$item['created_on'] = BackendModel::getUTCDate();
		$item['edited_on'] = BackendModel::getUTCDate();

		return (int) BackendModel::getContainer()->get('database')->insert('events', $item);
	}

	/**
	 * Insert a category in the database
	 *
	 * @param array $item
	 * @return int
	 */
	public static function insertCategory(array $item)
	{
		$item['created_on'] = BackendModel::getUTCDate();
		$item['edited_on'] = BackendModel::getUTCDate();

		return BackendModel::getContainer()->get('database')->insert('events_categories', $item);
	}

	/**
	 * Updates an item
	 *
	 * @param array $item
	 */
	public static function update(array $item)
	{
		$item['edited_on'] = BackendModel::getUTCDate();

		BackendModel::getContainer()->get('database')->update(
			'events', $item, 'id = ?', (int) $item['id']
		);
	}

	/**
	 * Update a certain category
	 *
	 * @param array $item
	 */
	public static function updateCategory(array $item)
	{
		$item['edited_on'] = BackendModel::getUTCDate();

		BackendModel::getContainer()->get('database')->update(
			'events_categories', $item, 'id = ?', array($item['id'])
		);
	}
}
