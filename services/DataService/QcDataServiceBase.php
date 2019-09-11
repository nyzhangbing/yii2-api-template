<?php

namespace app\services\DataService;

abstract class QcDataServiceBase
{
    private $modelClass = '';

    public function __construct()
    {
        $this->modelClass = $this->getModelClass();
    }

    abstract protected function getModelClass();

    /**
     * 根据id获取一条数据
     * @param $id
     * @return \yii\db\ActiveRecord
     */
    public function findModel($id)
    {
        $modelClass = $this->modelClass;
        return $modelClass::findOne($id);
    }

    /**
     * 根据条件获取一条数据
     * @param array|string $condition
     * @param array $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findModelByCondition($condition, $params = []): array
    {
        $modelClass = $this->modelClass;
        return $modelClass::findOne($condition, $params);
    }

    /**
     * 根据条件获取数据列表
     * @param array|string $condition
     * @param array $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findByCondition($condition, $params = []): array
    {
        $modelClass = $this->modelClass;
        return $modelClass::find()
            ->where($condition, $params)
            ->all();
    }

    /**
     * 根据条件获取数据条数
     * @param array|string $condition
     * @param array $params
     * @return int
     */
    public function getCountByCondition($condition, $params = []): int
    {
        $modelClass = $this->modelClass;
        return $modelClass::find()
            ->where($condition, $params)
            ->count();
    }

    /**
     * 根据sql获取数据列表
     * @param string $sql
     * @param array $params
     * @return \yii\db\ActiveRecord[]
     */
    public function findBySql($sql, $params = []): array
    {
        $connection = \Yii::$app->db;
        $command = $connection->createCommand($sql, $params);
        return $command->queryAll();
    }

    /**
     * 根据sql获取一条数据
     * @param string $sql
     * @param array $params
     * @return array|bool
     * @throws \yii\db\ActiveRecord
     */
    public function findOneBySql($sql, $params = [])
    {
        $connection = \Yii::$app->db;
        $command = $connection->createCommand($sql, $params);
        return $command->queryOne();
    }

    /**
     * 根据条件删除数据
     * @param array $condition
     * @param array $params
     * @return int
     */
    public function deleteByCondition(array $condition, $params = []): int
    {
        $modelClass = $this->modelClass;
        return $modelClass::deleteAll($condition, $params);
    }

    public function updateByCondition(array $condition, array $attributes)
    {
        $modelClass = $this->modelClass;
        return $modelClass::updateAll($attributes, $condition);
    }

    public function updateCounterByCondition($counter, $condition, $params = []): int
    {
        $modelClass = $this->modelClass;
        return $modelClass::updateAllCounters($counter, $condition, $params);
    }
}