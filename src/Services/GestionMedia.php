<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

class GestionMedia
{
    private $mediaSlide;
    private $mediaPresentation;
    private $mediaActualite;
    private $mediaProjet;
    private $mediaRessource;
    private $mediaJob;
    private $mediaCandidat;
    private $mediaMultimedia;
    private $mediaBienvenue;
    public function __construct(
        $slideDirectory, $presentationDirectory, $actualiteDirectory, $projetDirectory,
        $ressourceDirectory, $jobDirectory, $candidatDirectory, $multimediaDirectory, $bienvenueDirectory
    )
    {
        $this->mediaSlide = $slideDirectory;
        $this->mediaPresentation = $presentationDirectory;
        $this->mediaActualite = $actualiteDirectory;
        $this->mediaProjet = $projetDirectory;
        $this->mediaRessource = $ressourceDirectory;
        $this->mediaJob = $jobDirectory;
        $this->mediaCandidat = $candidatDirectory;
        $this->mediaMultimedia = $multimediaDirectory;
        $this->mediaBienvenue = $bienvenueDirectory;
    }


    /**
     * @param UploadedFile $file
     * @param $media
     * @return string
     */
    public function upload(UploadedFile $file, $media = null)
    {
        // Initialisation du slug
        $slugify = new AsciiSlugger();

        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugify->slug($originalFileName);
        $newFilename = $safeFilename.'-'.Time().'.'.$file->guessExtension();

        // Deplacement du fichier dans le repertoire dedié
        try {
            if ($media === 'slide') $file->move($this->mediaSlide, $newFilename);
            elseif ($media === 'presentation') $file->move($this->mediaPresentation, $newFilename);
            elseif ($media === 'actualite') $file->move($this->mediaActualite, $newFilename);
            elseif ($media === 'projet') $file->move($this->mediaProjet, $newFilename);
            elseif ($media === 'ressource') $file->move($this->mediaRessource, $newFilename);
            elseif ($media === 'job') $file->move($this->mediaJob, $newFilename);
            elseif ($media === 'candidat') $file->move($this->mediaCandidat, $newFilename);
            elseif ($media === 'multimedia') $file->move($this->mediaMultimedia, $newFilename);
            elseif ($media === 'bienvenue') $file->move($this->mediaBienvenue, $newFilename);
            else $file->move($this->mediaSlide, $newFilename);
        }catch (FileException $e){

        }

        return $newFilename;
    }

    /**
     * Suppression de l'ancien media sur le server
     *
     * @param $ancienMedia
     * @param null $media
     * @return bool
     */
    public function removeUpload($ancienMedia, $media = null): bool
    {
        if ($media === 'slide') unlink($this->mediaSlide.'/'.$ancienMedia);
        elseif ($media === 'presentation') unlink($this->mediaPresentation.'/'.$ancienMedia);
        elseif ($media === 'actualite') unlink($this->mediaActualite.'/'.$ancienMedia);
        elseif ($media === 'projet') unlink($this->mediaProjet.'/'.$ancienMedia);
        elseif ($media === 'ressource') unlink($this->mediaRessource.'/'.$ancienMedia);
        elseif ($media === 'job') unlink($this->mediaJob.'/'.$ancienMedia);
        elseif ($media === 'candidat') unlink($this->mediaCandidat.'/'.$ancienMedia);
        elseif ($media === 'multimedia') unlink($this->mediaMultimedia.'/'.$ancienMedia);
        elseif ($media === 'bienvenue') unlink($this->mediaBienvenue.'/'.$ancienMedia);
        else return false;

        return true;
    }
}