<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class AuthenticatorSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $securityLogger, private RequestStack $requestStack,
        private UserRepository $userRepository,
    )
    {
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'security.authentication.success' => 'onSecurityAuthenticationSuccess',
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            'Symfony\Component\Security\Http\Event\LogoutEvent' => 'onSecurityLogout',
            //'Symfony\Component\Security\Http\Event\LoginFailureEvent' => 'onSecurityAuthenticationFailure',
            SecurityEvents::SWITCH_USER => 'onSecuritySwitcher'
        ];
    }

    public function onSecurityAuthenticationFailure(): void
    {
        //dd($event);
        //['user_IP' => $userIP] = $this->getRouteNameAndUserIP();

        ///$securityToken = $event->getAuthenticator(); //dump($event->getAuthenticator());

        //['email' => $emailEntered] = $securityToken->get
    }

    public function onSecurityAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        //dd($event);
        [
            'route_name' => $routeName,
            'user_IP' => $userIP
        ] = $this->getRouteNameAndUserIP(); //dd($event->getAuthenticationToken()->getRoleNames());

        if (empty($event->getAuthenticationToken()->getRoleNames())){
            $this->securityLogger->info("Oh, un utilisateur anonyme ayant l'adresse IP '{$userIP}' est apparu sur la route: '{$routeName}' :-).");
        }else{
            $securityToken = $event->getAuthenticationToken();

            $userEmail = $this->getUserEmail($securityToken);

            $this->securityLogger->info("L'utilisateur {$userEmail} vient de se connecté via l'adresse IP: {$userIP} :-).");

            // Mise a jour de l'entité USER
            $userEntity = $this->userRepository->findOneBy(['email' => $userEmail]);
            if ($userEntity){
                $connexion = (int) $userEntity->getConnexion();
                $userEntity->setConnexion($connexion + 1);
                $userEntity->setLastConnectedAt( new \DateTime());

                $this->userRepository->save($userEntity, true);
            }
        }
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        //dd($event);
    }

    public function onSecurityLogout(LogoutEvent $event): void
    {
        $response = $event->getResponse(); //dd($response);

        $securityToken = $event->getToken();

        if (!$response || !$securityToken)
            return;

        ['user_IP' => $userIP] = $this->getRouteNameAndUserIP();
        $userEmail = $this->getUserEmail($securityToken);
        $targetUrl = $response->getTargetUrl();

        $this->securityLogger->info("{$userEmail} s'est donnecté depuis l'adresse IP {$userIP} et a été rédirigé vers l'url suivante: {$targetUrl}");

    }

    public function onSecuritySwitcher(SwitchUserEvent $event): void
    {
        dd($event);
    }

    /**
     * Renvoie l'adresse IP et le nom de la route de l'utilsateur
     *
     * @return array{user_IP: string|null, route_name: mixed}
     */
    private function getRouteNameAndUserIP(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request)
            return [
                'route_name' => 'Inconnue',
                'user_IP' => 'Inconnue',
            ];

        return [
            'route_name' => $request->attributes->get('_route'),
            'user_IP' => $request->getClientIp() ?? 'Inconnue',
        ];
    }

    private function getUserEmail(TokenInterface $securityToken)
    {
        /**
         * @var User $user
         */
        $user = $securityToken->getUser();

        return $user->getEmail();
    }
}
