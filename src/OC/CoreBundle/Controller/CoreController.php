<?php
namespace OC\CoreBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CoreController extends Controller
{
    public function indexAction()
    {
        return $this->render('OCCoreBundle:Core:index.html.twig');
    }

    public function contactAction(Request $request)
    {
        $session = $request->getSession();
        $session->getFlashBag()->add('info', 'La page de contact n’est pas encore disponible, merci de revenir plus tard.');

        return $this->redirectToRoute('oc_core_home');
    }

    public function translationAction($name)
    {
        $translator = $this->get('translator');
        $texteTraduit = $translator->trans('Mon message à inscrire dans les logs');

        return $this->render('OCCoreBundle:Core:translation.html.twig', ['message' => 'Hello', 'name' => $name, 'text' => $texteTraduit]);
    }
}