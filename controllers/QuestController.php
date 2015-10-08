<?php

namespace app\controllers;

use app\models\InviteForm;
use app\models\Question;
use app\models\search\QuestSearch;
use app\service\QuestService;
use Yii;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * QuestController implements the CRUD actions for Quest model.
 */
class QuestController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['start', 'run'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Quest models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new QuestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Quest model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = \Yii::$app->q->getQuest($id, false);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Quest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = \Yii::$app->q->getQuest();

        if ($model->load(Yii::$app->request->post()) && \Yii::$app->q->saveQuest($model)) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Quest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = \Yii::$app->q->getQuest($id, false);

        if ($model->load(Yii::$app->request->post()) && \Yii::$app->q->saveQuest($model)) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Deletes an existing Quest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        \Yii::$app->q->deleteQuest($id, false);

        return $this->redirect(['index']);
    }

    public function actionQuestions($quest_id) {
        $one_more_please = \Yii::$app->request->post('one_more_please');

        /** @var QuestService $q */
        $q = \Yii::$app->q;
        $quest = \Yii::$app->q->getQuest($quest_id);
        $questions = \Yii::$app->q->getQuestions($quest_id);

        if(\Yii::$app->request->post()) {
            $questions = $q->loadQuestions($quest, \Yii::$app->request->post('Question'));

            if($q->saveQuestions($quest, $questions) && $one_more_please !== 'yes') {
                return $this->redirect(['view', 'id' => $quest->id]);
            }
        }

        if($one_more_please === 'yes') {
            $questions [] = new Question();
        }

        return $this->render('questions', [
            'questions' => $questions,
            'quest' => $quest,
        ]);
    }

    public function actionInvite(){
        /** @var QuestService $q */
        $q = \Yii::$app->q;

        $model = new InviteForm();

        if($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $key = $q->invate($model->quest_id, $model->email);
            \Yii::$app->session->setFlash('alert', [
                'body' =>'Инвайт отправлен!',
                'options' => ['class' => 'alert alert-success']
            ]);
            return $this->refresh();
        }

        return $this->render('invite', [
            'model' => $model,
        ]);

    }

    public function actionStart($key){
        /** @var QuestService $q */
        $q = \Yii::$app->q;

        if($result = $q->startQuest($key)) {
            $this->redirect(['run', 'key'=>$key]);
        }

        throw new NotFoundHttpException();
    }

    public function actionRun($key) {
        /** @var QuestService $q */
        $q = \Yii::$app->q;

        $result = $q->getResultByKey($key);

        if(empty($result->start_at))
            return $this->redirect(['start', 'key'=>$key]);

        if($result->load(\Yii::$app->request->post()) && $q->saveResult($result)) {
            return $this->render('thanks', [
                'quest' => $result->quest,
                'model' => $result,
            ]);
        }

        return $this->render('run', [
            'quest' => $result->quest,
            'model' => $result,
        ]);
    }

}
