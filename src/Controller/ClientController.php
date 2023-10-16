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

class ClientController extends AbstractController
{
    #[Route('/api/client', name: 'app_client', methods: ['GET'])]
    public function getAll(ClientRepository $clientRepository, SerializerInterface $serializer): JsonResponse
    {
        $clientList = $clientRepository->findAll();
        $jsonClientList = $serializer->serialize($clientList, 'json', ["groups" => "getClients"]);
        return new JsonResponse($jsonClientList, Response::HTTP_OK, [''], true);
    }

    #[Route('/api/client/{id}', name: 'app_client_get_id', methods: ['GET'])]
    public function getById(int $id, ClientRepository $clientRepository, SerializerInterface $serializer): JsonResponse
    {
        $client = $clientRepository->find($id);
        if ($client) {
            $jsonClient = $serializer->serialize($client, 'json', ["groups" => "getClients"]);
            return new JsonResponse($jsonClient, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(["message" => "Client not found"], Response::HTTP_NOT_FOUND, [], true);

    }

    #[Route('/api/client', name: 'app_client_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $client = $serializer->deserialize($request->getContent(), Client::class, 'json');

        $em->persist($client);
        $em->flush();

        $jsonClient = $serializer->serialize($client, 'json');

        return new JsonResponse($jsonClient, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/client/{id}', name: 'app_client_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em, ClientRepository $clientRepository): JsonResponse
    {
        $client = $clientRepository->find($id);

        if ($client) {
            $em->remove($client);
            $em->flush();

            return new JsonResponse(["message" => "Author successfully deleted"], Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(["message" => "Author already deleted"], Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/client/{id}', name: "update_client", methods: ['PUT'])]
    public function updateClient
    (
        Request $request,
        SerializerInterface $serializer,
       Client $currentClient,
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $updatedClient = $serializer->deserialize($request->getContent(),
            Client::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentClient]
        );

        $em->persist($updatedClient);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/client/{currentAccount}/{targetAccount}/{value}', name: "app_client_payment", methods: ['PUT'])]
    public function payment
    (BankAccount $currentAccount,
     BankAccount $targetAccount,
     EntityManagerInterface $em,
     float $value
    ):JsonResponse
    {
        $currentAccount->setAmount($currentAccount->getAmount() - $value);
        $targetAccount->setAmount($targetAccount->getAmount() + $value);

        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/client/{currentAccount}/{value}', name: "app_client_deposit", methods: ['PUT'])]
    public function deposit
    (BankAccount $currentAccount,
     EntityManagerInterface $em,
     float $value
    ):JsonResponse
    {
        $currentAccount->setAmount($currentAccount->getAmount() + $value);

        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/client/{currentAccount}/{value}', name: "app_client_deposit", methods: ['PUT'])]
    public function withdrawal
    (BankAccount $currentAccount,
     EntityManagerInterface $em,
     float $value
    ):JsonResponse
    {
        $currentAccount->setAmount($currentAccount->getAmount() - $value);

        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
