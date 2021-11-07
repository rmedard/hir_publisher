<?php

namespace Drupal\hir_publisher\Plugin\QueueWorker;

use Drupal\Core\Annotation\QueueWorker;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\node\Entity\Node;

/**
 * @package Drupal\hir_publisher\Plugin\QueueWorker
 * @QueueWorker(
 *  id = "update_adverts_processor",
 *  title = "Update Adverts Queue Worker",
 *  cron = {"time" = 600}
 * )
 */
class UpdateAdvertQueueWorker extends QueueWorkerBase
{

  public function processItem($data)
  {
    $advert = Node::load($data);
    $advert->set('field_advert_furnished', 0);
    $advert->save();
    \Drupal::logger('Updator')->info('Updated Advert ' . $data);
  }
}
