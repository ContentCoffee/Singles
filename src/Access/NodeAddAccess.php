<?php

namespace Drupal\singles\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\node\NodeInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\singles\Service\Singles;
use Drupal\node\Entity\NodeType;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Class NodeAddAccess
 * @package Drupal\singles\Access
 */
class NodeAddAccess implements AccessInterface
{
    /** @var  CurrentRouteMatch */
    private $currentRoute;

    /** @var  Singles */
    private $singles;

    /**
     * NodeDeleteAccess constructor.
     * @param Singles $singles
     */
    public function __construct(
        Singles $singles,
        CurrentRouteMatch $currentRoute
    ) {
        $this->singles = $singles;
        $this->currentRoute = $currentRoute;
    }

    /**
     * @param NodeInterface $node
     * @return \Drupal\Core\Access\AccessResultForbidden|\Drupal\Core\Access\AccessResultNeutral
     */
    public function access()
    {
        /** @var NodeTypeInterface $type */
        $type = $this->currentRoute->getParameter('node_type');

        if ($type && $this->singles->isSingle($type)) {
            return AccessResult::forbidden();
        }

        return AccessResult::allowed();
    }
}
