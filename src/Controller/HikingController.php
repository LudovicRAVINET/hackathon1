<?php

namespace App\Controller;

use App\Model\HikingManager;

class HikingController extends AbstractController
{
    public function show()
    {
        $filter = 2;
        $hikingManager = new HikingManager();
        $lands = $hikingManager->selectAll();
        return $this->twig->render('Hiking/show.html.twig', ['lands' => $lands, 'filter' => $filter]);
    }

    // display description and image about a clicked balise land
    public function landDisplay(int $coordX, int $coordY)
    {
        $filter = 2;
        $filter = $_SESSION['filter'];
        $hikingManager = new HikingManager();
        if ($filter == 2) {
            $lands = $hikingManager->selectAll();
        } elseif ($filter == 1) {
            $lands = $hikingManager->selectAllFilter(1);
        } else {
            $lands = $hikingManager->selectAllFilter(0);
        }

        $coordX = (int)floor($coordX / 3);
        $coordY = (int)floor($coordY / 3);

        $idPop = [];
        foreach ($lands as $land) {
            $xMin = $land['coordinate_X'] - 20;
            $xMax = $land['coordinate_X'] + 20;
            $yMin = $land['coordinate_Y'] - 20;
            $yMax = $land['coordinate_Y'] + 20;
            if ($coordX > $xMin && $coordX < $xMax && $coordY > $yMin && $coordY < $yMax) {
                $idPop['id'] = $land['id'];
                $idPop['name'] = $land['name'];
                $idPop['description'] = $land['description'];
                $idPop['url'] = $land['url'];
            }
        }

        $choosenBalises = $_SESSION['baliseId'];
        $coordBalises = $_SESSION['coordBalises'];

        return $this->twig->render('Hiking/show.html.twig', ['idPop' => $idPop,
            'lands' => $lands,
            'baliseId' => $choosenBalises,
            'coordBalises' => $coordBalises,
            'filter' => $filter
        ]);
    }

    // display selected interest point balises
    public function baliseDisplay()
    {
        $choosenBalises = [];
        $coordBalises = '';
        $filter = 2;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $balisesId = array_map('trim', $_POST);
            $filter = $balisesId['filter'];
            $balisesId = array_slice($balisesId, 1);

            $hikingManager = new HikingManager();
            if ($filter == 2) {
                $balises = $hikingManager->selectAll();
            } elseif ($filter == 1) {
                $balises = $hikingManager->selectAllFilter(1);
            } else {
                $balises = $hikingManager->selectAllFilter(0);
            }

            foreach ($balises as $bal) {
                if (in_array($bal['id'], $balisesId)) {
                    $choosenBalises[] = (int)$bal['id'];
                    $coordBalises .= (int)$bal['coordinate_X'] . '-' .
                        (int)$bal['coordinate_Y'] . '-' . (int)$bal['id'] . ' ';
                }
            }
            $coordBalises = substr($coordBalises, 0, -1);
        }

        $_SESSION['baliseId'] = $choosenBalises;
        $_SESSION['coordBalises'] = $coordBalises;
        $_SESSION['filter'] = $filter;

        $hikingManager = new HikingManager();
        if ($filter == 2) {
            $lands = $hikingManager->selectAll();
        } elseif ($filter == 1) {
            $lands = $hikingManager->selectAllFilter(1);
        } else {
            $lands = $hikingManager->selectAllFilter(0);
        }

        return $this->twig->render('Hiking/show.html.twig', ['lands' => $lands,
            'baliseId' => $choosenBalises,
            'coordBalises' => $coordBalises,
            'filter' => $filter
        ]);
    }
}
