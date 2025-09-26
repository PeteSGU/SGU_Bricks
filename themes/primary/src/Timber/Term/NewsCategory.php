<?php

namespace Framework\Timber\Term;

use Timber\Term;

class NewsCategory extends Term
{
	public static $filter_key = 'category';
	public $fw_termlink = '/news?category=%news-category%';
}
