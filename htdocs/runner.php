<?php
require_once dirname(__FILE__) . '/../data/vendor/autoload.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use \Unclebot\Config;
use \Unclebot\Utils\Database;
use \Unclebot\Wikipedia\Parser;
use \Unclebot\Telegram\Response;
use \Unclebot\Telegram\Models\Text;

$config = Config::getInstance();
$db = Database::connect($config->db);

$query = $db->prepare('
    SELECT
        user
    FROM
        users_time
    WHERE time = ?
');
$query->execute(array(date('h:i')));
#$query->execute(array());

while ($row = $query->fetch())
{
    $text = new Text(Parser::getRandomUrl(), '', false);

    Response::addChatId($row['user']);
    Response::send($text->getData());
}