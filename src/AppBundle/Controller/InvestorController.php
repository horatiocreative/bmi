<?php

namespace AppBundle\Controller;

use AppBundle\Form\ProjectType;
use AppBundle\Form\InvestType;
use AppBundle\Util\Util;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class InvestorController extends BaseController
{
    /**
     * @Route("/invest", name="investment_opportunities")
     */
    public function viewInvestmentOpportunitiesAction(Request $request)
    {
        $authenticated = $this->get('session')->get('authenticated');
        if (!$authenticated) {
            return $this->redirectToRoute('homepage',array('required' => 'login'));
        }
        
       	$user = $this->get('user')->getUserInfo();
       	$accessError = '';
       	       	
       	if ($user['additional_type'] == '') {
       		$accessError = 'not_accredited';
       	}
       	elseif ($user['registration_complete'] != 1) {
       		$accessError = 'not_registered';
       	}
       	elseif ($user['has_been_approved'] != 1) {
       		$accessError = 'not_approved';
       	}
       	
        $offerings = $this->get('offering')->listOfferings(0,100,[],[]);
        
        if ($offerings['outcome'] == 'success') {
        	$offerings = $offerings['data']['list'];

            foreach ($offerings as $k=>$offering) {
            	$organizationResponse = $this->get('organization')->getOne($offering['organization_id']);
            	
            	$offerings[$k]['organization'] = $organizationResponse['data']['organization'];
            }        	
            
        } else {        
        	$offerings = array();
        }
        
        $this->params['access_error'] = $accessError;
        $this->params['offerings'] = $offerings;
        $this->params['menu_item']= 'investment_opportunities';
        return $this->render('AppBundle:Investor:invest.html.twig',$this->params);
    }
       
    /**
     * @Route("/company/{offering_id}", name="view_company")
     */
    public function viewOfferingAction($offering_id)
    {
        // Check user is logined
        $authenticated = $this->get('session')->get('authenticated');
        if (!$authenticated) {
            return $this->redirect($this->generateUrl('homepage', array('required' => 'login')));
        }

		$offering = array();
		$organization = array();
		$documents = array();
		$forum = array();
		$dealroom = array();
		$team = array();

		$response = $this->get('offering')->get($offering_id);
        
        if (!empty($response['outcome']) && $response['outcome'] == 'success') {
	
			$offering = $response['data']['offering'];
        
			$organization = $this->get('organization')->getOne($offering['organization_id']);

			foreach ($organization['data']['organization']['members'] as $k=>$member) {
				
				$user = $this->get('user')->getUserWithId($member['user_id']);
				
				if ($user['outcome'] == 'success') {
					$team[] = $user['data']['user'];
				}
			}
			
			// Get documents
			$documents = $this->get('document')->getOrganizationDocuments($organization['data']['organization']['id']);
			$documents = !empty($documents['data']['list']) ? $documents['data']['list'] : array();
			
			// Get deal room
			$dealroom = $this->get('dealroom')->getDealRoom($offering_id);
			$dealroom = !empty($dealroom['data']['list']) ? $dealroom['data']['list'] : array();
			
			// Get deal room access
			//$dealRoomAccessData = $this->get('dealroom')->getDealRoomAccess($dealroom['id']);
        }

        $investments = $this->get('investment')->selfInvestments();
        $requestSent = false;
        if(!empty($investments['data']['list'])){
            foreach($investments['data']['list'] as $investment){
                if($investment['offering_id'] == $offering_id && in_array($investment['life_cycle_stage'], [0, 2, 4])){
                    $requestSent = true;
                    break;
                }
            }
        }

        $this->params['team_members'] = $team;
        $this->params['organization'] = $organization['data']['organization'];
        $this->params['offering'] = $offering;
        $this->params['documents'] = $documents;
        $this->params['form'] = $this->createForm(new InvestType())->createView();
        $this->params['menu_item']= 'company_detail';
        $this->params['dealroom'] = $dealroom;
        $this->params['requestSent'] = $requestSent;
        return $this->render('AppBundle:Investor:company_detail.html.twig',$this->params);
    }


    /**
     * @Route("/request-dealroom-access/{offering_id}/{dealroom_id}", name="request_dealroom_access")
     *
     * @param Request $request     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function requestDealroomAccessAction(Request $request, $offering_id, $dealroom_id)
    {
        // Check user is logined
        $authenticated = $this->get('session')->get('authenticated');
        if (!$authenticated) {
            return $this->redirectToRoute('homepage',array('required' => 'login'));
        }

		$response = $this->get('dealroom')->requestAccess($offering_id, $dealroom_id);
		
        return $this->redirectToRoute('view_offering', array('offering_id' => $offering_id));
    }

    /**
     * @Route("/make-investment/{offering_id}", name="make-investment")
     *
     * @param Request $request
     * @param $offering_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function investAction(Request $request, $offering_id)
    {
        // Check user is logined
        $authenticated = $this->get('session')->get('authenticated');
        if (!$authenticated) {
            return $this->redirectToRoute('homepage',array('required' => 'login'));
        }
        $form = $this->createForm(new InvestType());
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $data = $form->getData();
            $data['investment_amount'] = 0;
            $data['life_cycle_stage'] = 0;
            $ret = $this->get('investment')->add($offering_id, $data);
            if (!empty($ret['outcome']) && $ret['outcome'] == 'success') {
                $this->addFlash('info', 'Your request has been successfully submitted.');
            } else {
                $this->addFlash('error', $ret['data']['user_message']);
            }
            $result = [
                'status' => 0,
                'url' => $this->generateUrl('view_company', ['offering_id' => $offering_id])
            ];
            $response = new JsonResponse();
            $response->setData($result);

            return $response;
        }
        return $this->redirectToRoute('view_company', array('offering_id' => $offering_id));
    }
      
}
