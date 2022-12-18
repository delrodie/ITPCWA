<?php

namespace App\EventSubscriber;

use App\Entity\Visiteur;
use App\Repository\VisiteurRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class VisitorSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $requestStack, private VisiteurRepository $visiteurRepository
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
    public function onKernelRequest(RequestEvent $event): void
    {
        $this->visiteur();
    }

    public function visiteur()
    {
        $request = $this->requestStack->getCurrentRequest();

        // Initialisation du fichier logs.json dans le repertoire public
        $path = $request->server->get('DOCUMENT_ROOT').'/logs.json'; //dd($path);
        if (!file_exists($path)){
            $data [0] = [
                'ip' => $request->getClientIp(),
                'page' => $this->route_page($request->get('_route')),
                'url' => $request->getUri(),
                'session' => '',
                'createdAt' => date('Y-m-d H:i:s')
            ];

            file_put_contents('logs.json', json_encode($data));
        }

        if ($this->frontend($request->get('_route'))) {

            $visiteur = new Visiteur();

            if (!$request->getSession()->get('_visiteur')){
                $request->getSession()->set('_visiteur', uniqid(12));

                $visiteur->setIp($request->getClientIp());
                $visiteur->setPage($this->route_page($request->get('_route')));
                $visiteur->setUrl($request->getUri());
                $visiteur->setSession($request->getSession()->get('_visiteur'));

                $this->visiteurRepository->save($visiteur, true);
            }

            $file = file_get_contents('logs.json');

            $i = count(json_decode($file, true));
            $data[$i++] = [
                'ip' => $request->getClientIp(),
                'page' => $this->route_page($request->get('_route')),
                'url' => $request->getUri(),
                'session' => $request->getSession()->get('_visiteur'),
                'createdAt' => date('Y-m-d H:i:s')
            ];

            $fusion = array_merge($data, json_decode($file, true));

            file_put_contents('logs.json', json_encode($fusion));
        }

    }

    protected  function route_page(string $route = null): int
    {
        if (!$route) return false;
        else
            $resultat = match ($route){
                'app_home' => 1,
                'app_francais' =>2
            };

        return $resultat;
    }

    protected function frontend(string $route =null): bool
    {
        return match ($route) {
                'app_francais' => true,
                default => false,
            };
    }

}
