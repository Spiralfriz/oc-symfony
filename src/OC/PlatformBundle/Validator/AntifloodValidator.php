<?php
namespace OC\PlatformBundle\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AntifloodValidator extends ConstraintValidator
{
    private $requestStack,
            $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        // Pour récupérer l'objet Request tel qu'on le connait, il faut utiliser getCurrentRequest du service request_stack
        $request = $this->requestStack->getCurrentRequest();

        // On récupère l'IP de celui qui poste
        $ip = $request->getClientIp();

        // On vérifie si cette IP a déjà posté une candidature il y a moins de 15 secondes
        $isFlood = null;
        /*$isFlood = $this->em
            ->getRepository('OCPlatformBundle:Application')
            ->isFlood($ip, 15) // Bien entendu, il faudrait écrire cette méthode isFlood, c'est pour l'exemple
        ;*/

        if (strlen($value) < 3 || $isFlood) {
            $this->context->addViolation($constraint->message); // C'est cette ligne qui déclenche l'erreur pour le formulaire, avec en argument le message
        }
    }
}