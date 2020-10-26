<?php


namespace App\Model;


use App\Database\MyPdo;
use Ramsey\Uuid\Uuid;

class Member extends Model
{
    protected $table = 'member';
    protected $chatTable = 'chat';

    /**
     * @param $chat
     * @param $user
     * @param $hasTransaction
     * @return bool
     */
    public function addMember($chat, $user, $hasTransaction = false)
    {
        try {
            $stmt2 = $this->connexion->prepare("INSERT INTO `" . $this->table . "` (`chat_id`, `user_id`) VALUES (:chat, :user)");
            $stmt2->bindParam(":chat", $chat);
            $stmt2->bindParam(":user", $user);
            $stmt2->execute();

            return true;
        } catch (\PDOException $e) {
            error_log('Member Model -> addMember : ' . $e->getMessage() . ", chat: " . $chat . ", user: " . $user);
            if ($hasTransaction){
                throw $e;
            }
            return false;
        }
    }

    public function increaseUnread($chat, $user)
    {
        try {
            $stmt2 = $this->connexion->prepare("UPDATE `" . $this->table . "` SET `unread` = `unread` + 1 WHERE `chat_id` = :chat AND user_id <> :user");
            $stmt2->bindParam(":chat", $chat);
            $stmt2->bindParam(":user", $user);
            $stmt2->execute();

            return true;
        } catch (\PDOException $e) {
            error_log('Member Model -> increaseUnread : ' . $e->getMessage() . ", chat: " . $chat . ", user: " . $user);
            throw $e;
        }
    }


    public function getUnreadCounter($user)
    {
        try {
            $stmt2 = $this->connexion->prepare("select t.uuid as chat, m.unread as unread FROM `" . $this->table . "` m INNER JOIN  `" . $this->chatTable . "` t on t.id = m.chat_id WHERE user_id = :user");
            $stmt2->bindParam(":user", $user);
            $stmt2->execute();
            $stmt2->setFetchMode(\PDO::FETCH_ASSOC);

            return $stmt2->fetchAll();
        } catch (\PDOException $e) {
            error_log('Member Model -> getUnreadCounter : ' . $e->getMessage() . ", user: " . $user);
            return [];
        }
    }

    public function resetUnread($chat, $user)
    {
        try {
            $stmt2 = $this->connexion->prepare("UPDATE `" . $this->table . "` SET `unread` = 0 WHERE `chat_id` = :chat AND user_id = :user");
            $stmt2->bindParam(":chat", $chat);
            $stmt2->bindParam(":user", $user);
            $stmt2->execute();

            return true;
        } catch (\PDOException $e) {
            error_log('Member Model -> increaseUnread : ' . $e->getMessage() . ", chat: " . $chat . ", user: " . $user);
            throw $e;
        }
    }

}