<?php

namespace App\Controller;

use App\Entity\BankAccount;
use App\Entity\Transaction;
use App\Repository\BankAccountRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    #[Route('/api/transaction/{currentAccount}/{targetAccount}/{value}',
        name: "app_transaction_payment", methods: ['PUT'])]
    public function payment
    (BankAccount $currentAccount,
     BankAccount $targetAccount,
     EntityManagerInterface $em,
     float $value
    ):JsonResponse
    {
        $currentTransaction = new Transaction();
        $currentTransaction->setDate(new DateTime());
        $currentTransaction->setAmount($value);
        $currentTransaction->setType('virement');
        $currentTransaction->setBankAccount($currentAccount);
        $currentTransaction->setTransferDirection('Sortant');

        $targetTransaction = new Transaction();
        $targetTransaction->setDate(new DateTime());
        $targetTransaction->setAmount($value);
        $targetTransaction->setType('virement');
        $targetTransaction->setBankAccount($targetAccount);
        $targetTransaction->setTransferDirection("Entrant");

        $currentAccount->setAmount($currentAccount->getAmount() - $value);
        $targetAccount->setAmount($targetAccount->getAmount() + $value);

        $em->persist($currentTransaction);
        $em->persist($targetTransaction);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/transaction/{currentAccount}/{value}', name: "app_transaction_deposit", methods: ['PUT'])]
    public function deposit
    (BankAccount $currentAccount,
     EntityManagerInterface $em,
     float $value
    ):JsonResponse
    {
        $transaction = new Transaction();
        $transaction->setDate(new DateTime());
        $transaction->setAmount($value);
        $transaction->setType('depot');
        $transaction->setBankAccount($currentAccount);
        $transaction->setTransferDirection('Entrant');

        $currentAccount->setAmount($currentAccount->getAmount() + $value);

        $em->persist($transaction);

        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/transaction/withdrawal/{currentAccount}/{value}', name: "app_transaction_withdrawal", methods: ['GET'])]
    public function withdrawal
    (BankAccount $currentAccount,
     EntityManagerInterface $em,
     BankAccountRepository $bankRepository,
     float $value
    ):JsonResponse
    {
        $transaction = new Transaction();
        $transaction->setDate(new DateTime());
        $transaction->setAmount($value);
        $transaction->setType('retrait');
        $transaction->setBankAccount($currentAccount);
        $transaction->setTransferDirection('Sortant');

        $currentAccount->setAmount($currentAccount->getAmount() - $value);

        $em->persist($transaction);

        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
