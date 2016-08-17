<?php

namespace ClientBundle\Service;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Guzzle\Http\Exception\ServerErrorResponseException;

class OfferingService {
    protected $container;
    protected $session;
    protected $network;
    protected $token;
    protected $user;

    /**
     * @param ContainerInterface $container
     * @param Session $session
     */
    public function __construct(ContainerInterface $container, Session $session )
    {
        $this->container = $container;
        $this->session = $session;
        $this->network = $this->session->get('cv_network') ? $this->session->get('cv_network') : $this->container->getParameter('cv_network');
        $this->token = $this->container->get('user')->getToken();
        $this->user = $this->container->get('user')->getUser();

    }

    /**
     * Get Offerings
     * @param $network
     * @param $offset
     * @param $limit
     * @param array $filter
     * @param null $search
     * @return mixed
     */
    public function listOfferings($offset = 0, $limit = 100, $filter = array(), $search = null)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $inputs = array(
            'page' => $offset,
            'limit' => $limit,
            'filter' => $filter
        );
        if (!empty($search)) {
            $inputs['search'] = $search;
        }
        $params = http_build_query($inputs);
        $request = $client->get('v1/' . $this->network . '/offerings?' . $params, array('cv-auth' => $token, 'network' => $this->network));

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
     * Get offering by offering id
     * @param $offering_id
     * @return mixed
     */

    public function get($offering_id)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/' . $this->network . '/offerings/' . $offering_id, array('cv-auth' => $token, 'network' => $this->network));
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
     * Get offering by offering id
     * @param $offering_id
     * @return mixed
     */

    public function getOfferingsForOrganization($organization_id)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/' . $this->network . '/organizations/' . $organization_id . '/offerings', array('cv-auth' => $token, 'network' => $this->network));
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
     * Add offering
     * @param $organization_id
     * @param $offeringArray
     * @return mixed
     */
    public function add($organization_id, $offeringArray)
    {
        $offeringJson = json_encode($offeringArray);
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->post('v1/' . $this->network . '/organizations/' . $organization_id . '/offerings', array('cv-auth' => $token, 'network' => $this->network));
        $request->setBody($offeringJson, 'application/json');
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
     * Update offering
     * @param $offering_id
     * @param $offeringArray
     * @return mixed
     */
    public function update($offering_id, $offeringArray)
    {
        $offeringJson = json_encode($offeringArray);
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->patch('v1/' . $this->network . '/offerings/' . $offering_id , array('cv-auth' => $token, 'network' => $this->network));
        $request->setBody($offeringJson, 'application/json');
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
     * Get list list of Offering's Bulletin
     *
     * @return mixed
     */
    public function getOfferingBulletins($offering_id)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/' . $this->network . '/offerings/' . $offering_id . '/bulletins', array('cv-auth' => $token, 'network' => $this->network));
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
    
    /**
     * Get list list of Offering's Documents
     *
     * @return mixed
     */
    public function getOfferingDocuments($offering_id)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/' . $this->network . '/offerings/' . $offering_id . '/documents', array('cv-auth' => $token, 'network' => $this->network));
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