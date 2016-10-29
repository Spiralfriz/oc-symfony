<?php
namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if($page < 1)
        {
            throw new NotFoundHttpException('La page : '.$page.' est inexistante');
        }

        $em = $this->getDoctrine()->getManager();

        $nbPerPage = $this->getParameter('nb_per_page');

        $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->getAdverts($page, $nbPerPage);

        $nbPages = ceil(count($listAdverts) / $nbPerPage);

        if($page > $nbPages)
        {
            throw new NotFoundHttpException('La page : '.$page.' est inexistante');
        }

        return $this->render('OCPlatformBundle:Advert:index.html.twig', [
            'listAdverts' => $listAdverts,
            'nbPages'  => $nbPages,
            'page'  => $page
        ]);
    }

    public function viewAction(Advert $advert, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /*$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
        if($advert === null)
        {
            throw new NotFoundHttpException("L'annonce n° ".$id." est inexistante");
        }*/

        $listApplications = $em->getRepository('OCPlatformBundle:Application')
                               ->findBy(['advert' => $advert]);

        $listSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')
                         ->findBy(['advert' => $advert]);

        return $this->render('OCPlatformBundle:Advert:view.html.twig', [
            'advert' => $advert,
            'listApplications' => $listApplications,
            'listSkills' => $listSkills
        ]);
    }

    public function addAction(Request $request)
    {
        $advert = new Advert;

        $form = $this->createForm(AdvertType::class, $advert);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'L\'annonce à bien été enregistrée');
            return $this->redirectToRoute('oc_platform_view', ['id' => $advert->getId()]);
        }

        return $this->render('OCPlatformBundle:Advert:add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if($advert === null)
        {
            throw new NotFoundHttpException('L\'annonce n° '.$id.' est inexistante');
        }

        $form = $this->createForm(AdvertEditType::class, $advert);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée');
            return $this->redirectToRoute('oc_platform_view', ['id' => $advert->getId()]);
        }

        return $this->render('OCPlatformBundle:Advert:edit.html.twig', [
            'advert' => $advert,
            'form' => $form->createView()
        ]);
    }

    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if($advert === null)
        {
            throw new NotFoundHttpException('L\'annonce n° '.$id.' est inexistante');
        }

        $form = $this->get('form.factory')->create();

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em->remove($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien supprimée');
            return $this->redirectToRoute('oc_platform_home');
        }

        return $this->render('OCPlatformBundle:Advert:delete.html.twig', [
            'advert' => $advert,
            'form' => $form->createView()
        ]);
    }

    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findBy([], ['date' => 'desc'], $limit, 0);

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
            'listAdverts' => $listAdverts
        ));
    }

    public function validatorAction()
    {
        $advert = new Advert();
        $advert->setDate(new \DateTime());
        $advert->setTitle('abc');
        $advert->setAuthor('A');

        $validator = $this->get('validator');

        $listErrors = $validator->validate($advert);

        if(count($listErrors) > 0)
        {
            return new Response('Annonce invalide :'.(string) $listErrors);
        } else {
            return new Response('L\'annonce est valide');
        }
    }

    /**
     * @ParamConverter("json")
     */
    public function paramConverterAction($json)
    {
        return new Response(print_r($json, true));
    }
}