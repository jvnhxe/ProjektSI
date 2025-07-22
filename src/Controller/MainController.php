<?php
/*
 * This file is part of the YourProject package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MainController.
 */
#[\Symfony\Component\Routing\Attribute\Route('/')]
class MainController extends AbstractController
{
    /**
     * Index action.
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/', name: 'main_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }
}
