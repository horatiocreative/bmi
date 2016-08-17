<?php

namespace ClientBundle\Service;
use Symfony\Component\HttpFoundation\Session\Session;
use Guzzle\Http\Exception\ServerErrorResponseException;

class InvestmentService
{
    protected $container;
    protected $session;
    protected $network;
    protected $token;
    protected $user;

    public function __construct($container, Session $session )
    {
        $this->container = $container;
        $this->session = $session;
        $this->network = $this->session->get('cv_network') ? $this->session->get('cv_network') : $this->container->getParameter('cv_network');
        $this->token = $this->container->get('user')->getToken();
        $this->user = $this->container->get('user')->getUser();
    }

    /**
     * Get list investment within current network
     *
     * @return mixed
     */
    public function getInvestment($id)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/' . $this->network . '/investments/'.$id, array('cv-auth' => $token, 'network' => $this->network));
        $responseBody = '';
        try {
            $response = $request->send();
            $responseBody = $response->getBody();
        } catch (ServerErrorResponseException $e) {
            if ($e->getResponse()) {
                $responseBody = $e->getResponse()->getBody();
            }
        }
        $responseArray = json_decode($responseBody, true);

        return $responseArray;
    }

    /**
     * @param $network
     * @return mixed
     */
    public function selfInvestments()
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/'.$this->network.'/self/investments', array('cv-auth' => $token, 'network' => $this->network) );
        $responseBody = '';
        try
        {
            $response = $request->send();
            $responseBody= $response->getBody();
        }
        catch(ServerErrorResponseException $e)
        {
            if ($e->getResponse())
                $responseBody =  $e->getResponse()->getBody();
        }

        $responseArray = json_decode($responseBody, true);
        return $responseArray;
    }

    /**
     * Add investment
     * @param $offering_id
     * @param $invArray
     * @return mixed
     */

    public function add($offering_id, $invArray)
    {
        $invJson = json_encode($invArray);
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->post('v1/' . $this->network . '/offerings/' . $offering_id . '/investments', array('cv-auth' => $token, 'network' => $this->network));
        $request->setBody($invJson, 'application/json');
        $response = $request->send();
        $responseBody = $response->getBody();
        $responseArray = json_decode($responseBody, true);

        return $responseArray;
    }
    
    /**
     * Get list of Investment's Documents
     *
     * @return mixed
     */
    public function getInvestmentDocuments($investment_id)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/' . $this->network . '/investments/' . $investment_id . '/documents', array('cv-auth' => $token, 'network' => $this->network));
        $responseBody = '';
        try {
            $response = $request->send();
            $responseBody = $response->getBody();
        } catch (BadResponseException $e) {
            if ($e->getResponse()) {
                $responseBody = $e->getResponse()->getBody();
            }
        }
        $responseArray = json_decode($responseBody, true);

        return $responseArray;
    }       
}
