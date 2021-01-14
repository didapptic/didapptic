<?php
declare(strict_types=1);


namespace Didapptic\Repository;


use DateTime;
use Didapptic\Object\Role;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use doganoo\PHPUtil\Storage\PDOConnector;
use PDO;

class RoleRepository {

    /** @var PDOConnector */
    private $connector;

    public function __construct(PDOConnector $connector) {
        $this->connector = $connector;
        $this->connector->connect();
    }

    public function getAll(): ArrayList {
        $list      = new ArrayList();
        $sql       = "
                    SELECT
                        r.`id`
                        , r.`name`
                        , r.`create_ts`
                    FROM `role` r
        ";
        $statement = $this->connector->prepare($sql);

        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_BOTH)) {
            $role = new Role();
            $role->setId((int) $row[0]);
            $role->setName($row[1]);
            $dateTime = new DateTime();
            $dateTime->setTimestamp((int) $row[2]);
            $role->setCreateTs($dateTime);
            $list->add($role);
        }
        return $list;
    }

}
