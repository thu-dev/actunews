<?php


namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/** Page d'user
 * http://localhost:8000/user
 */

class UserController extends AbstractController
{

    /**
     * @Route("user/register", name="user_register", methods={"GET|POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {

        # Creation d'un user
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        # Creation du formulaire
        $form = $this->createFormBuilder($user)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('submit',SubmitType::class)
            ->getForm()
            ;

        # Traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            # TODO Encodage du mot de passe
            $user->setPassword(
                $encoder->encodePassword(
                    $user,
                    $user->getPassword()
                )
            );

            # TODO Sauvegarde dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            # TODO Notification Flash
            $this->addFlash('sucess', 'merci pour votre inscription. Vous pouvez vous connecter.');
            # TODO Redirection
            return $this->redirectToRoute('index');

        }

        # Affichage du formulaire
        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);

    }
}