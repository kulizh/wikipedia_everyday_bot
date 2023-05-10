<?php
namespace Unclebot\Telegram;

use \Unclebot\Telegram\Models\Text as TelegramText;
use \Unclebot\Telegram\Models\ReplyMarkup\ReplyKeyboard;

class StateMachine
{
	private $user;

	private $message;

	private $responseText;

	public function __construct(User $user, string $message, ResponseText $responseText)
	{
		$this->user = $user;
		$this->message = $message;
		$this->responseText = $responseText;
	}

	public function handle() : array
	{
		$state = $this->user->state->get();

		switch ($state)
		{
			default:
				return array();
			case 'time':
				return $this->time();
		}
	}

	private function time()
	{
		$time_set = $this->user->setTime($this->message);

		if (!$time_set)
		{
			$text = $this->responseText->get('time_set_error');

			$telegramTextModel = new TelegramText($text, '');
			return $telegramTextModel->getData();
		}

		$text = $this->responseText->get('time_set_success');

		$telegramTextModel = new TelegramText($text, '');

		$this->user->state->clear();
		
		return $telegramTextModel->getData();
	}
}