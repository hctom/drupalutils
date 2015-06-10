<?php

/**
 * @file
 * Drush commands for Drupal Utilities console application.
 */

/**
 * Implements hook_drush_command().
 */
function drupalutils_drush_command() {
  $items['drupalutils-features-revertable'] = array(
    'description' => 'Return revertable features.',
    'drupal dependencies' => array('features'),
    'examples' => array(
      'drush drupalutils-features-revertable' => 'Return a JSON encoded array of all names of features that need to be reverted.',
    ),
    'callback' => '_drupalutils_features_revertable',
  );

  return $items;
}

/**
 * Drush command callback; Return revertable features.
 *
 * @return string
 *   A JSON encoded array of names of features that need to be reverted.
 */
function _drupalutils_features_revertable() {
  module_load_include('inc', 'features', 'features.export');
  $features_to_revert = array();

  foreach (features_get_features(NULL, TRUE) as $module) {
    if ($module->status) {
      switch (features_get_storage($module->name)) {
        case FEATURES_OVERRIDDEN:
        case FEATURES_NEEDS_REVIEW:
        case FEATURES_REBUILDABLE:
          $features_to_revert[] = $module->name;
          break;
      }
    }
  }

  return drush_json_encode($features_to_revert);
}
