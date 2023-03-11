<?php

namespace App\Controller;

use OAuth2\Encryption\Jwt;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends Controller
{
    protected function getClient(){
        return new \GuzzleHttp\Client();
    }
    /**
     * @Route("/client", name="client")
     */
    public function client(Request $request): Response
    {
        $client = $this->getClient();

        $clientId = 'testclient';
        if($code = $request->get('code')){
            $res = $client->request('POST', 'http://caddy/token', [
                'auth' => [$clientId, 'testpass'],
                'form_params' => [
//                    'client_id' => testclient
                    'grant_type'=>'authorization_code',
                    'code' => $code,
                    'redirect_uri' => 'http://caddy:8080/client',
                    'scope' => 'openid'
                ]
            ]);

            $response = \json_decode($res->getBody(), true);

            $jwt = new Jwt();
            dump($jwt->decode($response['id_token'], file_get_contents('/srv/app/config/jwt.pub')));
            return new Response();
        } else {
            return new RedirectResponse("http://caddy:8080/auth?client_id={$clientId}&response_type=code&state=asd&redirect_uri=http://caddy:8080/client&scope=openid");
        }
    }
}