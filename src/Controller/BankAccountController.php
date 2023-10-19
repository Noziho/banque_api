<?php

namespace App\Controller;

use App\Entity\BankAccount;
use App\Entity\Client;
use App\Repository\BankAccountRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class BankAccountController extends AbstractController
{
    #[Route('/api/bank', name: 'app_bank_accounts', methods: ['GET'])]
    public function getAll(BankAccountRepository $bankAccountRepository, SerializerInterface $serializer): JsonResponse
    {
        $bankAccount = $bankAccountRepository->findAll();

        if ($bankAccount){
            $jsonBankAccount = $serializer->serialize($bankAccount, 'json', [
                'groups' => 'getBankAccounts'
            ]);
            return new JsonResponse($jsonBankAccount, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(["message" => "Bank accounts not found"], Response::HTTP_NOT_FOUND, [], true);
    }

    #[Route('/api/bank/{id}', name: 'app_bank_get_id', methods: ['GET'])]
    public function getById(
        int $id,
        BankAccountRepository $bankAccountRepository,
        SerializerInterface $serializer,
    ): JsonResponse
    {

        $bankAccount = $bankAccountRepository->find($id);

        if ($bankAccount){
            $jsonBankAccount = $serializer->serialize($bankAccount, 'json', [
                'groups' => 'getBankAccounts'
            ]);
            return new JsonResponse($jsonBankAccount, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(["message" => "Bank account not found"], Response::HTTP_NOT_FOUND, [], true);
    }

    #[Route('/api/bank/{id}', name: 'app_bank_account_delete', methods: ['DELETE'])]
    public function delete
    (int $id,
     EntityManagerInterface $em,
     BankAccountRepository $bankAccountRepository
    ): JsonResponse
    {
        $bankAccount = $bankAccountRepository->find($id);

        if ($bankAccount) {
            $em->remove($bankAccount);
            $em->flush();

            return new JsonResponse(["message" => "Bank account successfully deleted"], Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(["message" => "Bank account not found"], Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/bank', name: 'app_bank_account_create', methods: ['POST'])]
    public function create
    (Request $request,
     EntityManagerInterface $em,
     SerializerInterface $serializer,
     ClientRepository $clientRepository,
    ): JsonResponse
    {
        $bankAccount = $serializer->deserialize($request->getContent(), BankAccount::class, 'json');
        $content = $request->toArray();
        $client = $clientRepository->find($content['clientId']);
        $bankAccount->setClient($client);
        $em->persist($bankAccount);
        $em->flush();

        $jsonBankAccount = $serializer->serialize($bankAccount, 'json', ['groups' => 'getBankAccounts']);
        return new JsonResponse($jsonBankAccount, Response::HTTP_OK, [], true);
    }

    #[Route('/api/bank/{id}', name: "update_bank_account", methods: ['PUT'])]
    public function updateBankAccount
    (
        Request $request,
        SerializerInterface $serializer,
        BankAccount $currentBankAccount,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $updatedBankAccount = $serializer->deserialize($request->getContent(),
            BankAccount::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentBankAccount]
        );

        $em->persist($updatedBankAccount);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/banks/find', name: 'app_bankaccount_findbyusername', methods: ['GET'])]
    public function findByUserName
    (
     BankAccountRepository $bankRepository,
     SerializerInterface $serializer,
        Request $request
    ): JsonResponse
    {
        $content = $request->toArray();
        $jsonBankAccount = $serializer->serialize
        ($bankRepository->findByUserName($content['name']), 'json', ['groups' => 'getBankAccounts']);

        return new JsonResponse($jsonBankAccount, Response::HTTP_OK, [], true);
    }

    #[Route('/api/bank/interest/{id}', name: 'app_bankaccount_getinterestrate', methods: ['GET'])]
    public function getInterestRate
    (int $id,
     BankAccountRepository $bankAccountRepository,
     EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $bankAccount = $bankAccountRepository->find($id);
        if ($bankAccount->getType() === "epargne"){
            $bankAccount->setAmount($bankAccount->getAmount() * $bankAccount->getInterestRate() / 100);
            $entityManager->persist($bankAccount);
            $entityManager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }
        return new JsonResponse(null, Response::HTTP_OK);
    }

}
