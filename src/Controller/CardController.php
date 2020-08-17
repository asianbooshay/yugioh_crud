<?php 
    namespace App\Controller;

    use App\Entity\Card;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;

    class CardController extends AbstractController {
        /**
         * @Route("/", name="yugioh_list")
         * @Method({"GET"})
         */
        public function index() {
            $cards = $this->getDoctrine()
                ->getRepository(Card::class)
                ->findAll();
            return $this->render('yugioh/index.html.twig', [
                'cards' => $cards
            ]);
        }

        /**
         * @Route("/yugioh/new", name="new_card")
         * Method({"GET", "POST"})
         */
        public function new(Request $request) {
            $card = new Card();

            $form = $this->createFormBuilder($card)
                ->add('cardName', TextType::class, [
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ])
                ->add('setName', TextType::class, [
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ])
                ->add('rarity', TextType::class, [
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Create',
                    'attr' => [
                        'class' => 'btn btn-primary mt-3'
                    ]
                ])
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $card = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($card);
                $entityManager->flush();

                return $this->redirectToRoute('yugioh_list');
            }

            return $this->render('yugioh/new.html.twig', [
                'form' => $form->createView()
            ]);
        }

        /**
         * @Route("/yugioh/edit/{id}", name="edit_card")
         * Method({"GET", "POST"})
         */
        public function edit(Request $request, $id) {
            $card = new Card();
            $card = $this->getDoctrine()->getRepository
            (Card::class)->find($id);

            $form = $this->createFormBuilder($card)
                ->add('cardName', TextType::class, [
                    'attr' => [
                    'class' => 'form-control'
                    ]
                ])
                ->add('setName', TextType::class, [
                    'attr' => [
                    'class' => 'form-control'
                    ]
                ])
                ->add('rarity', TextType::class, [
                    'attr' => [
                    'class' => 'form-control'
                    ]
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Update',
                    'attr' => [
                        'class' => 'btn btn-primary mt-3'
                    ]
                ])
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()
                    ->getManager();
                $entityManager->flush();

                return $this->redirectToRoute('yugioh_list');
            }

            return $this->render('yugioh/edit.html.twig', [
                'form' => $form->createView()
            ]);
        }

        
        /**
         * @Route("/yugioh/{id}", name="card_show")
         */
        public function show($id) {
            $card = $this->getDoctrine()->getRepository
            (Card::class)->find($id);
            return $this->render('yugioh/show.html.twig', [
                'card' => $card
            ]);
        }

        /**
         * @Route("/yugioh/delete/{id}", name="delete_card")
         * @Method({"DELETE"})
         */
        public function delete(Request $request, $id) {
            $card = $this->getDoctrine()->getRepository
            (Card::class)->find($id);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($card);
            $entityManager->flush();

            return $this->redirect($request->headers->get('referer'));
        }
    }

    //js main is no longer involved in this app. disregard it
    //don't delete it tho. remember what robert said about deleting shit
?>