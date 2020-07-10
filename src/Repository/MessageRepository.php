<?php

namespace PiedWeb\ConversationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PiedWeb\ConversationBundle\Entity\MessageInterface as Message;

/*
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function getMessagesPublishedByReferring(string $referring, $orderBy = 'createdAt DESC')
    {
        $orderBy = explode(' ', $orderBy);

        $q = $this->createQueryBuilder('m')
            ->andWhere('m.publishedAt is NOT NULL')
            ->andWhere('m.referring =  :referring')
            ->setParameter('referring', $referring)
            ->orderBy('m.'.$orderBy[0], $orderBy[1])
            ->getQuery();

        return $q->getResult();
    }
}
