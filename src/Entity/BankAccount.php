<?php

namespace App\Entity;

use App\Repository\BankAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(mappedBy: 'bankAccount', targetEntity: Transaction::class)]
    private Collection $transaction;

    public function __construct()
    {
        $this->transaction = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransaction(): Collection
    {
        return $this->transaction;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transaction->contains($transaction)) {
            $this->transaction->add($transaction);
            $transaction->setBankAccount($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transaction->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getBankAccount() === $this) {
                $transaction->setBankAccount(null);
            }
        }

        return $this;
    }
}
