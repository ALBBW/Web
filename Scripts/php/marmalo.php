<?php

	class date_time
	{
		public function get_year()
		{
			$year = date("Y");
			return $year;
		}
		public function get_month()
		{
			$month = date("m");
			return $month;
		}
		public function get_day()
		{
			$day = date("d");
			return $day;
		}
	}
?>