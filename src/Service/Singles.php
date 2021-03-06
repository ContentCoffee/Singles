<?php

namespace Drupal\singles\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\node\NodeInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\node\Entity\NodeType;
use Drupal\Core\State\StateInterface;

/**
 * Provides common functionality for content translation.
 */
class Singles
{

    /**
     * The entity type manager.
     *
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityTypeManager;

    /**
     * The state.
     *
     * @var \Drupal\Core\State\StateInterface
     */
    protected $state;

    /**
     * Constructs a WmContentManageAccessCheck object.
     *
     * @param EntityTypeManagerInterface $entityTypeManager
     * @param StateInterface $state
     */
    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        StateInterface $state
    ) {
        $this->entityTypeManager = $entityTypeManager;
        $this->state = $state;
    }

    /**
     * This functions checks that for each key there is a corresponding
     * entity in the given bundle, and creates one if it's not there.
     * @param NodeTypeInterface $type
     * @throws \Exception
     */
    public function checkSingle(NodeTypeInterface $type)
    {
        if ($this->isSingle($type)) {
            /** @var QueryInterface $query */
            $query = $this->entityTypeManager->getStorage('node')->getQuery();
            $snowFlakeCount = $query
                ->condition('type', $type->id())
                ->count()
                ->execute();

            if ($snowFlakeCount == 0) {
                /** @var NodeInterface $entity */
                $entity = $this
                    ->entityTypeManager
                    ->getStorage('node')
                    ->create([
                        'type' => $type->id(),
                        'title' => $type->label(),
                        'path' =>  ['alias' => '/' . str_replace("_", "-", $type->id())]
                    ]);
                $entity->save();
                $this->setSnowFlake($type, $entity);
            } elseif ($snowFlakeCount > 1) {
                throw new \Exception('Single Bundle with more then one entity.');
            }
        }
    }

  /**
   * Returns a loaded single node.
   *
   * @param NodeTypeInterface $type
   * @return bool|\Drupal\Core\Entity\EntityInterface|null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
    public function getSingle(NodeTypeInterface $type)
    {
        if ($id = $this->getSnowFlake($type)) {
            return $this->entityTypeManager->getStorage('node')->load($id);
        }
        return false;
    }

  /**
   * @param $bundle
   * @return bool|\Drupal\Core\Entity\EntityInterface|null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
    public function getSingleByBundle($bundle)
    {
        $types = $this->getAllSingles();
        return isset($types[$bundle]) ? $this->getSingle($types[$bundle]) : null;
    }

    /**
     * Check whether a bundle is single or not.
     *
     * @param NodeTypeInterface $type
     * @return bool
     */
    public function isSingle(NodeTypeInterface $type)
    {
        return $type->getThirdPartySetting('singles', 'isSingle', false);
    }

    /**
     * Get all single content types.
     * @return array|mixed
     */
    public function getAllSingles()
    {
        $list = &drupal_static(__FUNCTION__);
        if (!isset($list)) {
            $list = [];
            /** @var NodeTypeInterface $type */
            foreach (NodeType::loadMultiple() as $type) {
                if ($this->isSingle($type)) {
                    $list[$type->get('type')] = $type;
                }
            }
        }
        return $list;
    }

    /**
     * Set the snowflake entity id for a single bundle.
     *
     * @param NodeTypeInterface $type
     * @param NodeInterface $node
     */
    public function setSnowFlake(NodeTypeInterface $type, NodeInterface $node)
    {
        $this->state->set($this->getSnowFlakeKey($type), (int) $node->id());
    }

    /**
     * Get the current snowflake id for a single bundle.
     * @param NodeTypeInterface $type
     * @return integer
     */
    public function getSnowFlake(NodeTypeInterface $type)
    {
        return $this->state->get($this->getSnowFlakeKey($type), 0);
    }

    private function getSnowFlakeKey(NodeTypeInterface $type)
    {
        return 'singles.' . $type->id();
    }
}
