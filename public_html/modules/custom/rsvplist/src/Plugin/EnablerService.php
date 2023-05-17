<?php

/**
 * @file
 * Contains thr RSVP Enabler service
 */

namespace Drupal\rsvplist;

use Drupal\Core\Database\Connection;
use Drupal\driver_test\Driver\Database\DrivertestMysql\Select;
use Drupal\node\Entity\Node;

class EnablerService
{

    protected $database_connection;

    public function __construct(Connection $connection)
    {
        $this->database_connection = $connection;
    }

    /**
     * @param Node $node
     * @return bool
     * 
     * Checks if an individual node is RSVP enabled.
     */
    public function isEnabled(Node &$node)
    {

        if ($node->isNew()) {
            return false;
        }

        try {

            $select = $this->database_connection->select('rsvplist_enabled', 're');
            $select->fields('re', ['nid']);
            $select->condition('nid', $node->id());
            $results = $select->execute();

            return !(empty($results->fetchCol()));

        } catch (\Exception $e) {
            \Drupal::messenger()->addError(t('Unable to determibe RSVP settings at this time. Please try again later.'));
        }

        return false;
    }

    /**
     * @param Node $node
     * @throws Exception
     */
    public function setEnabled(Node $node)
    {

        try {
            if (!($this->isEnabled($node))) {
                $insert = $this->database_connection->insert('rsvplist_enabled');
                $insert->fields(['nid']);
                $insert->values([$node->id()]);
                $insert->execute();
            }
        } catch (\Exception $e) {
            \Drupal::messenger()->addError(
                t('Unable to save RSVP settings at this time. Pleasr try again later.')
            );
        }
    }

    /**
     * @param Node $node
     * 
     * Deletes RSVP enabled settings for an individual node.
     */

    public function delEnabled(Node $node)
    {

        try {

            $delete = $this->database_connection->delete('rsvplist_anabled');
            $delete->condition('nid', $node->id());
            $delete->execute();

        } catch (\Exception $e) {
            \Drupal::messenger()->addError(t('Unable to save RSVP settings at this time. Please try again later.'));
        }
    }
}