<?php

namespace App\Entity;

use App\Repository\BankAccountRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BankAccountRepository::class)]
class BankAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getBankAccounts', 'getClients'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['getBankAccounts', 'getClients'])]
    private ?int $number = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getBankAccounts', 'getClients'])]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups(['getBankAccounts', 'getClients'])]
    private ?float $amount = null;

    #[ORM\Column]
    #[Groups(['getBankAccounts', 'getClients'])]
    private ?bool $overdraft = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['getBankAccounts', 'getClients'])]
    private ?int $interestRate = null;

    #[ORM\ManyToOne(inversedBy: 'bankAccounts')]
    #[Groups(['getBankAccounts'])]
    private ?Client $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function isOverdraft(): ?bool
    {
        return $this->overdraft;
    }

    public function setOverdraft(bool $overdraft): static
    {
        $this->overdraft = $overdraft;

        return $this;
    }

    public function getInterestRate(): ?int
    {
        return $this->interestRate;
    }

    public function setInterestRate(int $interestRate): static
    {
        $this->interestRate = $interestRate;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }
}
