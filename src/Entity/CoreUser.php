<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */
class CoreUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ConfirmationToken;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $PasswordRequestedAt;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Local;

}