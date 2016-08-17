<?php

namespace ClientBundle\Service;
use Symfony\Component\HttpFoundation\Session\Session;
use Guzzle\Http\Exception\ServerErrorResponseException;

class DocumentService
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
     * Get document by id
     * @param $network
     * @param $document_id
     * @return mixed
     */
    public function getDocument($document_id)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/'.$this->network.'/document/'.$document_id, array('cv-auth' => $token, 'network' => $this->network)  );
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
     * Get documents for Offering
     * @param $offering_id
     * @return mixed
     */
    public function getOfferingDocuments($offering_id)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/'.$this->network.'/offerings/'.$offering_id.'/documents', array('cv-auth' => $token)  );
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
     * Get documents for Organization
     * @param $organization_id
     * @return mixed
     */
    public function getOrganizationDocuments($organization_id)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/'.$this->network.'/organizations/'.$organization_id.'/documents', array('cv-auth' => $token)  );
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

}

