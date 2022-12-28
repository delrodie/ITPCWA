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
    public function __construct(
        $slideDirectory, $presentationDirectory, $actualiteDirectory, $projetDirectory,
        $ressourceDirectory, $jobDirectory
    )
    {
        $this->mediaSlide = $slideDirectory;
        $this->mediaPresentation = $presentationDirectory;
        $this->mediaActualite = $actualiteDirectory;
        $this->mediaProjet = $projetDirectory;
        $this->mediaRessource = $ressourceDirectory;
        $this->mediaJob = $jobDirectory;
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
        else return false;

        return true;
    }
}