<?php

namespace AppBundle\Controller;

use AppBundle\Util\Util;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\CompleteRegistrationInvestorType;
use AppBundle\Form\UserType;
use AppBundle\Form\UserCustomInfoType;
use AppBundle\Form\OrganizationType;
use AppBundle\Form\OfferingType;
use AppBundle\Form\SignInType;
use AppBundle\Form\AccreditationType;

class ProfileController extends BaseController
{
    /**
     * @Route("/accreditation", name="accreditation")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function accreditationAction(Request $request)
    {
        // Check user is logined
        $authenticated = $this->get('session')->get('authenticated');
        if (!$authenticated) {
            return $this->redirectToRoute('homepage',array('required' => 'login'));
        }
        $user = $this->get('user')->getUserInfo();
                        
    	// If user has completed Accreditation and Completed Registration then go to My Profile        
        if ($user['additional_type'] != '' && $user['registration_complete'] == 1) {
        	return $this->redirect($this->generateUrl('my_profile'));
        }
        
        // If user has completed Accreditation but not Registration then go to Complete Registration
        elseif ($user['additional_type'] != '') {
        	return $this->redirect($this->generateUrl('complete_registration'));
        }
        
        // else additional_type is blank so continue to do accreditation
        $form = $this->createForm(new AccreditationType($user));
        if($request->getMethod() == 'POST'){
            $form->handleRequest($request);
            $userData = $form->getData();
            if(!empty($userData)){
                $response = $this->get('user')->update($userData);
                if (!empty($response['outcome']) && $response['outcome'] == 'success') {
                    return $this->redirect($this->generateUrl('complete_registration'));
                }
            }
        }

        $this->params['form'] = $form->createView();
        $this->params['menu_item']= 'complete_registration';
        return $this->render('AppBundle:Profile:accreditation.html.twig', $this->params);
    }


    /**
     * @Route("/complete-registration", name="complete_registration")
     */
    public function completeRegistrationAction(Request $request)
    {
        // Check user is logined
        $authenticated = $this->get('session')->get('authenticated');
        if (!$authenticated) {
            return $this->redirectToRoute('homepage',array('required' => 'login'));
        }
        $user = $this->get('user')->getUserInfo();
        if ($user['registration_complete'] == 1) {
        	return $this->redirect($this->generateUrl('my_profile'));
        }
        $userFillout = [];
        $userFillout['given_name'] = $user['given_name'];
        $userFillout['family_name'] = $user['family_name'];
        $userFillout['phone_1'] = $user['phone_1'];
        $userFillout['phone_2'] = $user['phone_2'];
        $userFillout['birth_date'] = $user['birth_date'];
        $userFillout['nationality'] = $user['nationality'];
        $userFillout['tax_id'] = $user['tax_id'];
        $userFillout['address'] = $user['address'];
        $userForm = $this->createForm(new UserType(), $userFillout);
        if($request->getMethod() == 'POST'){
            $userForm->handleRequest($request);
            $userData = $userForm->getData();
            $userData = Util::arrayFilterRecursive($userData);
            if(!empty($userData)){
                $response = $this->get('user')->update($userData);
                if (!empty($response['outcome']) && $response['outcome'] == 'success') {
                    $response = $this->get('user')->markSelfRegistrationCompleted();
                    if (!empty($response['outcome']) && $response['outcome'] == 'success') {
                        return $this->redirect($this->generateUrl('my_profile'));
                    }
                }
            }
        }

        $this->params['form'] = $userForm->createView();
        $this->params['menu_item']= 'complete_registration';
        return $this->render('AppBundle:Profile:complete_registration.html.twig',$this->params);
    }

