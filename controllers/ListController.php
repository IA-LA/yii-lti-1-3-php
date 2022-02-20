<?php
// YOUR_APP/controllers/ListController.php

/* change namespace in your app */
//namespace frontend\controllers;
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ArrayDataProvider;

class ListController extends Controller
{
    public function actionIndex()
    {
        $provider = new ArrayDataProvider([
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
                'id' => $i,
                'title' => 'Title ' . $i,
                'image' => 'https://place-hold.it/1x1/',
                'link'  => '<a href="https://place-hold.it/1x1/" target="_blank">URL</a>'
            ];

            $fakedModels[] = $fakedItem;
        }

        return $fakedModels;
    }
}
?>
