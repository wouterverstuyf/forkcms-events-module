<?php
/**
 * In this file we store all generic functions that we will be using in the Events module
 *
 * @author Wouter Verstuyf <info@webflow.be>
 */
class FrontendEventsModel
{
	/**
	 * Fetches a certain item
	 *
	 * @param string $URL
	 * @return array
	 */
	public static function get($URL)
	{
		$item = (array) FrontendModel::getContainer()->get('database')->getRecord(
			'SELECT i.id, i.language, i.title, i.introduction, i.text,
       UNIX_TIMESTAMP(i.begin_date) AS begin_date, UNIX_TIMESTAMP(i.end_date) AS end_date,
       c.title AS category_title, m2.url AS category_url,
			 m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
			 m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
			 m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
			 FROM events AS i
       INNER JOIN events_categories AS c ON i.category_id = c.id
       INNER JOIN meta AS m ON i.meta_id = m.id
       INNER JOIN meta AS m2 ON c.meta_id = m2.id
			 WHERE m.url = ?',
			array((string) $URL)
		);

		// no results?
		if(empty($item)) return array();

		// create full url
		$item['full_url'] = FrontendNavigation::getURLForBlock('events', 'detail') . '/' . $item['url'];
    $item['category_full_url'] = FrontendNavigation::getURLForBlock('events', 'category') . '/' . $item['category_url'];

		return $item;
	}

	/**
	 * Get all items (at least a chunk)
	 *
	 * @param int[optional] $limit The number of items to get.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function getAll($limit = 10, $offset = 0)
	{
		$items = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT i.*, UNIX_TIMESTAMP(i.begin_date) AS begin_date, UNIX_TIMESTAMP(i.end_date) AS end_date, m.url,
					c.title AS category_title, m2.url AS category_url
			 FROM events AS i
			 INNER JOIN events_categories AS c ON i.category_id = c.id
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 INNER JOIN meta AS m2 ON c.meta_id = m2.id
			 WHERE i.language = ?
			 ORDER BY i.id DESC LIMIT ?, ?',
			array(FRONTEND_LANGUAGE, (int) $offset, (int) $limit));

		// no results?
		if(empty($items)) return array();

		// get detail action url
		$detailUrl = FrontendNavigation::getURLForBlock('events', 'detail');

		// get category link
		$categoryLink = FrontendNavigation::getURLForBlock('events', 'category');


		// prepare items for search
		foreach($items as &$item)
		{
			$item['full_url'] =  $detailUrl . '/' . $item['url'];
		}

		// return
		return $items;
	}


  /**
   * Get all the filtered events
   *
   * @param $query
   * @param $parameters
   * @param $limit
   * @param $offset
   *
   * @return array
   */
   public static function getAllFiltered($query, $parameters, $limit, $offset)
   {

   		// set paging to query
   		$query .= ' LIMIT '.$offset.', '.$limit;

   		// execute query
   		$items = (array) FrontendModel::getContainer()->get('database')->getRecords(
   			$query,
   			$parameters
   		);

      foreach($items as $key => $item) {

        // get detail url
        $link = FrontendNavigation::getURLForBlock('events', 'detail');

        // add url
        $items[$key]['full_url'] = $link . '/' . $item['url'];
        $items[$key]['category_full_url'] = FrontendNavigation::getURLForBlock('events', 'category') . '/' . $item['category_url'];
      }

   		// return items
   		return $items;
   }


  /**
   * Get all upcoming events
   *
   * @param int[optional] $limit The number of items to get
   * @return array
   */
  /*WHERE i.begin_date > NOW() AND i.language = ?*/
  public static function getAllUpcomingEvents($limit = 3)
  {
    $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
      'SELECT i.*, UNIX_TIMESTAMP(i.begin_date) AS begin_date, UNIX_TIMESTAMP(end_date) AS end_date, m.url
        FROM events AS i
        INNER JOIN meta AS m ON i.meta_id = m.id
        WHERE i.language = ?
        AND i.begin_date > NOW()
        ORDER BY i.begin_date DESC LIMIT ?',
      array(FRONTEND_LANGUAGE, (int) $limit)
    );

    // no results?
    if(empty($items)) return array();

    // get detail action url
    $detaulUrl = FrontendNavigation::getURLForBlock('events', 'detail');

    // add url to items
    foreach($items as &$item) {

      $item['full_url'] = $detaulUrl . '/' . $item['url'];

    }

