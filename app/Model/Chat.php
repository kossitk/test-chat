<?php


namespace App\Model;


use App\Database\MyPdo;
use Ramsey\Uuid\Uuid;

class Chat extends Model
{
    protected $table = 'chat';
    protected $membersTable = 'member';
    protected $usersTable = 'user';

    public function createChat($data)
    {
        $pdo = $this->connexion;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO `" . $this->table . "` (`private`, `uuid`, `admin`, `group_name`, `updated_on`, `created_on`) VALUES (:private, :uuid, :admin, :group_name, :updated_on, :updated_on)");
            $stmt->bindValue(":private", $data['private']);
            $stmt->bindValue(":admin", $data['admin']);
            $stmt->bindValue(":group_name", substr($data['group_name'], 0, 55));
            $stmt->bindValue(":uuid", $this->getUuid());
            $stmt->bindValue(":updated_on", date(self::MYSQL_DATE_TIME_FORMAT));
            if ($stmt->execute()){
                $chatId = $this->connexion->lastInsertId();
                $memberModel = new Member();
                foreach ($data['members'] as $member){
                    $memberModel->addMember($chatId, $member, true);
                }

                return $chatId;
            }
        } catch (\PDOException $e) {
            error_log('Chat Model -> createChat : ' . $e->getMessage() . " \n " . print_r($data, true));
            if ($pdo->inTransaction()) {
                $pdo->rollback();
            }

            return false;
        }
    }


    public function getUserChats($user)
    {
        $stmt = $this->connexion->prepare("
            SELECT  t.`private`, t.`uuid` as chat_uuid, t.`admin`, t.`group_name`, t.`updated_on`, u.`pseudo`, m.unread, u.`uuid` as user_uuid, u.id as user_id
            FROM `" . $this->table . "` AS t 
                INNER JOIN `" . $this->membersTable . "` AS m ON t.id = m.chat_id 
                INNER JOIN `" . $this->usersTable . "` as u on u.id = m.user_id
            WHERE t.id IN (SELECT DISTINCT m2.chat_id FROM `" . $this->membersTable . "` AS m2 WHERE m2.user_id = :member)
            ORDER BY t.updated_on DESC
        ");
        $stmt->bindParam(":member", $user);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);

        $chats = [];
        while ($row = $stmt->fetch())
        {
            // Create nested table with result
            if (!isset($chats[$row['chat_uuid']])) {
                $chats[$row['chat_uuid']] = [
                    'uuid'       => $row['chat_uuid'],
                    'private'    => $row['private'],
                    'admin'      => $row['admin'],
                    'updated_on' => $row['updated_on'],
                    'unread'     => 0,
                    'group_name' => $row['group_name'],
                    'members'    => [],
                ];
            }

            $chats[$row['chat_uuid']]['members'][$row['user_uuid']] = [
                'pseudo' => $row['pseudo'],
                'uuid'   => $row['user_uuid'],
            ];

            if ($user == $row['user_id']){
                $chats[$row['chat_uuid']]['unread'] = $row['unread'];
            }
            elseif ($row['private'] == 1){
                $chats[$row['chat_uuid']]['group_name'] = $row['pseudo'];
            }
        }

        return $chats;
    }


    public function updateChat($chat)
    {
        try {
            $stmt2 = $this->connexion->prepare("UPDATE `" . $this->table . "` SET `updated_on` = :updated_on WHERE `id` = :chat");
            $stmt2->bindParam(":chat", $chat);
            $stmt2->bindValue(":updated_on", date(self::MYSQL_DATE_TIME_FORMAT));
            $stmt2->execute();

            return true;
        } catch (\PDOException $e) {
            error_log('Chat Model -> updateChat : ' . $e->getMessage() . ", chat: " . $chat );
            return false;
        }
    }

}