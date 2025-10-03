<?php

namespace App\Repository;

use App\Entity\Burger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Oignon; 
use App\Entity\Sauce; 
use App\Entity\Pain; 
// use App\Entity\Image;
// use App\Entity\Commentaire;  

/**
 * @extends ServiceEntityRepository<Burger>
 */
class BurgerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Burger::class);
    }

    //    /**
    //     * @return Burger[] Returns an array of Burger objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Burger
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    public function findTopXBurgers(int $limit): array
{
    return $this->createQueryBuilder('b')
        ->orderBy('b.price', 'DESC') // Tri par prix descendant
        ->setMaxResults($limit)       // Limiter les résultats
        ->getQuery()
        ->getResult();
}

/**
     * Finds burgers containing a specific ingredient by entity class and name.
     *
     * @param string $ingredientEntityClass The fully qualified class name of the ingredient entity (e.g., App\Entity\Oignon).
     * @param string $ingredientName The name of the ingredient to search for.
     * @return Burger[] Returns an array of Burger objects.
     */
    public function findBurgersWithIngredient(string $ingredientEntityClass, string $ingredientName): array
    {
        $ingredientType = (new \ReflectionClass($ingredientEntityClass))->getShortName(); // e.g., 'Oignon'
        $relationProperty = strtolower($ingredientType);

        if (!property_exists(Burger::class, $relationProperty)) {
            throw new \InvalidArgumentException(sprintf('Burger entity does not have a relation named "%s".', $relationProperty));
        }

        return $this->createQueryBuilder('b') 
            ->join('b.' . $relationProperty, 'i') 
            ->where('i.name = :ingredientName')
            ->setParameter('ingredientName', $ingredientName)
            ->getQuery()
            ->getResult();
    }

     /**
     * Trouve les burgers qui ne contiennent pas un ingrédient spécifique.
     *
     * @param object $ingredient L'objet ingrédient (Oignon, Sauce, Pain, etc.)
     * @return array<Burger>
     * @throws \InvalidArgumentException Si l'objet passé n'est pas un ingrédient connu.
     */
    public function findBurgersWithoutIngredient(object $ingredient): array
    {
        // ... (votre code pour déterminer $relationProperty et $entityClass) ...

        // Utiliser instanceof pour vérifier le type d'ingrédient
        $relationProperty = null;
        $entityClass = null;

        if ($ingredient instanceof Oignon) {
            $relationProperty = 'oignon'; // <-- Changé en 'oignon' (singulier)
            $entityClass = Oignon::class;
        } elseif ($ingredient instanceof Sauce) {
            $relationProperty = 'sauce'; // <-- Changé en 'sauce' (singulier)
            $entityClass = Sauce::class;
        } elseif ($ingredient instanceof Pain) {
            $relationProperty = 'pain'; // <-- Changé en 'pain' (singulier)
            $entityClass = Pain::class;
        }
        // Ajoutez ici d'autres types d'ingrédients si vous en avez

        if ($relationProperty === null) {
            throw new \InvalidArgumentException(sprintf('Objet passé n\'est pas un ingrédient reconnu: %s.', get_class($ingredient)));
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $subQuery = $qb
            ->select('b_with_ingredient.id')
            ->from(Burger::class, 'b_with_ingredient')
            ->join('b_with_ingredient.' . $relationProperty, 'i_with')
            ->where('i_with.id = :ingredientId');

        $qb = $this->createQueryBuilder('b'); // Créez un nouveau QueryBuilder pour la requête principale

        $qb
            ->where($qb->expr()->notIn('b.id', $subQuery->getDQL()))
            ->setParameter('ingredientId', $ingredient->getId());

        return $qb->getQuery()->getResult();
    }

     /**
     * Finds burgers with a minimum number of ingredients.
     *
     * @return Burger[] Returns an array of Burger objects
     */
    public function findBurgersWithMinimumIngredients(int $minIngredients): array
    {
        $qb = $this->createQueryBuilder('b');

        $qb
            ->select('b')
            ->leftJoin('b.pain', 'p')
            ->leftJoin('b.sauce', 's')
            ->leftJoin('b.oignon', 'o')
            ->groupBy('b.id')
            ->having('SUM(CASE WHEN p.id IS NOT NULL THEN 1 ELSE 0 END) + SUM(CASE WHEN s.id IS NOT NULL THEN 1 ELSE 0 END) + SUM(CASE WHEN o.id IS NOT NULL THEN 1 ELSE 0 END) >= :minIngredients')
            ->setParameter('minIngredients', $minIngredients);

        return $qb->getQuery()->getResult();
    }



}
