<?php

/**
 * @file
 * Creates a block which displays the RSVP form contained in RSVPForm.php.
 */

namespace Drupal\rsvplist\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;


/**
 * @Block(
 *   id = "rsvp_block",
 *   admin_label = @Translation("RSVP Block"),
 * )
 */

class RSVPBlock extends BlockBase
{

    /**
     * {@inhiritdoc}
     */
    public function build()
    {
        return \Drupal::formBuilder()->getForm('Drupal\rsvplist\Form\RSVPForm');
    }

    /**
     * {@inheritDoc}
     */
    public function blockAccess(AccountInterface $account)
    {
        //If viewing the node return fully liaded node object.
        $node = \Drupal::routeMatch()->getParameter('node');

        if (!(is_null($node))) {
            $enabler = \Drupal::service('rsvplist.enabler');

            if ($enabler->isEnabled($node)) {
                return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
            }
        }

        return AccessResult::forbidden();
    }
}