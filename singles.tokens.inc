<?php

/**
 * @file
 * Builds placeholder replacement tokens for singles
 */

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\node\Entity\NodeType;

/**
 * Implements hook_token_info().
 *
 * @return array
 */
function singles_token_info()
{
    $types['singles'] = [
        'name' => t("Singles"),
        'description' => t("Singles tokens"),
    ];

    // Get all singles.
    $singles = [];
    /** @var EntityTypeInterface $type */
    foreach (\Drupal::service('singles')->getAllSingles() as $type) {
        $singles[$type->id() . ':url'] = [
            'name' => $type->label() . ' URL',
            'description' => t('The Alias of the single.')
        ];
    }

    return [
        'types' => $types,
        'tokens' => [
            'singles' => $singles,
        ],
    ];
}

/**
 * Implements hook_tokens().
 *
 * @return array
 */
function singles_tokens($type, $tokens, array $data = [], array $options = [])
{
    $replacements =[];

    if ($type == 'singles') {
        foreach ($tokens as $name => $original) {
            $split = explode(':', $name);

            // Load the content type
            $node = \Drupal::service('singles')->getSingleByBundle($split[0]);
            if ($node) {
                // Failsafe
                $replace = '';

                if ($split[1] == 'url') {
                    $canonical = '/node/' . $node->id();
                    $alias = \Drupal::service('path.alias_manager')->getAliasByPath($canonical);
                    if ($alias !== $canonical) {
                        $replace = $alias;
                    }
                }

                $replacements[$original] = $replace;
            }
        }
    }

    return $replacements;
}