    // return
    return $items;

  }

	/**
	 * Get the number of items
	 *
	 * @return int
	 */
	public static function getAllCount()
	{
		return (int) FrontendModel::getContainer()->get('database')->getVar(
			'SELECT COUNT(i.id) AS count
			 FROM events AS i'
		);
	}

	/**
	* Get all category items (at least a chunk)
	*
	* @param int $categoryId
	* @param int[optional] $limit The number of items to get.
	* @param int[optional] $offset The offset.
	* @return array
	*/
	public static function getAllByCategory($categoryId, $limit = 10, $offset = 0)
	{
		$items = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT i.*, UNIX_TIMESTAMP(i.begin_date) AS begin_date, UNIX_TIMESTAMP(end_date) AS end_date, m.url,
        c.title AS category_title, m2.url AS category_url
			 FROM events AS i
       INNER JOIN events_categories AS c ON i.category_id = c.id
			 INNER JOIN meta AS m ON i.meta_id = m.id
       INNER JOIN meta AS m2 ON c.meta_id = m2.id
			 WHERE i.category_id = ? AND i.language = ?
			 ORDER BY i.id DESC LIMIT ?, ?',
			array($categoryId, FRONTEND_LANGUAGE, (int) $offset, (int) $limit));

		// no results?
		if(empty($items)) return array();

		// get detail action url
		$detailUrl = FrontendNavigation::getURLForBlock('events', 'detail');

    // get category url
    $categoryLink = FrontendNavigation::getURLForBlock('events', 'category');

		// prepare items for search
		foreach($items as &$item)
		{
			$item['full_url'] = $detailUrl . '/' . $item['url'];
      $item['category_full_url'] = $categoryLink . '/' . $item['category_url'];
		}

		// return
		return $items;
	}


  /**
  * Get all items by date
  *
  * @param int $day
  * @param int $month
  * @param int $year
  * @return array
  */
  public static function getAllByDate($day, $month, $year, $limit = 10, $offset = 0)
  {
    $items = (array) FrontendModel::getContainer()->get('database')->getRecords(
      'SELECT i.*, m.url, c.title AS category_title, m2.url AS category_url
       FROM events AS i
       INNER JOIN meta AS m ON i.meta_id = m.id
       LEFT JOIN events_categories AS c ON i.category_id = c.id
       INNER JOIN meta AS m2 ON c.meta_id = m2.id
       WHERE i.language = ? AND DATE(begin_date) = ?
       ORDER BY i.id DESC LIMIT ?, ?',
      array(FRONTEND_LANGUAGE, $year.'-'.$month.'-'.$day, (int) $offset, (int) $limit));

    // no results?
    if(empty($items)) return array();

    // get detail action url
    $detailUrl = FrontendNavigation::getURLForBlock('events', 'detail');

    // get category url
    $categoryLink = FrontendNavigation::getURLForBlock('events', 'category');

    // prepare items for search
    foreach($items as &$item)
    {
      $item['full_url'] = $detailUrl . '/' . $item['url'];
      $item['category_full_url'] = $categoryLink . '/' . $item['category_url'];
    }

    // return
    return $items;
  }


	/**
	* Get all categories used
	*
	* @return array
	*/
	public static function getAllCategories()
	{
		$return = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT c.id, c.title AS label, m.url, COUNT(c.id) AS total, m.data AS meta_data
			 FROM events_categories AS c
			 INNER JOIN events AS i ON c.id = i.category_id AND c.language = i.language
			 INNER JOIN meta AS m ON c.meta_id = m.id
			 GROUP BY c.id
			 ORDER BY c.sequence',
			array(), 'id'
		);

		// loop items and unserialize
		foreach($return as &$row)
		{
			if(isset($row['meta_data'])) $row['meta_data'] = @unserialize($row['meta_data']);
		}

		return $return;
	}

	/**
	* Fetches a certain category
	*
	* @param string $URL
	* @return array
	*/
	public static function getCategory($URL)
	{
		$item = (array) FrontendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*,
			 m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
			 m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
			 m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
			 FROM events_categories AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE m.url = ?',
			array((string) $URL)
		);

		// no results?
		if(empty($item)) return array();

		// create full url
		$item['full_url'] = FrontendNavigation::getURLForBlock('events', 'category') . '/' . $item['url'];

		return $item;
	}



	/**
	* Get the number of items in a category
	*
	* @param int $categoryId
	* @return int
	*/
	public static function getCategoryCount($categoryId)
	{
		return (int) FrontendModel::getContainer()->get('database')->getVar(
			'SELECT COUNT(i.id) AS count
			 FROM events AS i
			 WHERE i.category_id = ?',
			array((int) $categoryId)
		);
	}

	/**
	* Parse the search results for this module
	*
	* Note: a module's search function should always:
	* 		- accept an array of entry id's
	* 		- return only the entries that are allowed to be displayed, with their array's index being the entry's id
	*
	*
	* @param array $ids The ids of the found results.
	* @return array
	*/
	public static function search(array $ids)
	{
		$items = (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT i.title AS title, m.url
			 FROM events AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.language = ? AND i.id IN (' . implode(',', $ids) . ')',
			array(FRONTEND_LANGUAGE), 'id'
		);

		// get detail action url
		$detailUrl = FrontendNavigation::getURLForBlock('events', 'detail');

		// prepare items for search
		foreach($items as &$item)
		{
			$item['full_url'] = $detailUrl . '/' . $item['url'];
		}

		// return
		return $items;
	}

}
