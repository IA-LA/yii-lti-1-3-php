<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

/*UPLOAD*/
use app\models\UploadForm;
use yii\web\UploadedFile;

/*REGISTER*/
use app\models\RegisterForm;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

/*QUERY*/
use app\models\QueryForm;

class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /*UPLOAD*/
    /**
     * Displays upload page.
     *
     * @return string
     */
    public function actionUpload()
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->upload()) {
                // file is uploaded successfully
		        return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success">Archivo subido correctamente</p></div>');
		        //return $this->render('upload', ['model' => $model]);
		        //return;
            }
        }

        return $this->render('upload', ['model' => $model]);
    }

    /*REGISTER*/
    /**
     * Displays register page.
     *
     * @return Response|string
     */
    public function actionRegister()
    {
        $model = new RegisterForm();

        if ($model->load($request = Yii::$app->request->post()) && $model->register(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('registerFormSubmitted');

            // POST Register (https://stackoverflow.com/questions/19905118/how-to-call-rest-api-from-view-in-yii)
            $client = new Client();

            $response = $client->createRequest()
                //->setMethod('POST')
                ->setMethod('GET')
                ->setUrl('http://192.168.0.31:49151/servicios/lti/lti13/read/5fc3860a81740b0ef098a983')
                ->setData(['name' => 'John Doe', 'email' => 'johndoe@domain.com'])
                ->setOptions([
                    //'proxy' => 'tcp://proxy.example.com:5100', // use a Proxy
                    'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
                ])
                ->send();
            //foreach ($request as $key => $value){
            //    echo "{$key} => {$value} ";
            //}
            // Array ( [_csrf] => _jj1OVZYhyxeDkVwF82Lt-ANf6mPzL_xKv5nCNCp7H-4daVqPx3eXm1LGjpNq8Tut3RMnMCex7dQlStFhZC9LQ== [RegisterForm] => Array ( [id] => 012345678901234567890123 [url] => http://127.0.0.1:8000/index.php?r=site%2Fregister [subject] => a [body] => aa [verifyCode] => zuvagi ) [register-button] => )
            //return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . ArrayHelper::getValue($request, 'RegisterForm') . print_r($request) . print_r($response) . '</p></div>');
            return $this->renderContent('<div><p/><p/><p/><p class="alert alert-success"> Registro finalizado: ' . ArrayHelper::isAssociative($request) . '<br/>REQUEST:<br/>' . print_r($request) . '<br/>RESPONSE:<br/> . print_r($response) . </p></div>');
            //return $this->refresh();
        }
        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /*QUERY*/
    /**
     * Displays query page.
     *
     * @return Response|string
     */
    public function actionQuery()
    {
        $model = new QueryForm();

        if ($model->load(Yii::$app->request->post()) && $model->query(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('registerFormSubmitted');

            return $this->refresh();
        }
        return $this->render('query', [
            'model' => $model,
        ]);
    }
}
