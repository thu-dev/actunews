<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    /** Page d'accueil
     * http://localhost:8000     */
    public function index()
    {
        # Recuperation des articles de la BDD
        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findAll();

        return $this->render('default/index.html.twig',[
            'posts' => $posts
        ]);
    }

    /** Page d'category
     * http://localhost:8000/category
     * @Route("/{alias}", name="default_category", methods={"GET"})
     */
    public function category(Category $category)
    {
        return $this->render('default/category.html.twig', [
            'category' => $category
        ]);
        # return new Response("<h1>CATEGORY : $alias </h1>");
    }

    /** Page Article
     * http://localhost:8000/politique/un-alias-ici_1.html
     * @Route("/{category}/{alias}_{id}.html", name="default_post", methods={"GET"})
     */
    public function post(Post $post)
    {
        return $this->render('default/article.html.twig',[
            'post' => $post
        ]);
    }


    /** Page d'contact
     * http://localhost:8000/contact     */
    public function contact()
    {
        return new Response('<h1>CONTACT</h1>');
    }

    public function mentionsLegales()
    {
        return $this->render('default/mentions-legales.html.twig');
        # return new Response('<h1>MENTIONS LEGALES</h1>');
    }
}