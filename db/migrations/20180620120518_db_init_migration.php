<?php


use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class DbInitMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other distructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('organizations', ['signed' => false]);
        $table->addColumn('name', 'string')
            ->addIndex(['name'], ['unique' => true, 'name' => 'idx_organizations_name'])
            ->create();

        $table = $this->table('relations', ['id' => false]);
        $table->addColumn('organization_id', 'integer', ['signed' => false])
            ->addColumn('parent_id', 'integer', ['signed' => false])
            ->addForeignKey('organization_id', 'organizations', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addForeignKey('parent_id', 'organizations', 'id', ['delete'=> 'CASCADE', 'update'=> 'CASCADE'])
            ->addIndex(['parent_id', 'organization_id'], ['unique' => true, 'name' => 'idx_unique'])
            ->create();
    }
}
