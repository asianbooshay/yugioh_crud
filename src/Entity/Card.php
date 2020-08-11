<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CardRepository::class)
 */
class Card
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=100)
     */
    private $cardName;

    /**
     * @ORM\Column(type="text", length=100)
     */
    private $setName;

    /**
     * @ORM\Column(type="text", length=100)
     */
    private $rarity;

    // Getters & Setters
    public function getId() {
        return $this->id;
    }

    public function getCardName() {
        return $this->cardName;
    }

    public function setcardName($cardName) {
        $this->cardName = $cardName;
    }

    public function getSetName() {
        return $this->setName;
    }

    public function setSetName($setName) {
        $this->setName = $setName;
    }

    public function getRarity() {
        return $this->rarity;
    }

    public function setRarity($rarity) {
        $this->rarity = $rarity;
    }

}
