<?php
/**
 * This is the index-action (default), it will display the overview of Events posts in a calendar overview
 *
 * @author Wouter Verstuyf <info@webflow.be>
 */
class FrontendEventsIndex extends FrontendBaseBlock
{

	/**
	 * Filter variables
	 *
	 * @var array
	 */
	private $filter;


	/**
	 * Form
	 */
	private $frm;


	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->header->addCSS('/frontend/modules/' . $this->getModule() . '/layout/css/events.css');

		$this->loadTemplate();
		$this->setFilter();
		$this->loadForm();
		$this->parse();
	}

	/**
	 * Filter form
	 */
	 private function loadForm()
	 {
	 	// create form
	 	$this->frm = new FrontendForm('filter', '', 'get');

	 	// values for dropdown months
	 	if(!$this->filter['month']) $currentMonth = date('m', time());
	 	else $currentMonth = $this->filter['month'];

	 	// get months
		$arrayOfMonths = SpoonLocale::getMonths(FRONTEND_LANGUAGE);

	 	// values for dropdown years
	 	if(!$this->filter['year']) $currentYear = date('Y', time());
	 	else $currentYear = $this->filter['year'];

	 	for($i = $currentYear-3; $i <= $currentYear+3; $i++) {
	 		$arrayOfYears[$i] = $i;
	 	}

	 	// add dropdowns to form
	 	$this->frm->addDropdown('month', $arrayOfMonths, $currentMonth);
	 	$this->frm->addDropdown('year', $arrayOfYears, $currentYear);

	 	// parse form
	 	$this->frm->parse($this->tpl);
	 }


	/**
	 * Get the position of the first day of the week
	 */
	private function getFirstDayOfMonthPosition($month, $year) {
	  $weekpos = date("w",mktime(0,0,0,$month,1,$year));
	  if ('WEEK_START' != 0)
	    if ($weekpos < 'WEEK_START')
	      $weekpos = $weekpos + 7 - 'WEEK_START';
	    else
	      $weekpos = $weekpos - 'WEEK_START';
	  return $weekpos;
	}


	/**
	 * Parse the page
	 */
	protected function parse()
	{
		$this->tpl->assign("dateFormat", "\<\p\>d\<\/\p\> M");

		// build calendar
		$day = date('j');
		$month = $this->filter['month'];
		$year = $this->filter['year'];
		if($month == '') $month = date('m');
		if($year == '') $year = date('Y');
		$currentTimeStamp = strtotime($year."-".$month."-".$day);
		$monthName = date("F", $currentTimeStamp);
		$numDays = date("t", $currentTimeStamp);
		$counter = 0;
		$weekpos = self::getFirstDayOfMonthPosition($month, $year);

		// calendar navigation links
		$nextYear  = $year + 1;
		$prevYear  = $year - 1;
		$prevMonth = ($month == 1) ? 12 : $month - 1;
		$nextMonth = ($month == 12) ? 1 : $month + 1;

		// get url
		$url = $_SERVER['REQUEST_URI'];
		$chunk = explode('?', $url);

		// assign navigation urls
		if($month == 12) {
			$nextMonthUrl = $chunk[0].'?year='.($year+1).'&month='.$nextMonth;
		} else {
			$nextMonthUrl = $chunk[0].'?year='.$year.'&month='.$nextMonth;
		}
		$nextYearUrl = $chunk[0].'?year='.$nextYear.'&month='.$month;
		if($month == 1) {
			$prevMonthUrl = $chunk[0].'?year='.($year-1).'&month='.$prevMonth;
		} else {
			$prevMonthUrl = $chunk[0].'?year='.$year.'&month='.$prevMonth;
		}
		$prevYearUrl = $chunk[0].'?year='.$prevYear.'&month='.$month;


		// days array
		$days = array();
		$emptydays = array();

		// loop through days
		for($i = 1; $i < $numDays + 1; $i++) {

			// assign empty days
			if($i == 1) {
				for($j = 0; $j < $weekpos; $j++) {
					$emptydays[] = array('day' => '');
				}
			}

			// assign days
			$days[] = array('day' => $i, 'items' => FrontendEventsModel::getAllByDate($i, $month, $year));

		}

		// assign navigation urls
		$this->tpl->assign('prevMonthUrl', $prevMonthUrl);
		$this->tpl->assign('nextMonthUrl', $nextMonthUrl);
		$this->tpl->assign('prevYearUrl', $prevYearUrl);
		$this->tpl->assign('nextYearUrl', $nextYearUrl);

		// assign days
		$this->tpl->assign('emptydays', $emptydays);
		$this->tpl->assign('days', $days);

	}

	/**
	 * Sets the filter based on the $_GET array.
	 */
	private function setFilter()
	{
		$this->filter['month'] = $this->URL->getParameter('month');
		$this->filter['year'] = $this->URL->getParameter('year');
	}
}
