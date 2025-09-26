<?php

namespace Framework;

function fw_debug_message(string $message): void
{
	if (FW_DEBUG && !is_admin()) {
		dump($message);
	}
}
