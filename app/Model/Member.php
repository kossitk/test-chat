<?php


namespace App\Model;


use App\Database\MyPdo;
use Ramsey\Uuid\Uuid;

class Member extends Model
{
    protected $table = 'member';

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

}