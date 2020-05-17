<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssetRepository")
 */
class Asset
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")\
     * @Assert\NotBlank(message="Must not be blank")
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=4)
     * @Assert\Choice(choices = { "BTC", "ETH", "I0TA" }, message="Choose a valid currency.")
     * @Assert\NotBlank(message="Must not be blank")
     */
    private $currency;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive(message="Value must be positive")
     * @Assert\NotBlank(message="Must not be blank")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="assets")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency)
    {
        $this->currency = $currency;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(float $value)
    {
        $this->value = $value;

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
