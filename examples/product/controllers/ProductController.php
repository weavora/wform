<?php

class ProductController extends Controller
{

	public function actionAdd() {
		$this->forward('edit');
	}

	public function actionIndex() {
		$models = ProductForm::model()->findAll();
		$this->render('index', array('models' => $models));
	}

	public function actionEdit($id = null)
	{
		$model = $id ? ProductForm::model()->findByPk($id) : new ProductForm();

		if (Yii::app()->request->getPost('ProductForm')) {
		    $model->attributes = Yii::app()->request->getPost('ProductForm');
		    if ($model->save()) {
		        $this->redirect('/product/index');
		    }
		}
		$this->render('edit', array(
			'model' => $model,
			'categories' => Category::model()->findAll(),
			'tags' => Tag::model()->findAll()
		));
	}

	public function actionView($id) {
		$model = Product::model()->findByPk($id);
		$this->render('view', array('model' => $model));
	}
}
