<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WForm extends CActiveForm
{
	/**
	 * Renders an HTML label for a model attribute.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute or path to related model attribute
	 * @param array $htmlOptions additional HTML attributes.
	 * @return string the generated label tag
	 */
	public function label($parentModel, $attributedPath, $htmlOptions = array())
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		$htmlOptions['for'] = CHtml::getIdByName($htmlOptions['name']);
		if (!isset($htmlOptions['label']) && ($label = self::resolveLabel($parentModel, $attributedPath)) !== null)
			$htmlOptions['label'] = $label;
		return parent::label($model, $attribute, $htmlOptions);
	}

	/**
	 * Renders an HTML label for a model attribute.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute or path to related model attribute
	 * @param array $htmlOptions additional HTML attributes.
	 * @return string the generated label tag
	 */
	public function labelEx($parentModel, $attributedPath, $htmlOptions = array())
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		$htmlOptions['for'] = CHtml::getIdByName($htmlOptions['name']);
		if (!isset($htmlOptions['label']) && ($label = self::resolveLabel($parentModel, $attributedPath)) !== null)
			$htmlOptions['label'] = $label;
		return parent::labelEx($model, $attribute, $htmlOptions);
	}

	/**
	 * Renders a text field for a model attribute.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute or path to related model attribute
	 * @param array $htmlOptions additional HTML attributes.
	 * @return string the generated input field
	 */
	public function textField($parentModel, $attributedPath, $htmlOptions = array())
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		return parent::textField($model, $attribute, $htmlOptions);
	}

	/**
	 * Renders a hidden field for a model attribute.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute or path to related model attribute
	 * @param array $htmlOptions additional HTML attributes.
	 * @return string the generated input field
	 */
	public function hiddenField($parentModel, $attributedPath, $htmlOptions = array())
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		return parent::hiddenField($model, $attribute, $htmlOptions);
	}

	/**
	 * Renders a password field for a model attribute.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute or path to related model attribute
	 * @param array $htmlOptions additional HTML attributes.
	 * @return string the generated input field
	 */
	public function passwordField($parentModel, $attributedPath, $htmlOptions = array())
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		return parent::passwordField($model, $attribute, $htmlOptions);
	}

	/**
	 * Renders a text area for a model attribute.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute or path to related model attribute
	 * @param array $htmlOptions additional HTML attributes.
	 * @return string the generated text area
	 */
	public function textArea($parentModel, $attributedPath, $htmlOptions = array())
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		return parent::textArea($model, $attribute, $htmlOptions);
	}

	/**
	 * Renders a file field for a model attribute.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute or path to related model attribute
	 * @param array $htmlOptions additional HTML attributes
	 * @return string the generated input field
	 */
	public function fileField($parentModel, $attributedPath, $htmlOptions = array())
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		return parent::fileField($model, $attribute, $htmlOptions);
	}

	/**
	 * Renders a radio button for a model attribute.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute or path to related model attribute
	 * @param array $htmlOptions additional HTML attributes.
	 * @return string the generated radio button
	 */
	public function radioButton($parentModel, $attributedPath, $htmlOptions = array())
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		return parent::radioButton($model, $attribute, $htmlOptions);
	}

	/**
	 * Renders a checkbox for a model attribute.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute or path to related model attribute
	 * @param array $htmlOptions additional HTML attributes.
	 * @return string the generated check box
	 */
	public function checkBox($parentModel, $attributedPath, $htmlOptions = array())
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		return parent::checkBox($model, $attribute, $htmlOptions);
	}

	/**
	 * Renders a dropdown list for a model attribute.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute or path to related model attribute
	 * @param array $data data for generating the list options (value=>display)
	 * @param array $htmlOptions additional HTML attributes.
	 * @return string the generated drop down list
	 */
	public function dropDownList($parentModel, $attributedPath, $data, $htmlOptions = array())
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		return parent::dropDownList($model, $attribute, $data, $htmlOptions);
	}

	/**
	 * Renders a list box for a model attribute.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute or path to related model attribute
	 * @param array $data data for generating the list options (value=>display)
	 * @param array $htmlOptions additional HTML attributes.
	 * @return string the generated list box
	 */
	public function listBox($parentModel, $attributedPath, $data, $htmlOptions = array())
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		return parent::listBox($model, $attribute, $data, $htmlOptions);
	}

	/**
	 * Renders a checkbox list for a model attribute.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute or path to related model attribute
	 * @param array $data value-label pairs used to generate the check box list.
	 * @param array $htmlOptions additional HTML options.
	 * @return string the generated check box list
	 */
	public function checkBoxList($parentModel, $attributedPath, $data, $htmlOptions = array())
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		return parent::checkBoxList($model, $attribute, $data, $htmlOptions);
	}

	/**
	 * Renders a radio button list for a model attribute.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute or path to related model attribute
	 * @param array $data value-label pairs used to generate the radio button list.
	 * @param array $htmlOptions additional HTML options.
	 * @return string the generated radio button list
	 */
	public function radioButtonList($parentModel, $attributedPath, $data, $htmlOptions = array())
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		return parent::radioButtonList($model, $attribute, $data, $htmlOptions);
	}

	/**
	 * Displays the first validation error for a model attribute.
	 * This is similar to {@link CHtml::error} except that it registers the model attribute
	 * so that if its value is changed by users, an AJAX validation may be triggered.
	 * @param CModel $parentModel the parent data model
	 * @param string $attributedPath the attribute name
	 * @param array $htmlOptions additional HTML attributes to be rendered in the container div tag.
	 * @param boolean $enableAjaxValidation whether to enable AJAX validation for the specified attribute.
	 * @param boolean $enableClientValidation whether to enable client-side validation for the specified attribute.
	 * @return string the validation result (error display or success message).
	 * @see CHtml::error
	 */
	public function error($parentModel, $attributedPath, $htmlOptions=array(), $enableAjaxValidation=true, $enableClientValidation=true)
	{
		list($model, $attribute, $htmlOptions) = self::resolveArgs($parentModel, $attributedPath, $htmlOptions);
		return parent::error($model, $attribute, $htmlOptions, $enableAjaxValidation, $enableClientValidation);
	}

	public static function resolveModel($parentModel, $attributedPath)
	{
		$model = $parentModel;
		$pathPortions = explode('.', $attributedPath);

		// last portion is always model attribute
		$attribute = array_pop($pathPortions);
		foreach ($pathPortions as $index => $portion) {
			// handle 'parent.statuses..id'
			if ($portion == '') {
				// portion becomes to new index
				$portion = is_array($model) ? count($model) : 0;
			}

			// handle 'parent.1.'
			if (is_numeric($portion)) {
				if (!empty($model) && !is_array($model)) {
					throw new Exception("Incorrect '..' or '.&lt;index&gt;.' usage");
				}

				$nextModel = isset($model[$portion]) ? $model[$portion]
						: self::createRelationModel($parentModel, $pathPortions[$index - 1]);
			}
				// handle 'parent.status' when status relation is empty (new model required)
			elseif (empty($model->{$portion})) {

				$nextModel = self::createRelationModel($model, $portion, true);
			}
				// handle 'parent.status'
			else {
				$nextModel = $model->{$portion};
			}

			// shift models
			$parentModel = $model;
			$model = $nextModel;
		}

		return $model;
	}

	public static function resolveName($parentModel, $attributedPath)
	{
		$name = get_class($parentModel);
		$pathPortions = explode('.', $attributedPath);
		foreach ($pathPortions as $key => $pathPortion) {
			if ($pathPortion === '')
				$pathPortion = '{index}';
			$name .= '[' . $pathPortion . ']';
		}
		return $name;
	}

	public static function resolveAttribute($attributedPath)
	{
		$pathPortions = explode('.', $attributedPath);
		return trim(end($pathPortions));
	}

	public static function resolveArgs($parentModel, $attributedPath, $htmlOptions = array())
	{
		$model = self::resolveModel($parentModel, $attributedPath);
		$attribute = self::resolveAttribute($attributedPath);
		if (empty($htmlOptions['name']))
			$htmlOptions['name'] = self::resolveName($parentModel, $attributedPath);

		return array($model, $attribute, $htmlOptions);
	}

	public static function resolveLabel($parentModel, $attributedPath)
	{
		$attribute = str_replace('..','.', $attributedPath);
		return $parentModel->getAttributeLabel($attribute);
	}

	protected static function createRelationModel($model, $relation, $allowMany = false)
	{
		$relations = $model->relations();
		if (!array_key_exists($relation, $relations))
			throw new Exception("Undefined relation " . $relation);

		$relationType = $relations[$relation][0];
		$relationModelClass = $relations[$relation][1];

		if ($allowMany && in_array($relationType, array(CActiveRecord::HAS_MANY, CActiveRecord::MANY_MANY))) {
			$model = array();
		} else {
			$model = new $relationModelClass();
		}

		return $model;
	}
}
