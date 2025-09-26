<?php

namespace Framework;

function text_encode(?string $text): string
{
	return htmlspecialchars(htmlspecialchars_decode($text));
}

function desc_encode(?string $text): string
{
	return nl2br(text_encode($text));
}

function tel(string $number)
{
	return preg_replace('/[^0-9]/', '', $number);
}
