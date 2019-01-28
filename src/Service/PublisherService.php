<?php
/**
 * Created by PhpStorm.
 * User: reberme
 * Date: 22/09/2017
 * Time: 12:00
 */

namespace Drupal\hir_publisher\Service;


use Drupal;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\Entity\Node;
use function count;

class PublisherService
{

    protected $entityTypeManager;

    /**
     * PublisherService constructor.
     *
     * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
     */
    public function __construct(EntityTypeManager $entityTypeManager)
    {
        $this->entityTypeManager = $entityTypeManager;
    }

    public function loadExpiredAdverts($date)
    {
        try {
            $storage = $this->entityTypeManager->getStorage('node');
            $query = $storage->getQuery()
                ->condition('type', 'advert')
                ->condition('status', Node::PUBLISHED)
                ->condition('field_advert_expirydate', $date, '<');
            $expired_adverts_ids = $query->execute();
            if (isset($expired_adverts_ids) and count($expired_adverts_ids) > 0) {
                return $storage->loadMultiple($expired_adverts_ids);
            } else {
                Drupal::logger('hir_publisher')->debug('No expired adverts found');
            }
        } catch (InvalidPluginDefinitionException $e) {
            Drupal::logger('hir_publisher')->error($e->getMessage());
        } catch (PluginNotFoundException $e) {
            Drupal::logger('hir_publisher')->error($e->getMessage());
        }
        return array();
    }

    public function loadNonMappedPropertyRequests()
    {
        try {

            $connection = \Drupal::database();
            $selectQuery = $connection->select('webform_submission', 'ws')
                ->fields('ws', array('sid'))
//                ->condition('webform_id', 'property_request_form')
//                ->addTag('is_pr_mapped')
            ;
            $selectQuery->where('webform_id = \'property_request_form\'');
            $selectQuery->addTag('is_pr_mapped');

//            $storage = $this->entityTypeManager->getStorage('webform_submission');
//
//            $query = $storage->getQuery()
//                ->condition('webform_id', 'property_request_form')
//                ->addTag('is_pr_mapped');
//            $ids = $query->execute();
            $ids = $selectQuery->execute()->fetchAll();
            if (isset($ids) && count($ids) > 0) {
//                return $storage->loadMultiple($ids);
                return Drupal\webform\WebformSubmissionInterface::loadMultiple($ids);
            } else {
                Drupal::logger('hir_publisher')
                    ->info('No non-mapped submissions found');
            }
        } catch (InvalidPluginDefinitionException $e) {
            Drupal::logger('hir_publisher')->error($e->getMessage());
        } catch (PluginNotFoundException $e) {
            Drupal::logger('hir_publisher')->error($e->getMessage());
        }
        return array();
    }
}
