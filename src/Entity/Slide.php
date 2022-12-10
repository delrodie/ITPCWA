<?php

namespace App\Entity;

use App\Repository\SlideRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SlideRepository::class)]
class Slide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le titre du slide ne peut être null")]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    /*#[Assert\Image(
        minWidth: 920, maxWidth: 1920,
        maxHeight: 865, minHeight: 432,
        maxWidthMessage: "La largeur maximum de votre image doit être 1920px", minWidthMessage: "La largeur minimum de votre image doit être 920px",
        maxHeightMessage: "La hauteur maximum de votre image doit être 865px", minHeightMessage: "La hauteur minimum de votre image doit être 432px"
    )]*/
    private ?string $media = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(?string $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(?bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }
}
