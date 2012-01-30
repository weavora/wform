<h1>Products</h1>
<p>
	<a href="<?php echo $this->createUrl('product/add');?>">Create Product</a>
</p>
<?php if ($products): ?>
<table>
	<tr>
		<th>id</th>
		<th>name</th>
		<th>categpory</th>
		<th>description</th>
		<th>images count</th>
		<th>certificate</th>
		<th>edit</th>
		<th>delete</th>
	</tr>
	<?php foreach ($products as $product): ?>
	<tr>
		<td><?php echo $product->id ?></td>
		<td><?php echo $product->name ?></td>
		<td><?php echo $product->category ? $product->category->name : '' ?></td>
		<td><?php echo $product->description ? $product->description->color . '/' . $product->description->size : '' ?></td>
		<td><?php echo $product->images ? count($product->images) : '' ?></td>
		<td><?php echo $product->certificate ? $product->certificate->name : '' ?></td>
		<td><a href="<?php echo $this->createUrl('product/edit', array('id' => $product->id));?>">Edit</a></td>
		<td><a href="<?php echo $this->createUrl('product/delete', array('id' => $product->id));?>">Delete</a></td>
	</tr>
	<?php endforeach ?>
</table>
<?php endif; ?>
