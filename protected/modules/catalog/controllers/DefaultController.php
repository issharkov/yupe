<?php

class DefaultController extends YBackController
{

    /**
     * Отображает товар по указанному идентификатору
     * @param integer $id Идинтификатор товар для отображения
     */
    public function actionView($id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Создает новую модель товара.
     * Если создание прошло успешно - перенаправляет на просмотр.
     */
    public function actionCreate()
    {
        $model = new Good;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Good']))
        {
            $model->attributes = $_POST['Good'];

           if ($model->save())
            {
                $model->image = CUploadedFile::getInstance($model, 'image');               
                
                if ($model->image)
                {
                    $imageName = $this->module->getUploadPath() . $model->alias . '.' . $model->image->extensionName;

                    if ($model->image->saveAs($imageName))
                    {
                        $model->image = basename($imageName);

                        $model->update(array( 'image' ));
                    }
                }

                Yii::app()->user->setFlash(YFlashMessages::NOTICE_MESSAGE, Yii::t('yupe', 'Запись добавлена!'));

                $this->redirect(array( 'view', 'id' => $model->id ));
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Редактирование товара.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        
         $image = $model->image;

        if (isset($_POST['Good']))
        {
            $model->attributes = $_POST['Good'];

             if ($model->save())
            {
                $model->image = CUploadedFile::getInstance($model, 'image');

                if ($model->image)
                {
                    $imageName = $this->module->getUploadPath() . $model->alias . '.' . $model->image->extensionName;

                    @unlink($this->module->getUploadPath() . $image);

                    if ($model->image->saveAs($imageName))
                    {
                        $model->image = basename($imageName);

                        $model->update(array( 'image' ));
                    }
                }
                else
                {
                    $model->image = $image;

                    $model->update(array( 'image' ));
                }

                Yii::app()->user->setFlash(YFlashMessages::NOTICE_MESSAGE, Yii::t('yupe', 'Запись обновлена!'));

                $this->redirect(array( 'update', 'id' => $model->id ));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Удаяет модель товара из базы.
     * Если удаление прошло успешно - возвращется в index
     * @param integer $id идентификатор товара, который нужно удалить
     */
    public function actionDelete($id)
    {
        if (Yii::app()->request->isPostRequest)
        {
            // поддерживаем удаление только из POST-запроса
            $model = $this->loadModel($id);

            if ($model->delete())
                @unlink($this->module->getUploadPath() . $model->image);

            Yii::app()->user->setFlash(YFlashMessages::NOTICE_MESSAGE, Yii::t('yupe', 'Запись удалена!'));

            // если это AJAX запрос ( кликнули удаление в админском grid view), мы не должны никуда редиректить
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array( 'штвуч' ));
        }
        else
            throw new CHttpException(400, 'Неверный запрос. Пожалуйста, больше не повторяйте такие запросы');
    }

    /**
     * Управление товарами.
     */
    public function actionIndex()
    {
        $model = new Good('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Good']))
            $model->attributes = $_GET['Good'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    /**
     * Возвращает модель по указанному идентификатору
     * Если модель не будет найдена - возникнет HTTP-исключение.
     * @param integer идентификатор нужной модели
     */
    public function loadModel($id)
    {
        $model = Good::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'Запрошенная страница не найдена.');
        return $model;
    }

    /**
     * Производит AJAX-валидацию
     * @param CModel модель, которую необходимо валидировать
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'good-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}