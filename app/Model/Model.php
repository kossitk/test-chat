<?php


namespace App\Model;


use App\Database\MyPdo;
use Ramsey\Uuid\Uuid;

abstract class Model
{
    const MYSQL_DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    protected $connexion;

    protected $table = '';

    public function __construct()
    {
        $this->connexion = MyPdo::getInstance();
    }

    /**
     * @param $id
     * @return bool|array
     */
    public function find($id)
    {
        $stmt = $this->connexion->prepare("SELECT * FROM `" . $this->table . "` WHERE id = :elementID ");
        $stmt->bindParam(":elementID", $id);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $results = $stmt->fetchAll();

        if (count($results) == 1){
            return $results[0];
        }
        else{
            return false;
        }
    }

    /**
     * @param $id
     * @return bool|array
     */
    public function findByUuid($uuid)
    {
        $stmt = $this->connexion->prepare("SELECT * FROM `" . $this->table . "` WHERE uuid = :uuid ");
        $stmt->bindParam(":uuid", $uuid);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $results = $stmt->fetchAll();

        if (count($results) == 1){
            return $results[0];
        }
        else{
            return false;
        }
    }

    /**
     * @param  array  $params
     * @param  array  $orderBy
     * @param  bool  $oneResult
     * @return array|false
     */
    public function findBy(array $params = [], array $orderBy = [], bool $oneResult = false)
    {

        $where = implode(' AND ', array_map(function ($key){
            return "`" . $key . "` = :$key";
        }, array_keys($params)));
        $order = implode(', ', array_map(function ($key){
            return "`" . $key . "` = :$key";
        }, array_keys($params)));
        $stmt = $this->connexion->prepare("SELECT * FROM `" . $this->table . "` " . $where . " " . $order);

        foreach ($params as $key => $value){
            $stmt->bindValue(":$key", $value);
        }

        try {
            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_ASSOC);

            if ($oneResult){
                $results = $stmt->fetchAll();
                if (count($results) == 1){
                    return $results[0];
                }
                else{
                    return false;
                }
            }

            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return string
     */
    protected function getUuid()
    {
        return Uuid::uuid4()->toString();
    }

}