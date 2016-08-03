<?php

namespace AppBundle\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * TodoRepository
 */
class TodoRepository extends EntityRepository
{
    public function createTable()
    {
        //php bin/console doctrine:schema:update --dump-sql

        $connection = $this->getEntityManager()
            ->getConnection();

        $schemaManager = $connection->getSchemaManager();

        if ($schemaManager->tablesExist(array('todo')) !== true) {
            $qry = <<<QRY
CREATE TABLE todo (id INT AUTO_INCREMENT NOT NULL, category INT DEFAULT NULL, priority INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(65534) NOT NULL, date_created DATETIME NOT NULL, date_due DATETIME NOT NULL, INDEX IDX_5A0EB6A064C19C1 (category), INDEX IDX_5A0EB6A062A6DC27 (priority), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
QRY;

            $connection->exec($qry);

            return true;
        }

        return false;
    }
}