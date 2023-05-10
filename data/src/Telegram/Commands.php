<?php
namespace Unclebot\Telegram;

use \Unclebot\Utils\Logger;
use \Unclebot\Telegram\Models\Text as TelegramText;
use \Unclebot\Telegram\Models\Image as TelegramImage;
use \Unclebot\Telegram\Models\ReplyMarkup\ReplyKeyboard;
use \Unclebot\Telegram\Models\ReplyMarkup\InlineKeyboard;

class Commands
{
	private $command;

	private $user;

	private $responseText;

	public function __construct(User $user, string $command, ResponseText $responseText)
	{
		$this->user = $user;
		$this->command = ltrim($command, '/');;
		$this->responseText = $responseText;
	}

	public function getResponseData()
	{
		$command = $this->command;

		switch ($command)
		{
			default:
				return array();
			case 'start':
				return $this->start();
			case 'time':
				return $this->time();
			case 'send':
				return $this->send();
		}
	}

	private function send()
	{
		$telegramTextModel = new TelegramText(\Unclebot\Wikipedia\Parser::getRandomUrl(), '', false);
		$telegramTextModel->removeKeyboard();
		
		return $telegramTextModel->getData();
	}

	private function time()
	{
		$text = $this->responseText->get('time');

		$text = str_replace('_time_', $this->user->getTime(), $text);

		$telegramTextModel = new TelegramText($text);
		$telegramTextModel->removeKeyboard();

		$this->user->state->set('time');
		
		return $telegramTextModel->getData();
	}

	private function start()
	{
		$text = $this->responseText->get('start');

		$telegramTextModel = new TelegramText($text);
		$telegramTextModel->removeKeyboard();
		
		return $telegramTextModel->getData();
	}
}
