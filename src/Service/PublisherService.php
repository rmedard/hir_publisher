<?php
/**
 * Created by PhpStorm.
 * User: reberme
 * Date: 22/09/2017
 * Time: 12:00
 */

namespace Drupal\hir_publisher\Service;


use function count;
use Drupal;
use Drupal\Core\Entity\EntityTypeManager;

class PublisherService {

    protected $entityTypeManager;

    /**
     * PublisherService constructor.
     *
     * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
     */
    public function __construct(EntityTypeManager $entityTypeManager) {
        $this->entityTypeManager = $entityTypeManager;
    }

    public function loadExpiredAdverts($date){
        $storage = $this->entityTypeManager->getStorage('node');
        $query = $storage->getQuery()
          ->condition('type', 'advert')
          ->condition('status', 1)
          ->condition('field_advert_expirydate', $date, '<');
        $expired_adverts_ids = $query->execute();
        if (isset($expired_adverts_ids) and count($expired_adverts_ids) > 0){
            Drupal::logger('hir_publisher')->debug('Found expired adverts: ' . count($expired_adverts_ids));
            return $storage->loadMultiple($expired_adverts_ids);
        } else {
            Drupal::logger('hir_publisher')->debug('No expired adverts found');
            return array();
        }
    }

}