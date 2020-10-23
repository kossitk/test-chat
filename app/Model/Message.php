<?php


namespace App\Model;


use App\Database\MyPdo;
use Ramsey\Uuid\Uuid;

class Message extends Model
{
    protected $table = 'message';
    protected $usersTable = 'user';

    public function addMessage($data)
    {
        $pdo = $this->connexion;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO `" . $this->table . "` (`content`, `user_id`, `chat_id`, `created_on`) VALUES (:content, :user, :chat, :created_on)");
            $stmt->bindValue(":content", $data['content']);
            $stmt->bindValue(":user", $data['user']);
            $stmt->bindValue(":chat", $data['chat']);
            $stmt->bindValue(":created_on", date(self::MYSQL_DATE_TIME_FORMAT));
            if ($stmt->execute()){
                // On new message do some stuffs
                $chatModel = new Chat();
                $chatModel->updateChat($data['chat']);
                $memberModel = new Member();
                $memberModel->increaseUnread($data['chat'], $data['user']);
            }
        } catch (\PDOException $e) {
            error_log('Message Model -> addMessage : ' . $e->getMessage() . " \n " . print_r($data, true));
            if ($pdo->inTransaction()) {
                $pdo->rollback();
            }

            return false;
        }
    }

    public function getMessages($chat, $date, $direction = 'before', $max = 50)
    {
        $stmt = $this->connexion->prepare("
            SELECT  m.content, m.created_on, u.`pseudo`, u.`uuid` as user_uuid
            FROM `" . $this->table . "` AS t 
                INNER JOIN `" . $this->usersTable . "` as u on u.id = m.user_id
            WHERE m.created_on " . ($direction === 'before' ? ' < ' : ' > ') . " :selectedDate
                AND m.chat_id = :chatId
            ORDER BY m.created_on DESC "
            . ($max > 0 ? " LIMIT 0, " . $max : "")

        );
        $stmt->bindParam(":selectedDate", $date);
        $stmt->bindParam(":chatId", $chat);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);

        return $stmt->fetchAll();
    }
}