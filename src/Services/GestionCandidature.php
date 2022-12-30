<?php

namespace App\Services;

use App\Repository\CandidatRepository;
use Flasher\Prime\Flasher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class GestionCandidature
{
    public function __construct(
        private GestionMedia $gestionMedia, private CandidatRepository $candidatRepository,
        private TranslatorInterface $translator, private Flasher $flasher,
        private UrlGeneratorInterface $urlGenerator
    )
    {
    }

    /**
     * @param $form
     * @param $request
     * @param $entity
     * @return RedirectResponse
     */
    public function postulerRequest($form, $request, $entity): RedirectResponse
    {
        // Gestion des appels d'offres
        $localeReference = $request->request->get('_localeReference');
        $reCaptcha = $request->request->get('g-recaptcha-response'); //dd($reCaptcha);
        if (!$localeReference || !$reCaptcha){
            $this->flasher
                ->create('sweetalert')
                ->icon('error')
                ->addError($this->translator->trans("Please resume your application"));

            return new RedirectResponse($this->urlGenerator->generate('app_frontend_recruitment_show',[
                '_locale' => $request->get('_locale'),
                'slug' => $request->get('slug')
            ]));
        }

        // Verification de l'unicité de la candidature sur cet appel
        $candidat = $this->candidatRepository->findOneBy(['email' => $entity->getEmail(), 'jobReference' => $localeReference]);
        if ($candidat){
            $this->flasher
                ->create('sweetalert')
                ->icon('error')
                ->addError($this->translator->trans("Your application submission failed because you have already applied for this recruitment!"));

            return new RedirectResponse($this->urlGenerator->generate('app_frontend_recruitment_show',[
                '_locale' => $request->get('_locale'),
                'slug' => $request->get('slug')
            ], UrlGeneratorInterface::ABSOLUTE_URL));
        }


        $mediaCVFile = $form->get('mediaCV')->getData();
        $mediaLettreFile = $form->get('mediaLettre')->getData();
        if ($mediaCVFile && $mediaLettreFile){
            $mediaCV = $this->gestionMedia->upload($mediaCVFile, 'candidat');
            $mediaLettre = $this->gestionMedia->upload($mediaLettreFile, 'candidat');

            $entity->setMediaCV($mediaCV);
            $entity->setMediaLettre($mediaLettre);
            $entity->setJobReference($localeReference);
            $entity->setMatricule($this->matricule());

            $this->candidatRepository->save($entity, true);

            $this->flasher
                ->create('sweetalert')
                ->icon('success')
                ->addSuccess($this->translator->trans("Your application has been sent successfully! However, note that only those selected who will be contacted"));

            return new RedirectResponse( $this->urlGenerator->generate('app_frontend_recruitment',[
                '_locale' => $request->get('_locale'),
            ], UrlGeneratorInterface::ABSOLUTE_URL));
        }
        else {
            $this->flasher
                ->create('sweetalert')
                ->icon("error")
                ->addError($this->translator->trans("You must include your CV and cover letter to apply."));
            return new RedirectResponse();
        }
    }

    /**
     * Generation du matricule du candidat en fonction
     * L'année sur deux positions ainsi que le mois,
     * Le formattage de l'id sur trois positions et la première lettre du jour
     *
     * @return string
     */
    protected function matricule(): string
    {
        $candidat = $this->candidatRepository->findOneBy([],['id'=>"DESC"]);
        if (!$candidat) $id = 1;
        else $id = (int) $candidat->getId() + 1;

        // Formattage de l'incrementation
        if ($id < 10) $inc = '00'.$id;
        elseif ($id < 100) $inc = '0'.$id;
        else $inc = $id;

        // Premiere lettre du jour
        $init = substr(date('l'), 0, 1);

        return date('ym').$inc.$init;
    }
}