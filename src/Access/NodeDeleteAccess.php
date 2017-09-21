<?php

namespace Drupal\singles\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\node\NodeInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\singles\Service\Singles;
use Drupal\node\Entity\NodeType;
use Drupal\Core\Session\AccountInterface;

/**
 * Class NodeDeleteAccess
 * @package Drupal\singles\Access
 */
class NodeDeleteAccess implements AccessInterface
{
    /** @var  Singles */
    private $singles;

    /**
     * NodeDeleteAccess constructor.
     * @param Singles $singles
     */
    public function __construct(
        Singles $singles
    ) {
        $this->singles = $singles;
    }

    /**
     * @param NodeInterface $node
     * @param AccountInterface $account
     * @return \Drupal\Core\Access\AccessResultAllowed|\Drupal\Core\Access\AccessResultForbidden|\Drupal\Core\Access\AccessResultNeutral
     */
    public function access(NodeInterface $node, AccountInterface $account)
    {
        if ($account->hasPermission('administer singles')) {
            return AccessResult::allowed();
        }

        /** @var NodeTypeInterface $type */
        $type = NodeType::load($node->bundle());

        if ($this->singles->isSingle($type)) {
            return AccessResult::forbidden();
        }

        return AccessResult::allowed();
    }
}
