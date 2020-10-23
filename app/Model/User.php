<?php


namespace App\Model;


use App\Database\MyPdo;
use Ramsey\Uuid\Uuid;

class User extends Model
{
    protected $table = 'user';

    const INACTIVE_TIME_OUT = '-5 min';

    public function createUser($data)
    {
        try {
            $stmt = $this->connexion->prepare("INSERT INTO `" . $this->table . "` (`email`, `password`, `pseudo`, `uuid`, `roles`, `last_action`) VALUES (:email, :password, :pseudo, :uuid, :roles, :last_action)");
            $stmt->bindValue(":email", $data['email']);
            $stmt->bindValue(":password", $data['password']);
            $stmt->bindValue(":pseudo", $data['username']);
            $stmt->bindValue(":roles", $data['roles']);
            $stmt->bindValue(":uuid", $this->getUuid());
            $stmt->bindValue(":last_action", date(self::MYSQL_DATE_TIME_FORMAT));
            if ($stmt->execute()){
                return ['status' => true, 'id' => $this->connexion->lastInsertId()];
            }
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                return ['status' => false, 'message' => 'Another account exist with the same email or pseudo'];
            } else {
                return ['status' => false, 'message' => 'An error occur. Please retry later.'];
            }
        }
        return false;
    }




    public function lastActionUpdate($user)
    {
        try {
            $stmt2 = $this->connexion->prepare("UPDATE `" . $this->table . "` SET `last_action` = :last_action WHERE `id` = :user");
            $stmt2->bindParam(":user", $user);
            $stmt2->bindValue(":last_action", date(self::MYSQL_DATE_TIME_FORMAT));
            $stmt2->execute();

            return true;
        } catch (\PDOException $e) {
            error_log('User Model -> lastActionUpdate : ' . $e->getMessage() . ", user: " . $user );
            return false;
        }
    }


    public function getConnectedUsers()
    {
        $stmt = $this->connexion->prepare("
            SELECT  u.`pseudo`, u.`uuid`
            FROM `" . $this->table . "` AS t 
            WHERE m.last_action > :timeout
            ORDER BY m.last_action DESC "
        );
        $date = new \DateTime();
        $date->modify(self::INACTIVE_TIME_OUT);
        $stmt->bindValue(":timeout", $date->format(self::MYSQL_DATE_TIME_FORMAT));
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);

        return $stmt->fetchAll();

    }
}