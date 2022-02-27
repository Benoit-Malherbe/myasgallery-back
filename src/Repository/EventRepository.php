<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    // findRandom method is able to find a defined number of events
    public function findRandom($count) {

        $query = $this->createQueryBuilder('e')
            ->orderBy('RAND()')
            ->setMaxResults($count)
            ->getQuery();

        return $query->execute();
    }

    /**
     * Get all informations about one event
     * @return Event
     */
    public function findOneEventWithAllInfos(string $slug)
    {
        $entityManager = $this->getEntityManager();

        // We will use the DQL (Doctrine Query Language)
        $query = $entityManager->createQuery(
            'SELECT e, w, a
            FROM App\Entity\Event e
            LEFT JOIN e.artworks w
            LEFT JOIN e.artists a

        -- this parameter will forbid some DQL injections
            WHERE e.slug LIKE :slug'
        )->setParameter('slug', "%" . $slug . "%");

        // returns the selected Artwork Object

        return $query->getOneOrNullResult();
    }

    /**
     * Get all events who have a specific slug in a part of their name
     * @return Event
     */
    public function searchEvents(string $slug)
    {
        $entityManager = $this->getEntityManager();

        // We will use the DQL (Doctrine Query Language)
        $query = $entityManager->createQuery(
            'SELECT e, w, a
            FROM App\Entity\Event e
            LEFT JOIN e.artworks w
            LEFT JOIN e.artists a

        -- this parameter will forbid some DQL injections
            WHERE e.slug LIKE :slug OR e.country LIKE :slug'
        )->setParameter('slug', "%" . $slug . "%");

        // returns the selected Artwork Object
        return $query->getResult();
    }

    /**
     * Get all events with all informations
     * @return Event[]
     */
    public function findEventsWithAllInfos():array
    {
        $entityManager = $this->getEntityManager();

        // We will use the DQL (Doctrine Query Language)
        $query = $entityManager->createQuery(
            'SELECT e
            FROM App\Entity\Event e'
        );

        // returns the selected Artwork Object
        return $query->getResult();
    }

    /**
     * Get all events sorted by date
     * @return Event[]
     */
    public function findEventsByDate():array
    {
        $entityManager = $this->getEntityManager();

        // We will use the DQL (Doctrine Query Language)
        $query = $entityManager->createQuery(
            'SELECT e
            FROM App\Entity\Event e
            ORDER BY e.date ASC'
            );

        // returns the selected Artwork Object
        return $query->getResult();
    }

    /**
     * Get a defined number of events sorted by date
     * @return Event[]
     */
    public function findEventsByDateWithLimit($limit):array
    {
        // We will use the DQL (Doctrine Query Language)
        $query = $this->createQueryBuilder('e')
            ->where('e.date >= :now')
            ->setParameter('now', new \DateTime('now'))
            ->orderBy('e.date', 'ASC')
            ->setMaxResults($limit)
            ->getQuery();

        return $query->execute();
    }
}
