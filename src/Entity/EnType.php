<?php

namespace App\Entity;

use App\Repository\EnTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnTypeRepository::class)]
class EnType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(nullable: true)]
    private ?int $pageIndex = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\OneToOne(mappedBy: 'type', cascade: ['persist', 'remove'])]
    private ?EnPresentation $presentation = null;

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

    public function getPageIndex(): ?int
    {
        return $this->pageIndex;
    }

    public function setPageIndex(?int $pageIndex): self
    {
        $this->pageIndex = $pageIndex;

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

    public function getPresentation(): ?EnPresentation
    {
        return $this->presentation;
    }

    public function setPresentation(?EnPresentation $presentation): self
    {
        // unset the owning side of the relation if necessary
        if ($presentation === null && $this->presentation !== null) {
            $this->presentation->setType(null);
        }

        // set the owning side of the relation if necessary
        if ($presentation !== null && $presentation->getType() !== $this) {
            $presentation->setType($this);
        }

        $this->presentation = $presentation;

        return $this;
    }

    public function __toString(): string
    {
        return $this->titre;
    }
}
