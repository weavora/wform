<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class WFormRelationManyMany extends WFormRelationHasMany {

	public $type = CActiveRecord::HAS_MANY;

	public function save() {
		if ($this->mode == self::MODE_REPLACE) {
			foreach($this->getActualRelatedModels() as $model)
				$this->addToLazyDelete($model);
		}

		$relatedModels = $this->getRelatedModels();
		if (count($relatedModels) == 0 && $this->required)
			return false;

		$isSuccess = true;
		foreach ($relatedModels as $index => $relationModel) {
			$this->removeFromLazyDelete($relationModel);

			if ($relationModel->save()) {
				$isSuccess = $this->_linkTo($relationModel) && $isSuccess;
			} else {
				$isSuccess = false;
			}
		}

		return $isSuccess;
	}

	/**
	 * Insert link between parent and relation models into database
	 *
	 * @todo maybe we should execute bulk insert of links ? It faster a lot
	 * @param $relatedModel
	 * @return bool
	 */
	protected function _linkTo($relatedModel) {
		$foreignKey = $this->_parseForeignKey($this->info[WFormRelation::RELATION_FOREIGN_KEY]);

		try {
			$sql = "INSERT INTO {$foreignKey['table']} ({$foreignKey['model_fk']}, {$foreignKey['relation_fk']}) VALUES (:model_fk,:relation_fk)";

			$command = $this->model->getDbConnection()->createCommand($sql);
			$command->bindValues(array(
				":model_fk" => $this->model->primaryKey,
				":relation_fk" => $relatedModel->primaryKey,
			));
			$command->execute();
		} catch (Exception $e) {
			return false;
		}
		return true;
	}

	public function lazyDelete() {
		$relatedIds = array();
		foreach($this->_lazyDeleteRecords as $model) {
			$relatedIds[] = $model->primaryKey;
		}


		if (count($relatedIds))
			$this->_unlink($relatedIds);
	}

	public function delete() {
		if (!$this->cascadeDelete)
			return true;

		return $this->_unlink();
	}

	/**
	 * Remove all links between parent and relation models into database
	 *
	 * @return bool
	 */
	protected function _unlink($ids = null) {
		$foreignKey = $this->_parseForeignKey($this->info[WFormRelation::RELATION_FOREIGN_KEY]);

		try {

			$sql = "DELETE FROM {$foreignKey['table']} WHERE {$foreignKey['model_fk']} = :model_fk";
			if (!is_null($ids)) {
				$sql .= " AND {$foreignKey['relation_fk']} IN ('" . join("','", $ids) . "')";
			}

			$command = $this->model->getDbConnection()->createCommand($sql);
			$command->bindValues(array(
				":model_fk" => $this->model->primaryKey,
			));
			$command->execute();

		} catch (Exception $e) {
			return false;
		}

		return true;
	}

	/**
	 * Parse foreign key into table name, model FK and relation FK
	 *
	 * @param $key
	 * @return array
	 */
	protected function _parseForeignKey($key) {
		if (preg_match('/(?P<table>.*?)\((?P<model_fk>.*?),(?P<relation_fk>.*?)\)/is', $key, $matches))
		{
			return array(
				'table' => $this->model->getDbConnection()->quoteTableName(trim($matches['table'])),
				'model_fk' => $this->model->getDbConnection()->quoteColumnName(trim($matches['model_fk'])),
				'relation_fk' => $this->model->getDbConnection()->quoteColumnName(trim($matches['relation_fk'])),
			);
		}

		return null;
	}
}
