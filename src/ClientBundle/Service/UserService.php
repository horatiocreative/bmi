<?php
/**
 * Created by PhpStorm.
 * User: khoa.nguyen
 * Date: 8/10/2015
 * Time: 10:59 AM
 */

namespace ClientBundle\Service;

use CrowdValley\Bundle\AuthBundle\Service\UserService as BaseUserService;
use Guzzle\Http\Exception\BadResponseException;

class UserService extends BaseUserService {

    /**
     * @param $network
     * @param $parameters
     * @return mixed
     */
    public function signupUser($network, $parameters)
    {
        $token = $this->container->get('user')->getTokenNewUser();
        $json_parameter = json_encode($parameters);
        $client = $this->container->get('client');
        $request = $client->post('v1/' . $network . '/users', array('cv-auth' => $token, 'network' => $network));
        $request->setBody($json_parameter, 'application/json');
        $responseBody = "";
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
     * @param $network
     * @param $parameters
     */
    public function update($parameters)
    {
        $token = $this->getToken();
        $client = $this->container->get('client');
        $json_parameter = json_encode($parameters);
        $request = $client->patch('v1/'.$this->network.'/self', array('cv-auth' => $token, 'network' => $this->network) );
        $request->setBody($json_parameter, 'application/json');
        $responseBody = '';
        try
        {
            $response = $request->send();
            $responseBody= $response->getBody();
        }
        catch(BadResponseException $e)
        {
            if ($e->getResponse())
                $responseBody =  $e->getResponse()->getBody();
        }

        $responseArray = json_decode($responseBody, true);
        return $responseArray;
    }
    
    
    /**
     * 
     */
    public function getUsers()
    {
        $token = $this->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/'.$this->network.'/users', array('cv-auth' => $token, 'network' => $this->network) );
        $responseBody = '';
        try
        {
            $response = $request->send();
            $responseBody= $response->getBody();
        }
        catch(BadResponseException $e)
        {
            if ($e->getResponse())
                $responseBody =  $e->getResponse()->getBody();
        }

        $responseArray = json_decode($responseBody, true);
        return $responseArray;
    }
    
        /**
     * 
     */
    public function getUserWithId($user_id)
    {
        $token = $this->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/'.$this->network.'/users/'.$user_id, array('cv-auth' => $token, 'network' => $this->network) );
        $responseBody = '';
        try
        {
            $response = $request->send();
            $responseBody= $response->getBody();
        }
        catch(BadResponseException $e)
        {
            if ($e->getResponse())
                $responseBody =  $e->getResponse()->getBody();
        }

        $responseArray = json_decode($responseBody, true);
        return $responseArray;
    }
    
     /**
     * 
     */
    public function markSelfRegistrationCompleted()
    {
        $token = $this->getToken();
        $client = $this->container->get('client');
        $request = $client->post('v1/'.$this->network.'/self/markRegistrationComplete', array('cv-auth' => $token));
        $responseBody = '';
        try
        {
            $response = $request->send();
            $responseBody= $response->getBody();
        }
        catch(BadResponseException $e)
        {
            if ($e->getResponse())
                $responseBody =  $e->getResponse()->getBody();
        }

        $responseArray = json_decode($responseBody, true);
        return $responseArray;
    }    
    
     /**
     * 
     */
    public function getSelfOfferings()
    {
        $token = $this->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/'.$this->network.'/self/offerings', array('cv-auth' => $token));
        $responseBody = '';
        try
        {
            $response = $request->send();
            $responseBody= $response->getBody();
        }
        catch(BadResponseException $e)
        {
            if ($e->getResponse())
                $responseBody =  $e->getResponse()->getBody();
        }

        $responseArray = json_decode($responseBody, true);
        return $responseArray;
    }     
    
     /**
     * 
     */
    public function getSelfDocuments()
    {
        $token = $this->getToken();
        $client = $this->container->get('client');
        $request = $client->get('v1/'.$this->network.'/self/documents', array('cv-auth' => $token));
        $responseBody = '';
        try
        {
            $response = $request->send();
            $responseBody= $response->getBody();
        }
        catch(BadResponseException $e)
        {
            if ($e->getResponse())
                $responseBody =  $e->getResponse()->getBody();
        }

        $responseArray = json_decode($responseBody, true);
        return $responseArray;
    }           
}