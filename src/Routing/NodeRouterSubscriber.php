<?php

namespace Drupal\singles\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class NodeRouterSubscriber extends RouteSubscriberBase
{
    public function alterRoutes(RouteCollection $collection)
    {
        $route = $collection->get('entity.node.delete_form');
        $route->setRequirement('_singles_node_delete_access_check', 'true');

        $route = $collection->get('node.add');
        $route->setRequirement('_singles_node_add_access_check', 'true');
    }
}
