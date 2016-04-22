<?php
namespace appnets\giiz\model;
use Yii;
use yii\gii\CodeFile;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author chungdb
 * @since 1.0
 */
class Generator extends \yii\gii\generators\model\Generator
{

    /**
     * @var string The path of the base model.
     */
    public $baseModelPath;
    /**
     * @var string The base model class name.
     */
    public $baseModelClass;

    public function getName()
    {
        return 'Giiz Model Generator';
    }

    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            $className = $this->generateClassName($tableName);
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $className,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$className]) ? $relations[$className] : [],
            ];

            $this->baseModelPath = $this->ns . '/_base';
            $this->baseModelClass = 'Base' . $className;

            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . '.php',
                $this->render('model.php', $params)
            );

            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->baseModelPath)) . '/' . $this->baseModelClass . '.php',
                $this->render('_base/basemodel.php', $params)
            );
        }

        return $files;
    }

    public function formView()
    {
        return Yii::getAlias('@vendor/yiisoft/yii2-gii/generators/model/form.php');
    }

}
