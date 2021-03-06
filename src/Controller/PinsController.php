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
use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods="GET")
     */
    public function index(Request $request, PinRepository $pinRepository, PaginatorInterface $paginator): Response
    {
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);

        $pins = $paginator->paginate(
            $pins, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
        

        return $this->render('pins/index.html.twig', compact('pins'));
    }

    /**
     * @route("/pins/create", name="app_pins_create", methods="GET|POST")
     */

    public function create(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $pin = new Pin();

        // create form whith form/PinType
        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'pin creé avec succes!');

            // dd($form->getData());

            return $this->redirectToRoute('app_home');
            # code...
        }

        return $this->render('pins/create.html.twig', [
            'form' => $form->createView(),
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
     * @Route("/pins/{id}<[0-9]+>/edit", name="app_pins_edit", methods="GET|PUT")
     */

    public function edit(
        Request $request,
        EntityManagerInterface $em,
        Pin $pin
    ): Response {
        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'pin mofifié avec succes!');

            return $this->redirectToRoute('app_home');
            # code...
        }
        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @route("/pins/{id<[0-9]+>}/delete", name="app_pins_delete", methods="DELETE")
     */

    public function delete(
        Request $request,
        Pin $pin,
        EntityManagerInterface $em
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'pin_deletion_' . $pin->getId(),
                $request->request->get('csrf_token')
            )
        ) {
            $em->remove($pin);
            $em->flush();

            $this->addFlash('info', 'pin supprimé avec succes!');
        }

        return $this->redirectToRoute('app_home');
    }
}
