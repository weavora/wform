[Example source code](https://github.com/weavora/wform/tree/master/examples/product)

# Setup

1. Copy sample source to your application
2. Execute db dump from products.sql
3. Update imports in main.php to the following:

```php
'import'=>array(
	'application.models.*',
	'application.models.forms.*',
	'application.components.*',
	'ext.wform.*',
),
```
4. Now you have access to sample controller: 
 
- Product List: http://yourhost.local/index.php?r=/product
- Create Product: http://yourhost.local/index.php?r=/product/add

# Form Layout

Here is what you should see:

![Form Layout](http://i.imgur.com/F3wRZ.png)

# DB Diagram

![DB Structure](http://i.imgur.com/A8c7W.png)

# ProductController

```php
<?php
class ProductController extends Controller
{
	public function actionAdd() 
	{
		$this->forward('edit');
	}

	public function actionEdit($id = null)
	{
		$productForm = $id ? ProductForm::model()->findByPk($id) : new ProductForm();

		if (Yii::app()->request->getPost('ProductForm')) {
		    $productForm ->attributes = Yii::app()->request->getPost('ProductForm');
		    if ($productForm ->save()) {
		        $this->redirect('/product/index');
		    }
		}
		$this->render('edit', array(
			'product' => $productForm ,
			'categories' => Category::model()->findAll(),
			'tags' => Tag::model()->findAll()
		));
	}
	...
}
```

# ProductForm

```php
<?php
class ProductForm extends Product {

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function relations()
	{
		return array_merge(parent::relations(), array(
			'images' => array(self::HAS_MANY, 'AttachmentForm', 'object_id', 'condition' => 'images.object_type=:object_type', 'params' => array('object_type' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE)),
			'certificate' => array(self::HAS_ONE, 'CertificateForm', 'product_id'),
		));
	}


	public function behaviors() 
	{
		return array_merge(
			parent::behaviors(),
			array(
				'wform' => array(
					'class' => 'ext.wform.WFormBehavior',
					'relations' => array(
						'category' => array('unsetInvalid' => true, 'required' => false),
						'tags' => array('required' => false),
						'images',
						'certificate',
						'description',
					),
				),
			)
		);
	}
}
```

# AttachmentForm

```php

<?php

class AttachmentForm extends Attachment
{

	public $tempFile = null;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function rules()
	{
		return array_merge(parent::rules(), array(
			array('tempFile', 'safe'),
		));
	}

	public static function create($type)
	{
		$attachmentForm = new AttachmentForm();
		$attachmentForm->object_type = $type;
		return $attachmentForm;
	}

	public function beforeValidate()
	{
		if ($this->file instanceof CUploadedFile) {
			// save to tmp folder
			$tempFile = new WTempFile(Yii::app()->runtimePath);

			if ($this->file->saveAs($tempFile->getPath())) {
				$this->tempFile = $tempFile->getFile();

				// setup proper file_origin
				$this->file_origin = $this->file->getName();
			}
		}
		return true;
	}

	public function saveUploadedFile()
	{
		if (empty($this->file_origin)) {
			if (!$this->isNewRecord)
				$this->delete();
			return false;
		}

		if (empty($this->tempFile)) {
			return false;
		}

		$tempFile = new WTempFile(Yii::app()->runtimePath);
		$tempFile->setFile($this->tempFile);

		if (!$tempFile->isValid()) {
			return false;
		}

		$attachmentDirectory = Yii::app()->runtimePath . '/' . $this->object_type . '/';

		if (!is_dir($attachmentDirectory)) {
			mkdir($attachmentDirectory);
		}

		$fileName = $this->id . '.' . pathinfo($this->file_origin, PATHINFO_EXTENSION);


		if ($tempFile->saveAs($attachmentDirectory . $fileName)) {
			$this->file = $fileName;
			$this->isNewRecord = false;
			$this->tempFile = null;
			$this->save(false);
		}

		$this->tempFile = null;

		return false;
	}

	public function afterSave()
	{
		$this->saveUploadedFile();
		return parent::afterSave();
	}
}
```