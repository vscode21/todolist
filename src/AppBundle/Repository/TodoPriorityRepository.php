<?php

namespace AppBundle\Repository;

use \Doctrine\ORM\EntityRepository;
use AppBundle\Entity\TodoPriority;

/**
 * TodoPriority Repository
 */
class TodoPriorityRepository extends EntityRepository
{
    private $initValues = array('Normal', 'Low', 'High');

    public function createTable()
    {
        //php bin/console doctrine:schema:update --dump-sql

        $connection = $this->getEntityManager()
            ->getConnection();

        $schemaManager = $connection->getSchemaManager();

        if ($schemaManager->tablesExist(array('todo_priority')) !== true) {
            $qry = <<<QRY
CREATE TABLE todo_priority (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
QRY;

            $connection->exec($qry);

            $qry = <<<QRY
ALTER TABLE todo ADD CONSTRAINT FK_5A0EB6A062A6DC27 FOREIGN KEY (priority) REFERENCES todo_priority (id);
QRY;

            $connection->exec($qry);

            $this->initTable();

            return true;
        }

        return $this->initTable();
    }

    public function initTable()
    {
        $items = $this->findall();

        if (count($items) < 1) {
            $em = $this->getEntityManager();
            for ($i = 0; $i < count($this->initValues); ++$i) {
                $v = new TodoPriority();
                $v->setName($this->initValues[$i]);
                $em->persist($v);
            }
            $em->flush();

            return true;
        }

        return false;
    }

    public function findAllItems($options = false)
    {
        /*
        0 => TodoPriority {
            id: 1
            name: "Normal"
            todos: PersistentCollection {
                ...
            }
        }
        ...
        */
        $this->createTable();

        $itms = $this->findall();

        if ($options === true) {
            $items = array();
            foreach ($itms as $val) {
                $items[$val->getName()] = $val;
            }

            return $items;
        }

        return $itms;
    }
}