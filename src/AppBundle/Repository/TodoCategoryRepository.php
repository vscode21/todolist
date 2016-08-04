<?php

namespace AppBundle\Repository;

use \Doctrine\ORM\EntityRepository;
use AppBundle\Entity\TodoCategory;

/**
 * TodoCategory Repository
 */
class TodoCategoryRepository extends EntityRepository
{
    private $initValues = array('Common');

    public function createTable()
    {
        //php bin/console doctrine:schema:update --dump-sql

        $connection = $this->getEntityManager()
            ->getConnection();

        $schemaManager = $connection->getSchemaManager();

        if ($schemaManager->tablesExist(array('todo_category')) !== true) {
            $qry = <<<QRY
CREATE TABLE todo_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
QRY;

            $connection->exec($qry);

            $qry = <<<QRY
ALTER TABLE todo ADD CONSTRAINT FK_5A0EB6A064C19C1 FOREIGN KEY (category) REFERENCES todo_category (id);
QRY;

            $connection->exec($qry);

            $this->initTable();

            return true;
        }

        return false;
    }

    public function initTable()
    {
        $items = $this->findall();

        if (count($items) < 1) {
            $em = $this->getEntityManager();
            for ($i = 0; $i < count($this->initValues); ++$i) {
                $v = new TodoCategory();
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
        0 => TodoCategory {
            id: 1
            name: "Work"
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