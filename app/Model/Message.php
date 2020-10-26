<?php


namespace App\Model;


use App\Database\MyPdo;
use Ramsey\Uuid\Uuid;

class Message extends Model
{
    protected $table = 'message';
    protected $usersTable = 'user';


    /**
     * @param $chat
     * @param $user
     * @param $content
     * @return bool
     */
    public function addMessage($chat, $user, $content)
    {
        $pdo = $this->connexion;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO `" . $this->table . "` (`content`, `user_id`, `chat_id`, `created_on`, `uuid`) VALUES (:content, :user, :chat, :created_on, :uuid)");
            $stmt->bindValue(":content", $content);
            $stmt->bindValue(":user", $user);
            $stmt->bindValue(":chat", $chat);
            $stmt->bindValue(":uuid", $this->getUuid());
            $stmt->bindValue(":created_on", date(self::MYSQL_DATE_TIME_FORMAT));
            if ($stmt->execute()){
                // On new message do some stuffs
                $chatModel = new Chat();
                $chatModel->updateChat($chat);
                $memberModel = new Member();
                $memberModel->increaseUnread($chat, $user);
                $pdo->commit();

                return true;
            }
        } catch (\PDOException $e) {
            error_log('Message Model -> addMessage : ' . $e->getMessage() . " \n " . print_r([$chat, $user, $content], true));
            if ($pdo->inTransaction()) {
                $pdo->rollback();
            }

            return false;
        }
    }

    public function getMessages(int $chat, string $lastMessage = null, bool $after = true, bool $reset = true, int $user = null,  int $max = 3)
    {
        $lastMessageData = null;
        $sql = "
            SELECT  m.`content`, m.created_on, u.`pseudo`, m.uuid as uuid, u.`uuid` as user_uuid
            FROM `" . $this->table . "` AS m 
                INNER JOIN `" . $this->usersTable . "` as u on u.id = m.user_id
            WHERE " ;
        if ($lastMessage && strlen($lastMessage) == 36){
            $lastMessageData = $this->findByUuid($lastMessage);
            if ($lastMessageData){
                $sql .= " m.id " .($after ? ' > ' : ' < ') . " :lastMessage AND ";
            }
        }
        $sql .= " m.chat_id = :chatId ";
        $sql .= "ORDER BY m.created_on DESC " . ($max > 0 ? " LIMIT 0, " . $max : "");

        $stmt = $this->connexion->prepare($sql);
        if ($lastMessageData){
            $stmt->bindValue(":lastMessage", $lastMessageData['id']);
        }
        $stmt->bindValue(":chatId", $chat);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);

        //die(print_r([$stmt->queryString, $chat, $lastMessageData], true) );
        $results = $stmt->fetchAll();
        if ($reset && $user && count($results) > 0){
            $memberModel = new Member();
            $memberModel->resetUnread($chat, $user);
        }
        return $after ? array_reverse($results) : $results;
    }
}