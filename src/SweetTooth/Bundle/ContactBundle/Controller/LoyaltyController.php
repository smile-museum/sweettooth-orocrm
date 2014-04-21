<?php

namespace SweetTooth\Bundle\ContactBundle\Controller;

// use OroCRM\Bundle\CallBundle\Entity\Call;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class LoyaltyController extends Controller
{
    /**
     * @Route("/widget/points_balance", name="sweettooth_loyalty_widget_points_balance")
     * @Template("SweetToothContactBundle:Loyalty:widget/points_balance.html.twig")
     *
     * @param Request $request
     * @return array
     */
    public function pointsBalanceAction(Request $request)
    {
        $points_balance = '0';
        $params = $request->query->all();
        $contactId = $params['contactId'];

        $contactBroker = $this->get('sweettooth_binding.contact_broker');

        // Fetch contact's points balance from ST
        $binding = $contactBroker->retrieve($contactId);

        if ($binding && $binding->getRemoteObject()) {
            $points_balance = $binding->getRemoteObject()->points_balance;
        }

        return array(
            'points_balance' => $points_balance
        );
    }

    /**
     * @Route("/widget/points_history", name="sweettooth_loyalty_widget_points_history")
     * @Template("SweetToothContactBundle:Loyalty:widget/points_history.html.twig")
     *
     * @param Request $request
     * @return array
     */
    public function pointsHistoryAction(Request $request)
    {
        return array(
            'datagridParameters' => $request->query->all()
        );
    }
}
