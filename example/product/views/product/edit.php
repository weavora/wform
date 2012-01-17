<style type="text/css">
	fieldset {
		width: 300px;
	}
</style>
<div class="form">
<?php $form = $this->beginWidget('WForm', array('htmlOptions' => array('enctype'=>'multipart/form-data'))); ?>
	<fieldset>
		<legend>Product</legend>
		<div class="row">
			<?php echo $form->labelEx($model, 'name'); ?>
			<?php echo $form->textField($model, 'name'); ?>
			<?php echo $form->error($model, 'name'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model, 'price'); ?>
			<?php echo $form->textField($model, 'price'); ?>
			<?php echo $form->error($model, 'price'); ?>
		</div>
	</fieldset>

	<fieldset>
		<legend>Category</legend>
		<div class="row">
			<?php echo $form->labelEx($model, 'category_id'); ?>
			<?php echo $form->dropDownList($model, 'category_id',
				array_merge(CHtml::listData(Category::model()->findAll(), 'id', 'name'), array('' => 'new category'))
			, array('empty' => 'none')) ?>
			<?php echo $form->error($model, 'category_id'); ?>

			<div style="display:none;" id="other-category">
				<?php echo $form->labelEx($model, 'category.name'); ?>
				<?php echo $form->textField($model, 'category.name', array('disabled' => 'disabled')); ?>
				<?php echo $form->error($model, 'category.name'); ?>
			</div>

		</div>
	</fieldset>

	<fieldset>
		<legend>Tags</legend>
		<div class="row">
			<?php if ($tags): ?>
				<ul class="tag-list">
					<?php foreach ($tags as $index => $tag): ?>
						<li>
							<label>
								<?php echo $tag->name ?>
								<?php echo $form->checkBox($model, "tags.{$index}.id", array('value' => $tag->id, 'uncheckValue' => null)) ?>
							</label>
						</li>
					<?php endforeach ?>
				</ul>
			<?php endif; ?>

			<ul class="tags">
				<?php if ($model->tags): ?>
				<?php foreach ($model->tags as $index => $tag): ?>
					<?php if ($tag->isNewRecord): ?>
						<li>
							<?php echo $form->textField($model, "tags.$index.name") ?>
							<?php echo $form->error($model, "tags.$index.name") ?>
							<a class="delete" href="#">delete</a>
						</li>
					<?php endif; ?>
				<?php endforeach ?>
				<?php endif; ?>
				<li style="display: none;" class="template" id="product-tag">
					<?php echo $form->textField($model, 'tags..name') ?>
				</li>
			</ul>
			<a id="add-tag" href="#">add</a>

		</div>
	</fieldset>

	<fieldset>
		<legend>Images</legend>
		<ul class="images">
			<?php if ($model->images): ?>
				<?php foreach ($model->images as $index => $image): ?>
					<?php if (!empty($image->file_origin)):?>
						<?php echo $form->hiddenField($model, "images.{$index}.object_type") ?>
						<?php echo $form->hiddenField($model, "images.{$index}.id") ?>
						<?php echo $form->hiddenField($model, "images.{$index}.file") ?>
						<?php echo $form->hiddenField($model, "images.{$index}.file_origin") ?>
						<?php echo $form->hiddenField($model, "images.{$index}.tempFile") ?>
						<?php echo CHtml::link($image->file_origin, $image->fileUrl) ?>
						<a href="#" class="delete"">Delete</a>
					<?php endif; ?>
				<?php endforeach ?>
			<?php endif; ?>
			<li style="display: none;" class="template" id="product-image">
				<?php echo $form->hiddenField($model, 'images..object_type', array('value' => Attachment::OBJECT_TYPE_PRODUCT_IMAGE)) ?>
				<?php echo $form->fileField($model, 'images..file') ?>
			</li>
		</ul>
		<a id="add-image" href="#">add</a>

	</fieldset>

	<fieldset>
		<legend>Certificate</legend>
		<div class="row">
			<?php echo $form->labelEx($model, 'certificate.name'); ?>
			<?php echo $form->textField($model, 'certificate.name'); ?>
			<?php echo $form->error($model, 'certificate.name'); ?>
			<?php if (!empty($model->certificate->image->file_origin)): ?>
				<?php echo $form->hiddenField($model, "certificate.image.object_type") ?>
				<?php echo $form->hiddenField($model, "certificate.image.id") ?>
				<?php echo $form->hiddenField($model, "certificate.image.file") ?>
				<?php echo $form->hiddenField($model, "certificate.image.file_origin") ?>
				<?php echo $form->hiddenField($model, "certificate.image.tempFile") ?>
				<?php echo CHtml::link($model->certificate->image->file_origin, $model->certificate->image->fileUrl) ?>
				<a href="#" class="delete"">Delete</a>
			<?php else: ?>
				<?php echo $form->hiddenField($model, 'certificate.image.object_type', array('value' => Attachment::OBJECT_TYPE_CERTIFICATE)) ?>
				<?php echo $form->fileField($model, 'certificate.image.file') ?>
			<?php endif; ?>
		</div>
	</fieldset>

	<fieldset>
		<legend>Description</legend>
		<div class="row">
			<?php echo $form->labelEx($model, 'description.color'); ?>
			<?php echo $form->textField($model, 'description.color'); ?>
			<?php echo $form->error($model, 'description.color'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model, 'description.size'); ?>
			<?php echo $form->textField($model, 'description.size'); ?>
			<?php echo $form->error($model, 'description.size'); ?>
		</div>
	</fieldset>

	<div class="row submit">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">

	$(document).ready(function(){
		$('#ProductForm_category_id').change(function(){
			if (this.options.length == (this.selectedIndex + 1)) {
			    $('#other-category')
			    	.find('input')
			    		.removeAttr('disabled')
			    	.end()
			    .show();
			} else {
				$('#other-category')
					.find('input')
						.attr('disabled', 'disabled')
					.end()
				.hide();
			}
		});

		$('.tags').multiplyForms({
			addLink: '#add-tag'
		});

		$('.images').multiplyForms({
			addLink: '#add-image'
		});

	});

</script>
