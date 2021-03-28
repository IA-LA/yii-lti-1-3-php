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
        $provider = new ArrayDataProvider([
            'title' => 'Listado',
            'return' => 'lists',
            'allModels' => $this->getFakedModels(),
            'pagination' => [
                'pageSize' => 5
            ],
            'sort' => [
                'attributes' => ['id'],
            ],
        ]);

        return $this->render('index', ['listDataProvider' => $provider]);
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