    /**
     * @Route("/my-profile", name="my_profile")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function myProfileAction(Request $request)
    {
        // Check user is logined
        $authenticated = $this->get('session')->get('authenticated');
        if (!$authenticated) {
            return $this->redirectToRoute('homepage',array('required' => 'login'));
        }
        $user = $this->get('user')->getUserInfo();
                                                
    	// If user has not completed Accreditation then go to Accreditation      
        if ($user['additional_type'] == '') {
        	return $this->redirect($this->generateUrl('accreditation'));
        }
        $userInfo = $this->get('user')->getUserInfo();

        foreach( $userInfo['address'] as $type => $value)
        {
            if($type == 'building')
                $userInfo['address']['building'] = $value;
            if($type == 'postal_code')
                $userInfo['address']['postal_code'] = $value;
            if($type == 'city')
                $userInfo['address']['city'] = $value;
            if($type == 'region')
                $userInfo['address']['region'] = $value;
        }

        $userInfoForm = $this->createForm(new UserType(), $userInfo);
        $userInfoForm->setData($userInfo);
        foreach( $userInfo['info'] as $key => $val)
        {
            if ($val['type'] === 'organization_name') {
                $userInfoForm->get('organization_name')->setData($val['value']);
            }
            if ($val['type'] === 'position') {
                $userInfoForm->get('position')->setData($val['value']);
            }
        }
        
        $form = $this->createForm(new UserCustomInfoType());
        $orgForm = $this->createForm(new OrganizationType());
        
        if('POST' === $request->getMethod())
        {
            if ($request->request->has($userInfoForm->getName()))
            {
                $userInfoForm->handleRequest($request);
                $userData = $userInfoForm->getData();
                $updatedData['address'] = $userData['address'];
                $updatedData['biography'] = $userData['biography'];
                $updatedData['phone_1'] = $userData['phone_1'];
                unset($userData);
                $response = $this->get('user')->update($updatedData);
                if (!empty($response['outcome']) && $response['outcome'] == 'success') {
                    $this->get('session')->getFlashBag()->add('info', "Your profile info was successfully updated.");
                }else{
                    $this->get('session')->getFlashBag()->add('error', $response['data']['user_message']);
                }
                return $this->redirect($this->generateUrl('my_profile'));
            }
            
            else if ($request->request->has($orgForm->getName())) {
            
            		$orgForm->handleRequest($request);

					$organizationData = $orgForm->getData();
					
					$this->get('organization')->create($organizationData);
            }
            
            else if ($request->request->has($qnaForm->getName())) {
            
            		$qnaForm->handleRequest($request);

					$qnaData = $qnaForm->getData();
					
            }            
		}
                                
        // Get my documents
        $documentsData = $this->get('user')->getSelfDocuments();
        $documents = array();
        if (!empty($documentsData['outcome']) && $documentsData['outcome'] == 'success') {
            $documents = $documentsData['data']['list'];
        }
                                
        return $this->render('AppBundle:Profile:my_profile.html.twig',array(
            'userInfoForm' => $userInfoForm->createView(),
            'user' => $userInfo,
            'documents' => $documents,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/my-investments", name="my_investments")
     */    
    public function myInvestmentsAction()
	{
        // Check user is logined
        $authenticated = $this->get('session')->get('authenticated');
        if (!$authenticated) {
            return $this->redirect($this->generateUrl('homepage', array('required' => 'login')));
        }
		
        // Get my investments
        $investmentsData = $this->get('investment')->selfInvestments();
        $investments = [];
        if (!empty($investmentsData['outcome']) && $investmentsData['outcome'] == 'success') {
            $investments = $investmentsData['data']['list'];
            $temp = false;
            foreach ($investments as $k => $investment) {
                if($temp == $investment['offering_id']){
                    unset($investments[$k]);
                    continue;
                }
                $temp = $investment['offering_id'];
            	$offeringResponse = $this->get('offering')->get($investment['offering_id']);
            	if ($offeringResponse['outcome'] == 'success') {
            		$investments[$k]['offering'] = $offeringResponse['data']['offering'];
            		$organizationResponse = $this->get('organization')->getOne($offeringResponse['data']['offering']['organization_id']);
            		$investments[$k]['offering']['organization'] = $organizationResponse['data']['organization'];
            	}
            }
        }		

        return $this->render('AppBundle:Profile:my_investments.html.twig',
            array(
				'investments' => $investments,
				'menu_item' => 'my_investments',
            ));
    }        
        
    public function editProfileAction(Request $request)
    {
        $userInfo = $this->get('user')->getUserInfo();
        $userInfoForm = $this->createForm(new UserType());
        $userInfoForm->setData($userInfo);
        foreach( $userInfo['address'] as $type => $value)
        {
            if($type == 'building')
                $userInfoForm->get('address__building')->setData($value);
            if($type == 'postal_code')
                $userInfoForm->get('address__postal_code')->setData($value);
            if($type == 'city')
                $userInfoForm->get('address__city')->setData($value);
            if($type == 'region')
                $userInfoForm->get('address__region')->setData($value);
        }
        foreach( $userInfo['info'] as $key => $val)
        {
            if ($val['type'] === 'organization_name') {
                $userInfoForm->get('organization_name')->setData($val['value']);
            }
            if ($val['type'] === 'position') {
                $userInfoForm->get('position')->setData($val['value']);
            }
        }

        return $this->render('AppBundle:Profile:edit_profile.html.twig',array(
            'userInfoForm' => $userInfoForm->createView()
            ));
    }
        
    /**
     * @Route("/change-password", name="change_password")
     */
    public function changePasswordAction(Request $request)
    {
        $form = $this->createForm(new SignInType());
        if($request->isMethod('POST')){
            $form->handleRequest($request);
//            if($form->isValid()){
                $signInData = $form->getData();
                $authentication = $this->get('public')->authenticate($this->container->getParameter('cv_network'), $signInData['email'], $signInData['password']);

                if(!$authentication == false){
                    //FIXME
                    // set authenticated
                    $this->get('session')->set('authenticated', true);
                    $userInfo = $this->get('user')->getUserInfo();
                    $this->get('session')->set('userInfo', $userInfo);
                    if(isset($userInfo['is_admin']) && $userInfo['is_admin'] == true){
                        $this->get('session')->set('is_admin', true);
                    }
                    return $this->redirect($this->generateUrl('homepage'));
                }else{
                    $this->get('session')->getFlashBag()->add('errors','Username or Password is not correct, please try again.');
                    return $this->redirect($this->generateUrl('homepage'));
                }
            }

        return $this->render('AppBundle:Profile:change_password.html.twig');
    }    
    
    /**
     * Update avatar
     *
     * @Route("/update-avatar", name="update_avatar")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAvatarAction(Request $request)
    {
        $response = $this->get('user')->update($request->request->all());
        if (!empty($response['outcome']) && $response['outcome'] == 'success') {
            $this->get('session')->getFlashBag()->add('info', "Your avatar was successfully updated.");
        }else{
            $this->get('session')->getFlashBag()->add('error', $response['data']['user_message']);
        }
        return $this->redirect($this->generateUrl('my_profile'));
    }

}
