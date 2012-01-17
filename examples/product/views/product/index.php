<?php if ($models): ?>
<table>
	<tr>
		<th>id</th>
		<th>name</th>
		<th>categpory</th>
		<th>description</th>
		<th>images count</th>
		<th>certificate</th>
	</tr>
	<?php foreach ($models as $model): ?>
	<tr>
		<td><?php echo $model->id ?></td>
		<td><?php echo $model->name ?></td>
		<td><?php echo $model->category ? $model->category->name : '' ?></td>
		<td><?php echo $model->description ? $model->description->color . '/' . $model->description->size : '' ?></td>
		<td><?php echo $model->images ? count($model->images) : '' ?></td>
		<td><?php echo $model->certificate ? $model->certificate->name : '' ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php endif; ?>
