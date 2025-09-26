<?php

namespace Framework\Timber\Term;

use Timber\Term;

class DepartmentCategory extends Term
{
	public static $filter_key = 'category';
	public $fw_termlink = '/departments?category=%department-category%';
}
