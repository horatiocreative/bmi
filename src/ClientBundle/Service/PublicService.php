<?php

namespace ClientBundle\Service;

use CrowdValley\Bundle\AuthBundle\Service\PublicService as BasePublicService;
use Guzzle\Http\Exception\ServerErrorResponseException;

class PublicService extends BasePublicService {
    /**
     * @param $network
     * @param $parameters
     * @return mixed
     */
    public function resetPwd($network, $parameters)
    {
        $json_parameter = json_encode($parameters);
        $client = $this->container->get('client');
        $request = $client->post('admin/'.$network.'/reset-pwd' , array('network' => $network) );
        $request->setBody($json_parameter, 'application/json');
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
     * @param $network
     * @param $parameters
     * @return mixed
     */
    public function forgotPwd($network, $parameters)
    {
        $json_parameter = json_encode($parameters);
        $client = $this->container->get('client');
        $request = $client->post('admin/'.$network.'/forgot-pwd' , array('network' => $network) );
        $request->setBody($json_parameter, 'application/json');
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
     * @param $network
     * @param $parameters
     * @return mixed
     */
    public function verifyEmail($network, $parameters)
    {
        $json_parameter = json_encode($parameters);
        $client = $this->container->get('client');
        $request = $client->put('admin/'.$network.'/verify-email' , array('network' => $network) );
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

    public function getS3URL($network, $parameters)
    {
        $parameters = json_encode($parameters);
        $token = $this->container->get('user')->getToken();
        $client = $this->container->get('client');
        $request = $client->put('v1/' . $network . '/s3', array('cv-auth' => $token, $network));
        $request->setBody($parameters, 'application/json');
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
    
    public function getFeaturedOfferings()
    {
        $client = $this->container->get('client');
        $request = $client->get('v1/kobofunds/public/featuredOfferings');
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
}