<?php
// YOUR_APP/controllers/ListsController.php

/* change namespace in your app */
//namespace frontend\controllers;
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ArrayDataProvider;

class ListsController extends Controller
{
    public function actionIndex()
    {
        // GET params
        $params = Yii::$app->request->getQueryParams();
        //$title = Yii::$app->request->getQuery('title');
        if (!isset($_GET['title'])){
            $provider = new ArrayDataProvider([
                'allModels' => $this->getFakedModels(),
                'pagination' => [
                    'pageSize' => 5
                ],
                'sort' => [
                    'attributes' => ['id'],
                ],
            ]);

            return $this->render('index', [
                'title' => 'Listado',
                'return' => 'lists',
                'listDataProvider' => $provider
            ]);
        }
        else{
            return $this->render('index', [
                'title' => $_GET['title'],
                //'return' => $return,
                //'listDataProvider' => $listDataProvider
            ]);
        }
    }

    // function to generate faked models, don't care about this.
    private function getFakedModels()
    {
        $fakedModels = [];

        for ($i = 1; $i < 18; $i++) {
            $fakedItem = [
                //'list' => 'Listado',
                'id' => $i,
                'title' => 'Actividad ' . $i,
                'image' => 'http://placehold.it/300x200',
                'link'  => '<a href="http://placehold.it/300x200" target="_blank">Launch URL</a>'
            ];

            $fakedModels[] = $fakedItem;
        }

        return $fakedModels;
    }
}
?>
