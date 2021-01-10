<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/blog")
*/
class salmyController extends AbstractController
{
	/**
	* @Route("/")
	*/
	public function home() 
	{
		$questions = [
			'Comment devenir Cryptologue ?',
			'Comment devenir Développeur ?',
			'Comment devenir Data Analyst ?',
			'Comment devenir Magicien ?',
			'Comment devenir Musicien ?'
		];
		return $this->render('home.html.twig', [
			'questions' => $questions
		]);
	}

	/**
	* @Route("/show/{slug}")
	*/
	public function show($slug, Request $request) 
	{
		if($request->isMethod('POST')) 
		{
			$data = $request->request->all();
			$slug = $data['yourQuestion'];

			if($slug == '')
			{
				return $this->render('home.html.twig');
			}
		}

		return $this->render('show.html.twig', [
			'question' => $slug
		]);
	}
}
?>