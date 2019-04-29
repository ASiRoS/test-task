<?php

namespace App\Controller;

use App\Serializer\EntityDenormalizer;
use App\Serializer\EntityNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ApiController
 * @package App\Controller
 * @Route("/api")
 */
abstract class ApiController extends AbstractController
{
    abstract protected static function getEntity();

    /**
     * @Route("", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorInterface $validator, SerializerInterface $serializer): JsonResponse
    {
        $entity = static::getEntity();
        $category = $serializer->deserialize($request->getContent(), $entity, 'json');

        $errors = $validator->validate($category);

        if(count($errors) > 0) {
            return $this->json(['errors' => $errors]);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($category);
        $manager->flush();

        return $this->json($category);
    }

    /**
     * @Route("", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function select(Request $request): JsonResponse
    {
        $conditions = json_decode($request->getContent(), true);

        $repository = $this->getDoctrine()->getRepository(static::getEntity());

        if(!empty($conditions)) {
            $result = $repository->findBy($conditions);
        } else {
            $result = $repository->findAll();
        }

        return $this->json($result);
    }
}