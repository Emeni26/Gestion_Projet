<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{
    /**
     * @Route("/new", name="new_project")
     * @Method({"GET","POST"})
     */
    public function newAction(Request $request)
    {   
        $link = $this->container->getParameter('url');
        $bb_user = $this->container->getParameter('bb_user');
        $bb_pass = $this->container->getParameter('bb_pass');
        if ($request->getMethod() == 'POST') {
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $is_private = $request->request->get('gender');
            $url = $link . $name;
            $fields = array(
                    'is_private' => $is_private,
                    'description' => $description
            );
            $fields_string='';
            foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
            rtrim($fields_string, '&');
            $ch = curl_init();
            try{
                curl_setopt($ch, CURLOPT_USERPWD, $bb_user . ":" . $bb_pass); 
                curl_setopt($ch,CURLOPT_URL, $url);
                curl_setopt($ch,CURLOPT_POST, count($fields));
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                $result = curl_exec($ch);
            } catch(Exception $e){
                echo $e->fields->message;die;
            }
            return $this->redirect($this->generateUrl('repository', array('slug' => $name)));
            //close connection
            curl_close($ch);
        }
        
        //var_dump($result);die;
         return $this->render('Projects/new.html.twig'
        );
    }
    /**
     * @Route("/repositories", name="repositories")
     */
    public function repositoriesAction()
    {  
        $ch = curl_init();
        $link = $this->container->getParameter('url');
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $repositories = json_decode($response);
        
        return $this->render('Projects/repositories.html.twig', array(
            "repositories" => $repositories
        ));
        curl_close($ch);
    }
    /**
     * @Route("/repository/{slug}", name="repository")
     * Method("GET")
     */
    public function repositoryAction($slug)
    {   
        $link = $this->container->getParameter('url');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $link .$slug);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $repository = json_decode($response);
        
        return $this->render('Projects/repository.html.twig', array(
            "repository" => $repository,
            "watchers" => $this->getData($slug, 'watchers'),
            "commits" => (array) $this->getData($slug, 'commits'),
            "branches" => $this->getData($slug, 'refs/branches/'),
            "tags" => $this->getData($slug, 'refs/tags'),
        ));
        curl_close($ch);
    }
    public function getData($slug, $links)
    {
        $link = $this->container->getParameter('url');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $link .$slug.'/'.$links);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $data = json_decode($response);
        
        return $data;
    }
    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction()
    {  
        
        return $this->render('Projects/contact.html.twig');
    }
}
