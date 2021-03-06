<?php

namespace Drupal\singles\Controller;

use Drupal\node\NodeInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\singles\Service\Singles;

/**
 * Class OverviewController
 * @package Drupal\singles\Controller
 */
class OverviewController extends ControllerBase
{
    /**
     * @var Singles
     */
    protected $singles;

    /**
     * OverviewController constructor.
     * @param Singles $singles
     */
    public function __construct(
        Singles $singles
    ) {
        $this->singles = $singles;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     *
     * @return static
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('singles')
        );
    }

    /**
     * @return mixed
     */
    public function overview()
    {
        $output['table'] = [
            '#type' => 'table',
            '#header' => [
                $this->t('Title'),
                $this->t('Type'),
                $this->t('Operations'),
            ],
            '#empty' => $this->t('No singles found.'),
            '#sticky' => true,
        ];

        /** @var NodeTypeInterface $item */
        foreach ($this->singles->getAllSingles() as $item) {
            /** @var NodeInterface $node */
            $node = $this->singles->getSingleByBundle($item->id());
            if ($node) {
                $operations = $this->entityTypeManager()->getListBuilder('node')->getOperations($node);

                $output['table'][$item->id()]['title'] = [
                    '#markup' => $node->label(),
                ];

                $output['table'][$item->id()]['type'] = [
                    '#markup' => $item->label(),
                ];

                $output['table'][$item->id()]['operations'] = [
                    '#type' => 'operations',
                    '#subtype' => 'node',
                    '#links' => $operations,
                ];
            }
        }

        return $output;
    }
}
