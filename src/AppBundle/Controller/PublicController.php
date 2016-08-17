<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\SignInType;
use AppBundle\Form\SignUpType;
use AppBundle\Util\Util;
use Symfony\Component\HttpFoundation\Response;

class PublicController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        
        $response = array();
		$authenticated = $this->get('session')->get('authenticated');
        if ($authenticated) {
            $offerings = $this->get('offering')->listOfferings();
				if (!empty($offerings['outcome']) && $offerings['outcome'] == 'success') {
				$offeringList = $offerings['data']['list'];
				if (!empty($offeringList)) {
					foreach ($offeringList as $k => $offering) {
						$organization = $this->get('organization')->getOne($offering['organization_id']);
						if (!empty($organization['outcome']) && $organization['outcome'] == 'success') {
							$org = $organization['data']['organization'];
							$response[] = array('offering'=>$offering,'org'=>$org);
						}
					}
				}
			}
        }
        
        $this->params['res'] = $response;
        $this->params['required'] = $request->get('required');
        $this->params['menu_item']= 'homepage';
        return $this->render('AppBundle:Public:index.html.twig',$this->params);
    }


    /**
     * @Route("/contact-us", name="contact_us")
     */
    public function contactUsAction()
    {
        $this->params['menu_item']= 'contact_us';
        return $this->render('AppBundle:Public:contact_us.html.twig',$this->params);
    }

    /**
     * @Route("/legal", name="legal")
     */
    public function viewLegalAction()
    {
        $this->params['menu_item']= 'legal';
        return $this->render('AppBundle:Public:legal.html.twig',$this->params);
    }
    
    /**
     * @Route("/forgot-password", name="forgot_password")
     */
    public function forgotPasswordAction()
    {
        $this->params['menu_item']= 'forgot_password';
        return $this->render('AppBundle:Public:forgot_password.html.twig',$this->params);
    }
    
    /**
     * @Route("/sign-in", name="sign_in")
     */
    public function signInAction(Request $request)
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
//        else{
//                $errorMsg = (string) $form->getErrors(true);
//
//                if($request->isXmlHttpRequest()) {
//                    return new JsonResponse(array('success' => false, 'errorMsg' => $errorMsg));
//                }
//            }
//        }
        return $this->render('AppBundle:Public:sign_in.html.twig');
    }


    /**
     * @Route("/sign-up", name="sign_up")
     */
    public function signUpAction(Request $request)
    {
        $form = $this->createForm(new SignUpType());
        
        if($request->isMethod('POST')){
            $form->submit($request);
            $signUpData = $request->request->get('sign_up_type');
            $network = $this->container->getParameter('cv_network');
            $url = $this->generateUrl('verify_email',array(), true);
            $urlArray = array('url' => $url);
            $arrData = array_merge($signUpData, $urlArray);

            $registerResponse = $this->get('user')->signupUser($network, $arrData );            
            if (!empty($registerResponse['outcome']) && $registerResponse['outcome'] == 'success') {
                $this->addFlash('info','Thanks for registering to the platform. Please click the link in your email in order to continue the registration process.');
                return $this->redirectToRoute('homepage');

            }
            
            else {
                if(isset($registerResponse['status']) && $registerResponse['status'] == 20009){ // email exist in network
                    $forgotPasswordURL = $this->generateUrl('forgot_password');
                    $msg = "Email account is already in use. If you have forgotten your password, click <a href='".$forgotPasswordURL."'>here</a>";
                    $this->addFlash('errors',$msg);
                }else{
                    $this->addFlash('errors',$registerResponse['data']['user_message']);
                    return $this->redirectToRoute('homepage');                    
                }

            }
            return $this->redirectToRoute('homepage');
        }
        return $this->render('AppBundle:Public:sign_up.html.twig',array('form'=>$form->createView()));
    }

    /**
     * Logout
     *
     * @Route("/logout", name="logout")
     */
    public function signOutAction(Request $request)
    {
        $required = $request->query->get('required', '');
        if($this->get('session')->has('authenticated') && $this->get('session')->get('authenticated'))
        {
            $this->get('session')->set('authenticated', false);
            $this->get('security.token_storage')->setToken(null);
            $this->get('request')->getSession()->invalidate();
        }
        
        if ($required === 'login') {
            return $this->redirectToRoute('homepage', array('required' => 'login'));
        } else {
            return $this->redirectToRoute('homepage');
        }        
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/verify-email", name="verify_email")
     */
    public function verifyEmailAction(Request $request)
    {
        $network = $this->container->getParameter('cv_network');
        $user_id = $request->query->get('user_id');
        $secret  = $request->query->get('secret');
        $params = array(
            'user_id' => $user_id,
            'secret' => $secret
        );
                
        $this->get('public')->verifyEmail('<network>', $params);

        return $this->redirect($this->generateUrl('accreditation'));
    }

    /**
     * @Route("/put-s3-url", name="put_s3aws")
     *
     * @return Response
     */
    public function s3AwsAction()
    {
        $request = $this->get('request');
        $fileName = $request->get('name');
        $fileType = $request->get('type');
        $fileObject = Util::alphaNumCodeGenerator();
        $parameters = array('file_name' => $fileName, 'file_type' => $fileType, 'file_object' => $fileObject);
        $ret = $this->get('public')->getS3URL($this->container->getParameter('cv_network'), $parameters);

        return new Response($ret['url']['url'], 200);
    }
}
