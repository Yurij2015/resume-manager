<?php

namespace App\Repository;

use App\Entity\SendResume;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Throw_;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<SendResume>
 *
 * @method SendResume|null find($id, $lockMode = null, $lockVersion = null)
 * @method SendResume|null findOneBy(array $criteria, array $orderBy = null)
 * @method SendResume[]    findAll()
 * @method SendResume[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SendResumeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SendResume::class);
    }

    public function save(SendResume $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SendResume $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function __toString()
    {
        return $this->format("Y-m-d H:i:s");
    }

    /**
     * @throws Exception
     */
    public function sentResumeSave($resume, $selectedCountry): void
    {
        $dateCreate = (new \DateTime())->format('Y-m-d H:i:s');
        try {
            $conn = $this->getEntityManager()->getConnection();
            $sql = "
            INSERT INTO send_resume (resume_id, company_id, date_create)
            VALUES (:resume_id, :company_id, :date_create)";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':resume_id', $resume);
            $stmt->bindValue(':company_id', $selectedCountry);
            $stmt->bindValue(':date_create', $dateCreate);
            $stmt->executeQuery();
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
