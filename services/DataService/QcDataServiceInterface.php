<?php

namespace app\services\DataService;

interface QcDataServiceInterface
{
    /**
     * 根据id获取一条数据
     * @param $id
     * @return \yii\db\ActiveRecord
     */
    public function findModel($id);

    /**
     * 根据条件获取一条数据
     * @param array $condition
     * @param array $params
     * @return array
     */
    public function findModelByCondition($condition, $params = []): array;

    /**
     * 根据条件获取数据列表
     * @param array $condition
     * @param array $params
     * @return array
     */
    public function findByCondition($condition, $params = []): array;

    /**
     * 根据条件获取数据条数
     * @param array $condition
     * @param array $params
     * @return int
     */
    public function getCountByCondition($condition, $params = []): int;

    /**
     * 根据sql获取数据列表
     * @param $sql
     * @param array $params
     * @return \yii\db\ActiveRecord[]
     */
    public function findBySql($sql, $params = []): array;

    /**
     * 根据sql获取一条数据
     * @param $sql
     * @param array $params
     * @return array|bool
     */
    public function findOneBySql($sql, $params = []);

    /**
     * 根据条件删除数据
     * @param array $condition
     * @param array $params
     * @return int
     */
    public function deleteByCondition(array $condition, $params = []): int;

    public function updateByCondition(array $condition, array $attributes);

    public function updateCounterByCondition($counter, $condition, $params = []): int;
}