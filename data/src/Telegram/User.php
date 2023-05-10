<?php
namespace Unclebot\Telegram;

class User
{
	public $settings;

	public $state;

	private $db;

	private $chatID = '';

	private $nickname = '';

	private $name = '';

	private $surname = '';

	public function __construct(\PDO $db)
	{
		$this->db = $db;
	}

	public function getChatID() : string
	{
		return $this->chatID;
	}

	public function getNickname() : string
	{
		return $this->nickname;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function getSurname() : string
	{
		return $this->surname;
	}

	public function register(string $chat_id, string $nickname = '', string $name = '', string $surname = ''): bool
	{
		$query = $this->db->prepare('
			INSERT INTO users
				(chat_id, nickname)
			VALUES
				(?, ?)
			ON DUPLICATE KEY UPDATE `status` = "active"
		');

		$this->chatID = $chat_id;
		$this->nickname = $nickname;
		$this->name = $name;
		$this->surname = $surname;

		$this->settings = new User\Settings($this->db, $chat_id);
		$this->state = new User\State($this->db, $chat_id);

		$this->setTime('09:00');

		return $query->execute(array(
			$chat_id,
			$nickname
		));
	}

	public function setTime(string $time): bool
	{
		$time = str_replace('`', '', $time);

		if (strlen($time) > 5 || strripos($time, ':') === false)
		{
			return false;
		}

		$query = $this->db->prepare('
			INSERT INTO users_time 
			(user, time)
			VALUES
			(?, ?)
			ON DUPLICATE KEY
				UPDATE `time` = ?
		');
		
		return $query->execute(array(
			$this->chatID, 
			$time,
			$time
		));
	}

	public function getTime(): string
	{
		$query = $this->db->prepare('
			SELECT
				time
			FROM
				users_time
			WHERE
				user = ?
		');
		$query->execute(array($this->chatID));

		return $query->fetch()['time'] ?? '';
	}
}