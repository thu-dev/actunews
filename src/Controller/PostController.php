<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Sodium\add;

/**
 *@Route("/dashboard/post")
 */
class PostController extends AbstractController
{
    /**
     * Page permettant de creer un article
     * http://localhost:8000/dashboard/post/create
     * @IsGranted("ROLE_AUTHOR")
     * @Route("/create", name="post_create", methods={"GET|POST"})
     */
    public function create(Request $request, SluggerInterface $slugger)
    {
        # Creation d'un nouvel article vierge
        $post = new Post();

        dump( $post);

        # Recuperation d'un User dans la BDD
        # TODO A remplacer par User connecte
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneByEmail('thu@email.com');

        # On affecte le journaliste
        $post->setUser($user);

        # Creation du Formulaire
        $form = $this->createFormBuilder( $post )

            # Titre de l'article
            ->add('title', TextType::class, [
                'label' => 'Titre de mon article',
                'attr' => [
                    'placeholder' => "Titre de l'article"
                ]
            ])

            //TODO Category
            ->add('category', EntityType::class, [
                'label' => 'Choisssez une catégorie',
                'class' => Category::class,
                'choice_label' => 'name'
            ])

            # TODO Content
            ->add('content', TextareaType::class, [
                'label' => false
            ])

            # TODO Image
            ->add('image', FileType::class, [
                'label' => 'Image'
            ])

            # TODO submit
            ->add('save', SubmitType::class, [
                'label' => 'enregistrer les modifications'

            ])


            # Finaliser notre Formulaire
        ->getForm();

        # Traitement du formulaire par symfony
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid()) {
//            dd($post);

            # TODO Upload de l'image
            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    # TODO Traitement en cas d'erreur de l'upload
                }

                // on sauvegarde dans la BDD le nom du nouveau fichier
                $post->setImage($newFilename);
            }


            # TODO Generation de l'alias
            $post->setAlias(
                $slugger->slug(
                    $post->getTitle()
                )
            );
            ## TODO Sauvegarde dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            # TODO Notification de confirmation
            $this->addFlash('success', 'Felicitation, votre article est en ligne');

            # TODO Redirection vers le nouvel article
            return $this->redirectToRoute('default_post',[
                'category' => $post->getCategory()->getAlias(),
                'alias' => $post->getAlias(),
                'id' => $post->getId()
            ]);
        }

        # Transmission du formulaire a la vue
        return $this->render('post/create.html.twig',[
            'form' => $form->createView()
        ]);

    }

}