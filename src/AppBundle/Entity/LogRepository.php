<?php

namespace AppBundle\Entity;

use AppBundle\Collection\AppEvent;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;

class LogRepository extends EntityRepository
{
    public function fetchLastValidated($group)
    {

        $criteria = new Criteria();
        $criteria->where($criteria->expr()->eq('action', AppEvent::REMOTE_USER_VALIDATE_ONEKEY));
        $criteria->orWhere($criteria->expr()->eq('action', AppEvent::REMOTE_USER_INVALIDATE_ONEKEY));
        $criteria->andWhere($criteria->expr()->eq('logGroup', $group));
        $criteria->orderBy(array('time' => 'DESC'));

        $logs = $this->matching($criteria)->getValues();

        return $logs;
    }

    public function countDistinctOnekeyValidationLogs($group = null)
    {
        $query = 'SELECT count(DISTINCT l.subject) total FROM AppBundle:Log l WHERE l.action IN (:action) ';

        $parameters = array(
            'action' => array(AppEvent::REMOTE_USER_VALIDATE_ONEKEY, AppEvent::REMOTE_USER_INVALIDATE_ONEKEY),
        );

        if ($group) {
            $query .= ' AND l.logGroup = :group ';
            $parameters['group'] = $group;
        }

        $query = $this->getEntityManager()->createQuery($query)->setParameters($parameters);

        // execute
        $logs = $query->getSingleScalarResult();
        return (int)$logs;
    }
}
