<?php

namespace ClientBundle\Service;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Guzzle\Http\Exception\ServerErrorResponseException;

class OrganizationService {
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
     * Get all organizations
     *
     * @param null $id
     * @return mixed
     */
    public function getAll($id = null)
    {
        $end_point = 'v1/'.$this->network.'/organizations';
        if($id){
            $end_point = $end_point. '/'. $id;
        }

        $client = $this->container->get('client');
        $request = $client->get($end_point, array('cv-auth' => $this->token, 'network' => $this->network) );
        $response = $request->send();
        $responseBody= $response->getBody();
        $responseArray = json_decode($responseBody, true);

        return $responseArray;
    }

    /**
     * Get one organization
     *
     * @param $id
     * @return mixed
     */
    public function getOne($id)
    {
        return $this->getAll($id);
    }

    /**
     * Create a new Organization.
     *
     */
    public function create($orgArray)
    {
        if(!$this->user){
            throw new AccessDeniedException("User does not login.");
        }
        $user_id = $this->user['data']['user']['id'];
        $orgJson = json_encode($orgArray);
        $client = $this->container->get('client');
        $request = $client->post('v1/admin/'.$this->network.'/'.$user_id.'/organization', array('cv-auth' => $this->token, 'network' => $this->network));
        $request->setBody($orgJson, 'application/json');
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
     * Update an existing Organization.
     *
     */
    public function update($organization_id, $orgArray)
    {
        if(!$this->user){
            throw new AccessDeniedException("User does not login.");
        }
        $user_id = $this->user['data']['user']['id'];
        $orgJson = json_encode($orgArray);
        $client = $this->container->get('client');
        $request = $client->patch('v1/'.$this->network.'/organizations/'.$organization_id, array('cv-auth' => $this->token, 'network' => $this->network));
        $request->setBody($orgJson, 'application/json');
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
    
    public function addMemberToOrganization($org_id)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $user_id = $this->user['data']['user']['id'];

        $request = $client->post('v1/' . $this->network . '/organizations/' . $org_id . '/user/'.$user_id, array('cv-auth' => $token));
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
     * Delete an existing Organization
     *
     */
    public function delete()
    {

    }
}