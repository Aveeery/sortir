<?php


namespace App\Security;


use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AccessDeniedHandler implements \Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $content = "
<!DOCTYPE html>
<html lang='en'>
<style>
a:visited {
color: black;
}
button {
 text-decoration: none;
  border: 2px solid #F5EEEB;
  padding: 8px 15px;
  border-radius: 10px;
  background-color: #FFF7BF;
  color: #709FD3;
  font-weight: bold; 
}
h1 {
    color: #FB999A;
}
html {
background-color: #A6DAF5;
}
</style>
<div style='width: 80%; margin: auto; padding: 10px; text-align: center'>
<img src='/sortir/public/icons/unicorn.png' alt='surprised unicorn' style='margin-left: 3em'>
<h1>Oops, access denied ... </h1>
<a href='/sortir/public/' style='text-decoration: none'><button>Retour à l'accueil</button></a>
</div>
</html>";
        return new Response($content, 403);
    }
}