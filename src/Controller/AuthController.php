<?php


namespace App\Controller;

use App\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request, #[CurrentUser] ?User $currentUser, HttpClientInterface $httpClient): Response
    {
        if (null === $currentUser) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $requestContent = json_decode($request->getContent());

        $client = $currentUser->oauthClient;

        $response = $httpClient->request('POST', 'http://spices.home/token', [
            'body' => [
                'grant_type' => 'password',
                'client_id' => $client->identifier,
                'client_secret' => $client->secret,
                'scope' => 'spice',
                'username' => $requestContent->username,
                'password' => $requestContent->password
            ]
        ]);

        $responseContent = json_decode($response->getContent());

        $session = $request->getSession();
        $session->set('refresh_token', $responseContent->refresh_token);

        return $this->json($responseContent);
    }

    #[Route('/refresh', name: 'refresh', methods: ['GET'])]
    public function refresh(Request $request, #[CurrentUser] ?User $currentUser, HttpClientInterface $httpClient): Response {
        $client = $currentUser->oauthClient;
        $session = $request->getSession();

        $response = $httpClient->request('POST', 'http://spices.home/token', [
            'body' => [
                'grant_type' => 'refresh_token',
                'client_id' => $client->identifier,
                'client_secret' => $client->secret,
                'refresh_token' => $session->get('refresh_token')
            ]
        ]);

        $responseContent = json_decode($response->getContent());
        $session->set('refresh_token', $responseContent->refresh_token);

        return $this->json($responseContent);
    }

    #[Route('/logout-message', name: 'logout_message', methods: ['GET'])]
    public function logout(): Response {
        return $this->json(["message" => "You logged out."]);
    }
}
