<?php

namespace App\Repository;

use App\Entity\Resume;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Resume>
 *
 * @method Resume|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resume|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resume[]    findAll()
 * @method Resume[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResumeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resume::class);
    }

    public function save(Resume $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Resume $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws Exception
     */
    public function sentResumeStat(): array
    {
        try {
            $conn = $this->getEntityManager()->getConnection();
            $sql = "SELECT DISTINCT(resume_id) as resume, r.position as position, COUNT(company_id) as numberOfCompanies
                    FROM send_resume JOIN resume r on r.id = send_resume.resume_id
                    GROUP BY resume_id;";
            $stmt = $conn->prepare($sql);
            $result = $stmt->executeQuery();
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $result->fetchAllAssociative();
    }
}
