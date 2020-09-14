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
            $card = $this->getDoctrine()->getRepository(Card::class)->find($id);

            $form = $this->createFormBuilder($card)
                ->add('cardName', TextType::class, [
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
         * @Route("/yugioh/{id}", name="see_sets")
         */
        
        public function seeSetNames($id) {
            $card = $this->getDoctrine()->getRepository(Card::class)->find($id);
    
            //curl request api
            $url = 'http://yugiohprices.com/api/card_versions/'.urlencode($card->getCardName());
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
            $response = json_decode($response, true);
            curl_close($curl);
        
            //trying to foreach set tags into 1 array
            $printTagList = [];
            $setTarget = $response['data'];
            foreach($setTarget as $printTag) {
                array_push($printTagList, $printTag['print_tag']);
            }
            $readableTagList = implode(', ', $printTagList);
            $card->setSetNames($readableTagList);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($card);
            $entityManager->flush();

            return $this->render('yugioh/sets.html.twig', [
                'card' => $card
            ]);
        }

        /**
         * @Route("/yugioh/rarity", name="see_rarity")
         */
        public function seeRarity($setNames) {
            //trying to target the setNames column
            $card = $this->getDoctrine()->getRepository(Card::class)->find($setNames);
            //undo implode from seeSetName function
            $setNameArray = explode(', ', $setNames);
            foreach ($setNameArray as $set) {
                //curl api request for each element in exploded array
                $url = 'http://yugiohprices.com/api/price_for_print_tag/'.urlencode($setNameArray);
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($curl);
                $response = json_decode($response, true);
                curl_close($curl);
                //if the element in array matches curl requested print tag then set the rarity field
                $shorterResponse = $response['data']['price_data'];
                if ($setNameArray === $shorterResponse['price_tag']) {
                    $card->setRarity($shorterResponse['rarity']);
                    //then persist
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($card);
                    $entityManager->flush();
                }
            }
            return $this->render('yugioh/rarity.html.twig', [
                'card' => $card
            ]);
        }

        /**
         * @Route("/yugioh/delete/{id}", name="delete_card")
         * @Method({"DELETE"})
         */
        public function delete(Request $request, $id) {
            $card = $this->getDoctrine()->getRepository(Card::class)->find($id);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($card);
            $entityManager->flush();

            return $this->redirect($request->headers->get('referer'));
        }
    }

    //js main is no longer involved in this app. disregard it
    //don't delete it tho. remember what robert said about deleting shit
?>