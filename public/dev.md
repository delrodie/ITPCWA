# ITPC West Africa website 

````
cahier de route du developpement du website de ITPC West Africa. 
Notons que le site est développé comme suit: 
langage: PHP 8
Framework: symfony 6.1.*
```` 

reférence de [formatdashcontage](https://docs.github.com/fr/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax#links)

## Partie 1 : Backoffice 

### 1- Intégration du template du backoffice 

<details><summary> Détails </summary>
<p>

Le template du backoffice est un style menu vertical composé des rubriques suivants:

```Rubriques
- Tableau de bord
* Francais 
    - Slider
    - Type de présentation 
    - Présentation
    - Actulaités 
    - Campagnes 
    - Jobs
        - Appel d'offres
        - Candidats 
    - Ressources 
    - Galérie
* Anglais 
    - Slider
    - Présentation's type 
    - Presentation
    - News 
    - Campagnes 
    - Jobs
        - Tender
        - Candidates 
    - Resources 
    - Gallery
- Images
- Newsletter

* Administrateur 
    - Utilisateur
    - Monitoring 
```

Chaque rubrique est associée à une entité (table) d'où le MLD suivant:

```MLD
# Tables des rubriques française
FrInfo (Id, titre, debut, fin) 
FrType (Id, libelle, pageIndex, slug)
FrPresentation (Id, titre, resumé, contenu, media, slug, tags, pageIndex, createdAt, updatedAt, #FrType)
FrActualite (Id, titre, resume, contenu, media, slug, tags, pageIndex, createdAt, updatedAt)
FrCampagne (id, titre, resume, contenu, media, slug, tags, pageIndex, createdAt, updatedAt)
FrJob (id, reference, titre, resume, contenu, media, slug, date fin, lieu fonction, createdAt, updatedAt)
FrRessource (id, reference, titre, description, media, slug, extension, pageIdex, created, updatedAt)

#Table des rubriques anglaise 
EnInfo (Id, titre, debut, fin) 
EnType (Id, libelle, pageIndex, slug)
EnPresentation (Id, titre, resumé, contenu, media, slug, tags, pageIndex, createdAt, updatedAt, #FrType)
EnActualite (Id, titre, resume, contenu, media, slug, tags, pageIndex, createdAt, updatedAt)
EnCampagne (id, titre, resume, contenu, media, slug, tags, pageIndex, createdAt, updatedAt)
EnJob (id, reference, titre, resume, contenu, media, slug, date fin, lieu fonction, createdAt, updatedAt)
EnRessource (id, reference, titre, description, media, slug, extension, pageIdex, created, updatedAt)

# tables globales
Slider (Id, titre, media, statut)
Candidat (id, matricule, nom, prenoms, phone, email, mediaLettreMotivation, mediaCV, jobReference, recaptcha, createdAt, updatedAt)
Visiteur (Id, IP, IdSession, page, createdAt)
Traduction (id, route, locale, page)
Contact( Id, nom, email, localisation, message, statut, createdAt, updatedAt)
User (id, username, pass, roles)
``` 
</p>
</details>

### 2- PHPSTAN
Installation du phpstan pour vérification du code PHP 
> composer require --dev phpstan/phpstan-symfony  
> 
> vendor/bin/phpstan analyse [option]

### 3- Gestion User
La rubrique User permet de gerer les utilisateurs du backoffice.
<details><summary> a- Security Login  </summary>
Creation du LoginController pour la gestion du formulaire de connexion. 
<p>

```
<?php

namespace App\Controller;

use App\Services\GestionUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
#[Route('/security', name: 'app_login')]
public function index(AuthenticationUtils $authenticationUtils, GestionUser $_user): Response
{
if ($_user->initialisation())
$this->addFlash('success', "L'utilisateur ITPCWA a été crée avec succès!");

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
}

```
Isolation du Layout de template de sécurité 

```
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{% block title %}SECURTE :: {% endblock %}</title>
    <link rel="icon" href="{{ absolute_url(asset('assets/img/favico.svg')) }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&amp;display=swap" rel="stylesheet">

    {% block stylesheets %}
        <link rel="stylesheet" href="{{ absolute_url(asset('assets/css/vendor.min.css')) }}">
        <link rel="stylesheet" href="{{ absolute_url(asset('assets/css/theme.minc619.css')) }}">
        <link rel="stylesheet" href="{{ absolute_url(asset('assets/css/theme.min.css')) }}">
        {{ encore_entry_link_tags('app') }}
    {% endblock %}

</head>
<body class="has-navbar-vertical-aside navbar-vertical-aside-show-xl footer-offset">

<script src="{{ asset('assets/js/hs.theme-appearance.js') }}"></script>

<main id="content" role="main" class="main">
    <div class="position-fixed top-0 end-0 start-0 bg-img-start" style="height: 32rem; background-image: url(assets/svg/components/card-6.svg);">
        <div class="shape shape-bottom zi-1">
            <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 1921 273">
                <polygon fill="#fff" points="0,273 1921,273 1921,0 " />
            </svg>
        </div>
    </div>

    <div class="container py-5 py-sm-7">
        <a class="d-flex justify-content-center mb-5" href="{{ path('app_home') }}">
            <img class="zi-2" src="{{ asset('assets/img/logo.svg') }}" alt="Image Description" style="width: 8rem;">
        </a>

        <div class="mx-auto" style="max-width: 30rem;">
            <div class="card card-lg mb-5">
                {% block body %}{% endblock %}
            </div>
        </div>
    </div>
</main>

{% block javascripts %}
    <script src="{{ absolute_url(asset('assets/js/vendor.min.js')) }}"></script>
    <script src="{{ absolute_url(asset('assets/js/theme.min.js')) }}"></script>

{% endblock %}

<script>
    (function() {
        window.onload = function () {


            // INITIALIZATION OF TOGGLE PASSWORD
            // =======================================================
            new HSTogglePassword('.js-toggle-password')
        }
    })()
</script>
</body>
</html>

``` 

Template du formulaire de connexion ` App/Template/Security/login.html.twig `

```
{% extends 'security_layout.html.twig' %}

{% block title %}{{parent()}} Sécurité{% endblock %}

{% block body %}
    <div class="card-body">
        <form action="{{ path('app_login') }}" method="post" class="js-validate needs-validation" novalidate>
            <div class="text-center">
                <div class="mb-5">
                    <h1 class="display-5">Connexion</h1>
                    {% for label, messages in app.flashes %}
                        {% for message in messages %}
                            <div class="alert alert-{{ label }}" role="alert">
                                {{ message }}
                            </div>
                        {% endfor %}
                    {% endfor %}
                    <span class="divider-center text-muted mb-4"></span>
                </div>
            </div>
            {% if error %}
                <div class="alert alert-danger">{{ error.messageKey|trans(error.messageData, 'security') }}</div>
            {% endif %}

            {% if app.user %}
                <div class="mb-3">
                    Vous êtes connecté en tant que {{ app.user.userIdentifier }}, <a href="{{ path('app_logout') }}">Deconnexion</a>
                </div>
            {% endif %}


            <div class="mb-4">
                <label class="form-label" for="inputUsername">Email</label>
                <input type="text" value="{{ last_username }}" name="_username" id="inputUsername" class="form-control form-control-lg" autocomplete="username" tabindex="1" required autofocus>
            </div>

            <div class="mb-4">

                <label class="form-label" for="inputPassword">Mot de passe</label>
                <div class="input-group input-group-merge" data-hs-validation-validate-class>
                    <input type="password" class="js-toggle-password form-control form-control-lg" name="_password" id="inputPassword" placeholder="8+ caractères" aria-label="8+ characters required" required minlength="8" data-hs-toggle-password-options='{
                           "target": "#changePassTarget",
                           "defaultClass": "bi-eye-slash",
                           "showClass": "bi-eye",
                           "classChangeTarget": "#changePassIcon"
                         }' autocomplete="current-password" tabindex="2">
                    <a id="changePassTarget" class="input-group-append input-group-text" href="javascript:;">
                        <i id="changePassIcon" class="bi-eye"></i>
                    </a>
                </div>

                <span class="invalid-feedback">Entrez un mot de passe valide.</span>
            </div>
            <input type="hidden" name="_csrf_token"
                   value="{{ csrf_token('authenticate') }}"
            >

            {#
            Uncomment this section and add a remember_me option below your firewall to activate remember me functionality.
            See https://symfony.com/doc/current/security/remember_me.html

            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" name="_remember_me"> Remember me
                </label>
            </div>
            #}

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg" tabindex="3">Se connecter</button>
            </div>
        </form>
    </div>
{% endblock %}

``` 
</p>
</details>

<details><summary> b- Change mot de passe  </summary>

Afin de permettre a chaque utilisateur de modifier son mot passe, le controller ` ChangePasswordController `  
<p>

```
<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ChangePasswordController extends AbstractController
{
    #[Route('/change/password', name: 'app_change_password')]
    public function index(Request $request, AuthenticationUtils $authenticationUtils, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        // Verification de la connexion de l'utilisateur
        $last_username = $authenticationUtils->getLastUsername(); //dd($last_username);
        if (!$last_username){
            $this->addFlash('danger', "Attention veuillez vous connecter pour changer le mot de passe.");
            return $this->redirectToRoute('app_login',[],Response::HTTP_SEE_OTHER);
        }

        // Recuperation des informations du formulaire
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        // Modification du mot de passe si c'est le même utilisateur
        if ($username === $last_username && $request->request->get('_csrf_token')){
            $user = $userRepository->findOneBy(['email' => $username]);
            $passwordHashed = $passwordHasher->hashPassword($user, $password);

            $userRepository->upgradePassword($user, $passwordHashed);

            $this->addFlash('success', "Le mot de passe a été modifié avec succès!");

            return $this->redirectToRoute('app_logout');
        }

        return $this->render('security/change_password.html.twig',[
            'last_username' => $last_username,
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }
}

``` 

Formulaire de modification de mot de passe ` App/Template/Security/change_password.html.twig  `

```
{% extends 'security_layout.html.twig' %}

{% block title %}{{parent()}} Mot de passe{% endblock %}

{% block body %}
    <div class="card-body">
        <form action="{{ path('app_change_password') }}" method="post" class="js-validate needs-validation" novalidate>
            <div class="text-center">
                <div class="mb-5">
                    <h1 class="display-5">Mot de passe</h1>
                    {% for label, messages in app.flashes %}
                        {% for message in messages %}
                            <div class="alert alert-{{ label }}" role="alert">
                                {{ message }}
                            </div>
                        {% endfor %}
                    {% endfor %}
                    <span class="divider-center text-muted mb-4"></span>
                </div>
            </div>
            {% if error %}
                <div class="alert alert-danger">{{ error.messageKey|trans(error.messageData, 'security') }}</div>
            {% endif %}

            {% if app.user %}
                <div class="mb-3">
                    Vous êtes connecté en tant que {{ app.user.userIdentifier }}, <a href="{{ path('app_logout') }}">Deconnexion</a>
                </div>
            {% endif %}


            <div class="mb-4">
                <label class="form-label" for="inputUsername">Login</label>
                <input type="text" value="{{ last_username }}" name="_username" id="inputUsername" class="form-control form-control-lg" autocomplete="username" tabindex="1" required autofocus>
            </div>

            <div class="mb-4">
                <label class="form-label" for="inputPassword">Nouveau mot de passe</label>
                <div class="input-group input-group-merge" data-hs-validation-validate-class>
                    <input type="password" class="js-toggle-password form-control form-control-lg" name="_password" id="inputPassword" placeholder="8+ caractères" aria-label="8+ characters required" required minlength="8" data-hs-toggle-password-options='{
                           "target": "#changePassTarget",
                           "defaultClass": "bi-eye-slash",
                           "showClass": "bi-eye",
                           "classChangeTarget": "#changePassIcon"
                         }' autocomplete="current-password" tabindex="2">
                    <a id="changePassTarget" class="input-group-append input-group-text" href="javascript:;">
                        <i id="changePassIcon" class="bi-eye"></i>
                    </a>
                </div>

                <span class="invalid-feedback">Entrez un mot de passe valide.</span>
            </div>
            <input type="hidden" name="_csrf_token"
                   value="{{ csrf_token('authenticate') }}"
            >

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg" tabindex="3">Modifier le mot de passe</button>
            </div>
        </form>
    </div>
{% endblock %}

``` 
</p>
</details>

<details><summary> C- AuthenticatorEventSubscriber  </summary>

Ce eventSubscriber permet de gerer les logs des utilisateurs connectés.
` App/src/EventSubscriber/AuthenticatorSubscriber `

<p>

```
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

```
</p>
</details>