<?php

namespace Framework\Timber\Term;

use Timber\Term;
use Tribe__Events__Main;

class EventCategory extends Term
{
	public $fw_termlink = '/events?category=%' . Tribe__Events__Main::TAXONOMY . '%';
}
