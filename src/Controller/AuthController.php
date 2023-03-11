<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends Controller
{
    protected function getServer(): \OAuth2\Server {
                $dsn      = 'pgsql:host=database';
                $username = 'app';
                $password = '!ChangeMe!';
                
                $config['use_openid_connect'] = true;
                $config['issuer'] = 'caddy';

                // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
                $storage = new \OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
                
                // Pass a storage object or array of storage objects to the OAuth2 server class
                $server = new \OAuth2\Server($storage, $config);


                $publicKey  = file_get_contents('/srv/app/config/jwt.pub');
                $privateKey = file_get_contents('/srv/app/config/jwt.pem');
                // create storage
                $keyStorage = new \OAuth2\Storage\Memory(array('keys' => array(
                    'public_key'  => $publicKey,
                    'private_key' => $privateKey,
                )));

                $server->addStorage($keyStorage, 'public_key'); 
                
                // Add the "Client Credentials" grant type (it is the simplest of the grant types)
                $server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
                
                // Add the "Authorization Code" grant type (this is where the oauth magic happens)
                $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));

                $server->addGrantType(new \OAuth2\OpenID\GrantType\AuthorizationCode($storage));

                return $server;
    }
    /**
     * @Route("/auth", name="auth")
     */
    public function auth(): Response
    {
        $server = $this->getServer();

        $request = \OAuth2\Request::createFromGlobals();
        $response = new \OAuth2\Response();
        
        $userAgreed = true;

        $server->handleAuthorizeRequest($request, $response, $userAgreed, 'pumba');

        $response->send();
        die;


        if(302 === $response->getStatusCode()){
            return new Response($response->getHttpHeader('Location'));
        } else {
            $response->send();
        }
    }

    /**
     * @Route("/token", name="token")
     */
    public function token(): Response
    {
        $server = $this->getServer();

        $response = $server->handleTokenRequest(\OAuth2\Request::createFromGlobals());

        $response->send();
        die;

        return new Response((string)$response);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
    
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
    
        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): Response
    {
        //
    }
}
