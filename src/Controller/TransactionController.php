<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Transaction;
use App\Entity\AccountBalance;
use App\Entity\MaxTransactionVolume;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/", name="transaction_")
 */
class TransactionController extends AbstractController
{
    /**
     * @Route("/ping", name="ping", methods={"GET"})
     */
    public function ping(): Response
    {
        /* Empty Response */
        return new Response('HTTP_OK', Response::HTTP_OK);
    }

    /**
     * @Route("/amount", name="amount")
     */
    public function amount(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        if (!$request->isMethod('POST')) {
            return new Response('Specified HTTP method not allowed.', Response::HTTP_METHOD_NOT_ALLOWED);
        }

        if (
            !$request->headers->has('Transaction-Id')
            || !preg_match(
                '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i',
                $request->headers->get('Transaction-Id')
            )
        ) {
            return new Response('Incorrect transaction ID type.', Response::HTTP_BAD_REQUEST);
        }

        try {
            $transaction = new Transaction();
            list(
                "account_id" => $account_id,
                "amount" => $amount
            ) = json_decode($request->getContent(), JSON_OBJECT_AS_ARRAY);

            $account = $this->getDoctrine()
                ->getRepository(AccountBalance::class)
                ->find($account_id);

            if (!$account) {
                return new Response('Account not found', Response::HTTP_NOT_FOUND);
            }

            if (empty((int) $amount)) {
                return new Response('Incorrect amount value type.', Response::HTTP_BAD_REQUEST);
            }
            $transaction->setId($request->headers->get('Transaction-Id'));
            $transaction->setAccount($account);
            $transaction->setAmount((int) $amount);

            $errors = $validator->validate($transaction);
            if (count($errors) > 0) {
                return new Response('Transaction ID already used.', Response::HTTP_BAD_REQUEST);
            }

            $account->setBalance($account->getBalance() - (int) $amount);

            $entityManager->persist($transaction);
            $entityManager->flush();

            /* Max Transaction Volume update */
            $hasMaxVolume = true;
            $maxSavedVolume = $this->getDoctrine()
                ->getRepository(MaxTransactionVolume::class)
                ->findMaxTransactionVolume();

            if (!$maxSavedVolume) {
                $maxSavedVolume = new MaxTransactionVolume();
                $hasMaxVolume = false;
            }

            $transactionsVolumes = $this->getDoctrine()
                ->getRepository(Transaction::class)
                ->findTransactionVolume();

            $tNumber = $transactionsVolumes[0]['tNumber'];
            $maxSavedVolume->setMaxVolume($tNumber);
            foreach ($transactionsVolumes as $tVolume) {
                if ($tNumber === $tVolume['tNumber']) {
                    $maxSavedVolume->addAccount(
                        $tVolume['transaction']->getAccount()
                    );
                    continue;
                }
                break;
            }
            if (!$hasMaxVolume) {
                $entityManager->persist($maxSavedVolume);
            }
            $entityManager->flush();
        } catch (ErrorException $e) {
            return new Response('Specified content type not allowed.', Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        } catch (Exception $e) {
            var_dump($e->getMessage());
            return new Response('Internal server error.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('Transaction created.', Response::HTTP_OK);
    }

    /**
     * @Route("/transaction/{transaction_id}", name="transaction", methods={"GET"})
     */
    public function transaction(?string $transaction_id): Response
    {

        $transaction = $this->getDoctrine()
            ->getRepository(Transaction::class)
            ->find($transaction_id);

        if (!$transaction) {
            return new Response('Transaction not found', Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'account_id' => $transaction->getId(),
            'amount' => $transaction->getAmount(),
        ]);
    }

    /**
     * @Route("/balance/{account_id}", name="balance", methods={"GET"})
     */
    public function balance(?string $account_id): Response
    {

        $account = $this->getDoctrine()
            ->getRepository(AccountBalance::class)
            ->find($account_id);

        if (!$account) {
            return new Response('Account not found', Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'balance' => $account->getBalance(),
        ]);
    }

    /**
     * @Route("/max_transaction_volume", name="max_transaction_volume", methods={"GET"})
     */
    public function maxTransactionVolume(): Response
    {
        $maxVolume = $this->getDoctrine()
            ->getRepository(MaxTransactionVolume::class)
            ->findMaxTransactionVolume();

        if (!$maxVolume) {
            return new Response('Not found', Response::HTTP_NOT_FOUND);
        }

        $accounts = $maxVolume->getAccounts()->map(function ($account) {
            return $account->getId();
        })->toArray();

        return $this->json([
            'maxVolume' => $maxVolume->getMaxVolume(),
            'accounts' => $accounts,
        ]);
    }
}
