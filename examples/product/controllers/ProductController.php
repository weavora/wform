<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class ProductController extends Controller
{
	public function actionIndex()
	{
		$products = ProductForm::model()->findAll();
		$this->render('index', array(
			'products' => $products
		));
	}

	public function actionAdd()
	{
		$this->forward('edit');
	}

	public function actionEdit($id = null)
	{
		$productForm = $id ? ProductForm::model()->with('images','tags')->findByPk($id) : new ProductForm();

		if (Yii::app()->request->getPost('ProductForm')) {
			$productForm->attributes = Yii::app()->request->getPost('ProductForm');
			if ($productForm->save()) {
				$this->redirect($this->createUrl('product/index'));
			}
		}
		$this->render('edit', array(
			'product' => $productForm,
			'categories' => Category::model()->findAll(),
			'tags' => Tag::model()->findAll()
		));
	}

	public function actionDelete($id)
	{
		$productForm = ProductForm::model()->findByPk($id);
		if (!empty($productForm)) {
			$productForm->delete();
		}
		$this->redirect($this->createUrl('product/index'));
	}
}
