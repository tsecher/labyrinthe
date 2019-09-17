<?php

    namespace App\Controller;

    use http\Client\Request;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;
    use App\Object\Grid;
    use App\Object\Cell;
    use App\Object\Path;

    class GridController extends AbstractController {
        /**
         * @Route("/grid", name="grid")
         */
        public function index() {
            $grid = new Grid(20, 20);
            $grid->generate();

//        $start = $grid->getCell([1,1])->setStatus(Cell::STATUS_START);
//        $end = $grid->getCell([18,18])->setStatus(Cell::STATUS_END);
//
//        $grid->randomObstacle(100);

//        $path = new Path($grid);
//        $path->goto($start, $end);
//        if( $data = $path->getSuccessPath() ){
//            /** @var Cell $cell */
//            foreach( $data as $cell ){
//              if( !in_array($cell->getStatus(), [Cell::STATUS_START, Cell::STATUS_START]) ){
//                $cell->setStatus(Cell::STATUS_OK);
//              }
//
//            }
//        }

            return $this->render('grid/index.html.twig', [
                'controller_name' => 'GridController',
                'grid'            => $grid
            ]);
        }

        /**
         * @Route("/grid/findPath"), name="grid Find Path",
         *     condition="request.isXmlHttpRequest()")
         */
        public function findPath(\Symfony\Component\HttpFoundation\Request $request) {
            $data = json_decode($request->query->get('data'));

            $grid = new Grid(20, 20);
            $grid->generate();

            // Start  || ends
            $start = $grid->getCellById(reset($data->start))
                ->setStatus(Cell::STATUS_START);
            $end = $grid->getCellById(reset($data->end))
                ->setStatus(Cell::STATUS_END);

            foreach ($data->obstacles as $oId) {
                $grid->getCellById($oId)->setStatus(Cell::STATUS_OBSTACLE);
            }

            $path = new Path($grid);
            $path->goto($start, $end);
            $result = $path->getSuccessPath() ?: [];
            return new JsonResponse(
                array_map(
                    function (Cell $cell) {
                        return $cell->getId();
                    },
                    $result

            ));
        }
    }
