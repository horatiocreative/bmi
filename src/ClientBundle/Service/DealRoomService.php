<?php

namespace ClientBundle\Service;
use Symfony\Component\HttpFoundation\Session\Session;
use Guzzle\Http\Exception\ServerErrorResponseException;

class DealRoomService
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

    public function getDealRoom($offering_id)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/' . $this->network . '/offerings/' . $offering_id . '/dealrooms', array('cv-auth' => $token));

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

    public function getDealTasks($network, $offering_id)
    {
        $networkArray = array('network' => $network);
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/' . $network . '/offerings/' . $offering_id . '/tasks', array('cv-auth' => $token, 'network' => $network));
        $response = $request->send();
        $responseBody = $response->getBody();
        $responseArray = json_decode($responseBody, true);

        return $responseArray;
    }


    public function addDealRoom($offering_id, $dealRoomArray)
    {
        $dealRoomJson = json_encode($dealRoomArray);
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->post('v1/' . $this->network . '/offerings/' . $offering_id . '/dealrooms', array('cv-auth' => $token));
        $request->setBody($dealRoomJson, 'application/json');
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

    public function requestAccess($offering_id, $dealRoomId)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->post('v1/' . $this->network . '/dealrooms/' . $dealRoomId . '/accessrequest', array('cv-auth' => $token));
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


    public function getDealRoomAccess($dealRoomId)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/' . $this->network . '/dealrooms/' . $dealRoomId . '/accessrequests', array('cv-auth' => $token));
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

    public function getDealRoomUserAccess($network, $dealRoomId, $userId)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/' . $network . '/dealrooms/' . $dealRoomId . '/accessrequests/' . $userId, array('cv-auth' => $token, 'network' => $network));
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

    public function updateAccess($network,  $dealRoomId, $requestId, $status)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');

        $request = $client->patch('v1/' . $network . '/dealrooms/' . $dealRoomId . '/accessrequests/'.$requestId.'?status='.$status, array('cv-auth' => $token, 'network' => $network));
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


    public function updateDealRoom($network,  $dealRoomId, $info)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');

        $request = $client->patch('v1/' . $network . '/dealrooms/'.$dealRoomId, array('cv-auth' => $token, 'network' => $network));
        $request->setBody(json_encode($info), 'application/json');
        $response = $request->send();
        $responseBody= $response->getBody();
        $responseArray = json_decode($responseBody, true);
        return $responseArray;
    }

    public function addDocumentGroup($network, $dealRoomId, $dealRoomArray)
    {
        $dealRoomJson = json_encode($dealRoomArray);
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->post('v1/' . $network . '/dealrooms/' . $dealRoomId . '/document_groups', array('cv-auth' => $token, 'network' => $network));
        $request->setBody($dealRoomJson, 'application/json');
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

    public function addAccessGroup($network, $dealRoomId, $dealRoomArray)
    {
        $dealRoomJson = json_encode($dealRoomArray);
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->post('v1/' . $network . '/dealrooms/' . $dealRoomId . '/access_groups', array('cv-auth' => $token, 'network' => $network));
        $request->setBody($dealRoomJson, 'application/json');
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

    public function updateAccessGroup($network,  $dealRoomId, $accessId, $group_id)
    {
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');

        $request = $client->post('v1/' . $network . '/dealrooms/' . $dealRoomId . '/accessrequests/'.$accessId.'/group/'.$group_id, array('cv-auth' => $token, 'network' => $network));
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

    public function editAccessGroup($network,  $dealRoomId, $accessId, $dealRoomArray)
    {
        $dealRoomJson = json_encode($dealRoomArray);

        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');

        $request = $client->post('v1/' . $network . '/dealrooms/' . $dealRoomId . '/accessgroups/'.$accessId, array('cv-auth' => $token, 'network' => $network));
        $request->setBody($dealRoomJson, 'application/json');
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
}
