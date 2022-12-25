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

    /**
     * @throws \Exception
     */
    public function visiteur()
    {
        $request = $this->requestStack->getCurrentRequest(); //dd($request->getLocale());

        // Initialisation du fichier logs.json dans le repertoire public
        $path = $request->server->get('DOCUMENT_ROOT').'/logs.json'; //dd($path);
        if (!file_exists($path)){
            $data [0] = [
                'ip' => $request->getClientIp(),
                'page' => $this->route_page(),
                'url' => $request->getUri(),
                'session' => '',
                'createdAt' => date('Y-m-d H:i:s')
            ];

            file_put_contents('logs.json', json_encode($data));
        }

        if ($this->frontend($request->get('_route'))) {

            $visiteur = new Visiteur();

            if (!$request->getSession()->get('_visiteur')){ //dd($request->get('_locale'));
                $request->getSession()->set('_visiteur', uniqid(12));

                $visiteur->setIp($request->getClientIp());
                $visiteur->setPage($this->route_page());
                $visiteur->setUrl($request->getUri());
                $visiteur->setSession($request->getSession()->get('_visiteur'));

                $this->visiteurRepository->save($visiteur, true);
            }

            $file = file_get_contents('logs.json');

            $i = count(json_decode($file, true));
            $data[$i++] = [
                'ip' => $request->getClientIp(),
                'page' => $this->route_page(),
                'url' => $request->getUri(),
                'session' => $request->getSession()->get('_visiteur'),
                'createdAt' => date('Y-m-d H:i:s')
            ];

            $fusion = array_merge($data, json_decode($file, true));

            file_put_contents('logs.json', json_encode($fusion));
        }

    }

    /**
     * @throws \Exception
     */
    protected  function route_page(): int
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->get('_route');
        $locale = $request->getLocale();

        if (!$route) return false;
        elseif($locale === 'fr')
            $resultat = match ($route){
                'app_home' => 1,
                'app_frontend_index' =>2,
                'app_frontend_actualite_index' =>4,
                'app_frontend_actualite_show' =>6,
                'app_frontend_projet_index' =>8,
                'app_frontend_projet_show' =>10,
                default => throw new \Exception('Non supporter')
            };
        else{
            $resultat = match ($route){
                'app_home' => 1,
                'app_frontend_index' => 3,
                'app_frontend_actualite_index' => 5,
                'app_frontend_actualite_show' => 7,
                'app_frontend_projet_index' => 9,
                'app_frontend_projet_show' => 11,
                default => throw new \Exception('Unexpected match value')
            };
        }

        return $resultat;
    }

    protected function frontend(string $route =null): bool
    {
        return match ($route) {
                'app_frontend_index' => true,
                'app_frontend_actualite_index' => true,
                'app_frontend_actualite_show' => true,
                'app_frontend_projet_index' => true,
                'app_frontend_projet_show' => true,
                default => false,
            };
    }

}
