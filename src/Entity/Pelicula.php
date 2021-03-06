<?php

namespace App\Entity;

use App\Repository\PeliculaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PeliculaRepository::class)
 */
class Pelicula
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $titulo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duracion;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $genero;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $director;

    /**
     * @ORM\Column(type="string", length=4096, nullable=true)
     */
    private $sinopsis;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $estreno;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $valoracion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $caratula;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="peliculas")
     */
    private $user;
    
    // public function __toString(){
    //     return "$this->titulo($this->duracion m), de $this->director. Género $this->genero";
    // }


    // método __toString de Pelcula (útil para)
    public function __toString(){
        return "ID: $this->id - $this->titulo
                ($this->estreno - $this->duracion min, de $this->director.
                GÉNERO: $this->genero
                VAL: ".($this->valoracion ?? 'Sin valorar')." / 5";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDuracion(): ?int
    {
        return $this->duracion;
    }

    public function setDuracion(?int $duracion): self
    {
        $this->duracion = $duracion;

        return $this;
    }

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function setDirector(?string $director): self
    {
        $this->director = $director;

        return $this;
    }

    public function getGenero(): ?string
    {
        return $this->genero;
    }

    public function setGenero(?string $genero): self
    {
        $this->genero = $genero;

        return $this;
    }

    public function getSinopsis(): ?string
    {
        return $this->sinopsis;
    }

    public function setSinopsis(?string $sinopsis): self
    {
        $this->sinopsis = $sinopsis;

        return $this;
    }

    public function getEstreno(): ?int
    {
        return $this->estreno;
    }

    public function setEstreno(?int $estreno): self
    {
        $this->estreno = $estreno;

        return $this;
    }

    public function getValoracion(): ?int
    {
        return $this->valoracion;
    }

    public function setValoracion(?int $valoracion): self
    {
        $this->valoracion = $valoracion;

        return $this;
    }

    public function getCaratula(): ?string
    {
        return $this->caratula;
    }

    public function setCaratula(?string $caratula): self
    {
        $this->caratula = $caratula;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
