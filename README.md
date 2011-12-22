Yii Composite Models class
==========================

Setup
-----
As a first step we need model inherited from our ActiveRecord class

		<?php
		class MyModel extends ActiveRecord {
			...
			public function relations()
			{
				return array(
					'client' => array(self::HAS_ONE, 'Client', 'my_model_id'),
					'product' => array(self::HAS_ONE, 'ModelB', 'my_model_id'),
					'notes' => array(self::HAS_MANY, 'Note', 'my_model_id'),
				);
			}
			...
    	}

The complex form should be inherited from MyModel class

		<?php
		class MyClassForm extends MyModel {

			public static function model($className=__CLASS__)
			{
				return parent::model($className);
			}

			return array(
				'WFormBehavior' => array(
					'class' => 'ext.wform.WFormBehavior',
					'relations' => array('client', 'notes'),
				),
			);
		}

Representation:

		<?php $form = $this->beginWidget('CActiveForm'); ?>
		<!-- major form fields -->
		<div class="row">
			<?php echo $form->labelEx($model, 'name'); ?>
			<?php echo $form->textField($model, 'name'); ?>
			<?php echo $form->error($model, 'name'); ?>
		</div>

		<!-- fields of embeded forms -->

		<div class="row">
			<!-- has one relation -->
			<?php echo $form->labelEx($model->client, 'name'); ?>
			<?php echo $form->textField($model->client, 'name', array('name' => 'MyClassForm[client][name]')); ?>
			<?php echo $form->error($model->client, 'name'); ?>
		</div>
		<div class="row">
			<!-- has many relation -->
			<?php if ($myModel->notes): ?>
				<?php foreach ($myModel->notes as $index => $note): ?>
					<?php if ($note->isNewRecord): ?>
						<?php echo $form->hiddenField($note, "[$index]id", array('name' => "MyClassForm[notes][$index][id]")); ?>
						<?php echo $form->error($note, "[$index]id"); ?>
					<?php endif; ?>
					<?php echo $form->labelEx($note, "[$index]text"); ?>
					<?php echo $form->textField($note, "[$index]text", array('name' => "MyClassForm[notes][$index][text]")); ?>
					<?php echo $form->error($note, "[$index]text"); ?>
				<?php endforeach ?>
			<?php endif; ?>
		</div>

		<?php $this->endWidget(); ?>

Controller source:

	    public function actionEdit($id = null)
    	{
    		$myModel = $id ? MyClassForm::model()->findByPk($id) : new MyClassForm();
    		if (Yii::app()->request->getPost('MyClassForm')) {
    		    $myModel->attributes = Yii::app()->request->getPost('MyClassForm');
    		    if ($myModel->save()) {
    		        $this->redirect('some/page');
    		    }
    		}
    		$this->render('edit', array('model' => $myModel));
    	}
