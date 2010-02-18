<?

/**
* Split Page Results
* Simple class for pagination
* @package CorpusPHP
* @subpackage Navigation
* @todo full on rewrite of this, its inherrited from OSC and frankly a mess
*/
class SplitPageResults {
	var $sql_query, $number_of_rows, $current_page_number, $number_of_pages, $number_of_rows_per_page, $page_name, $link;

	/**
	* Constucts the pagination, determines number of pages, all that good stuff :-)
	* 
	* @todo re-implement $page_holder / rewrite
	* @param string $query the sql query to be paginated
	* @param int $max_rows the number of rows per page
	* @param mixed $link the url of the page to be paginated
	* @param mixed $page_holder the $_GET variable to be used for pagination, no longer implemented at the moment
	* @return SplitPageResults
	*/
	function __construct($query, $max_rows, $link, $page_holder = 'page') {
		/* should the need arise for multiple splits per page, impliment the page holder func.  May need some changes to app_top */
		global $page;
		$this->link = $link;
		$this->sql_query = $query;

		if (empty($page) || !is_numeric($page)) $page = 1;
		$this->current_page_number = $page;
		$this->number_of_rows_per_page = $max_rows;

		$count_query = db::query("SELECT COUNT(*) AS total FROM (".$query.") AS t");
		$count = @mysql_fetch_array($count_query);
		$this->number_of_rows = $count['total'];

		$this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);

		if ($this->current_page_number > $this->number_of_pages) {
			$this->current_page_number = $this->number_of_pages;
		}

		$offset = ($this->number_of_rows_per_page * ($this->current_page_number - 1));

		$this->sql_query .= " LIMIT " . max($offset, 0) . ", " . $this->number_of_rows_per_page;
	}

	/**
	* Builds and returns a list of pagination links
	* 
	* @todo clean this up, it looks a mess
	* @param string $parms paramaters to be added as a query string to the end of each link
	*/
	function getLinks($parms = '') {
		
		$str = '<div class="pagination">';
		if($this->current_page_number > 2) $str .= '<a href="'.$this->link.'?'.$parms.'">&laquo; First Page</a> &mdash; ';
		if($this->current_page_number > 1) $str .= '<a href="'.$this->link.'?page='.($this->current_page_number-1).'&'.$parms.'" class="n">Previous</a> &ndash; ';
		$str .= 'Page <strong>' . $this->current_page_number . '</strong> of ' . $this->number_of_pages;
		if($this->current_page_number < $this->number_of_pages) $str .= ' &ndash; <a href="'.$this->link.'?page='.($this->current_page_number+1).'&'.$parms.'" class="o">Next</a>';
		if($this->current_page_number < $this->number_of_pages) $str .= ' &mdash; <a href="'.$this->link.'?page='.($this->number_of_pages).'&'.$parms.'">Last Page &raquo;</a>';
		$str .= '</div>';
		return $str;
		
	}

}
