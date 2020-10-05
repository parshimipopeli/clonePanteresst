<?php

namespace App\Controller;
use App\Entity\Pin;
use App\Form\PinType;
use Doctrine\ORM\EntityManager;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods="GET")
     */
    public function index(PinRepository $pinRepository): Response
    {

        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);


        return $this->render('pins/index.html.twig', compact('pins')) ;
    }

    /**
     * @route("/pins/create", name="app_pins_create", methods="GET|POST")
     */

    public function create(Request $request, EntityManagerInterface $em): Response
{

    $pin = new Pin;

    // create form whith form/PinType
    $form = $this->createForm(PinType::class, $pin);
        
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($pin);
            $em->flush();

            return $this->redirectToRoute('app_home');
            # code...
        }

        return $this->render('pins/create.html.twig', [
            'formCreate' => $form->createView()

        ]);

    }


    /**
     * @Route("/pins/{id}", name="app_pins_show", methods="GET")
     */
    public function show(Pin $pin): Response
    {
        
return $this->render('pins/show.html.twig', compact('pin'));

    }

    /**
     * @Route("/pins/{id}/edit", name="app_pins_edit", methods="GET|PUT")
     */

    public function edit(Request $request, EntityManagerInterface $em, Pin $pin): Response
    {
        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'
        ]);
            

        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();

            return $this->redirectToRoute('app_home');
            # code...

    }
            return $this->render('pins/edit.html.twig', [
                'pin' => $pin,
                'form' => $form->createView()
            ]);


}
}